<?php

namespace App\Http\Livewire\SecondaryOut;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use App\Models\SignalBit\MasterPlan;
use App\Models\Nds\Numbering;
use App\Models\SignalBit\SecondaryOut;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\Rework as ReworkModel;
use Carbon\Carbon;
use DB;

class Rework extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $searchDefect;
    public $searchRework;

    // defect position
    public $defectImage;
    public $defectPositionX;
    public $defectPositionY;

    // defect list
    public $allDefectListFilter;
    public $allDefectImage;
    public $allDefectPosition;
    // public $allDefectList;

    // mass rework
    public $massQty;
    public $massSize;
    public $massDefectType;
    public $massDefectTypeName;
    public $massDefectArea;
    public $massDefectAreaName;
    public $massSelectedDefect;

    public $info;

    public $output;
    public $rework;
    public $sizeInput;
    public $sizeInputText;
    public $numberingInput;
    public $numberingCode;

    public $rapidRework;
    public $rapidReworkCount;

    protected $rules = [
        'sizeInput' => 'required',
        'noCutInput' => 'required',
        'numberingInput' => 'required',
    ];

    protected $messages = [
        'sizeInput.required' => 'Harap scan qr.',
        'noCutInput.required' => 'Harap scan qr.',
        'numberingInput.required' => 'Harap scan qr.'
    ];

    protected $listeners = [
        'submitRework' => 'submitRework',
        'submitAllRework' => 'submitAllRework',
        'cancelRework' => 'cancelRework',
        'hideDefectAreaImageClear' => 'hideDefectAreaImage',
        'updateWsDetailSizes' => 'updateWsDetailSizes',
        'updateOutputRework' => 'updateOutput',
        'setAndSubmitInputRework' => 'setAndSubmitInput',
        'toInputPanel' => 'resetError'
    ];

    private function checkIfNumberingExists(): bool
    {
        $currentData = DB::table('output_secondary_out')->where('kode_numbering', ($numberingInput ?? $this->numberingInput))->first();
        if ($currentData) {
            $this->addError('numberingInput', 'Kode QR sudah discan di '.strtoupper($currentData->status).'.');

            return true;
        }

        return false;
    }

    public function dehydrate()
    {
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function resetError() {
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updateWsDetailSizes($panel)
    {
        $this->sizeInput = null;
        $this->sizeInputText = null;
        $this->numberingInput = null;
        $this->numberingCode = null;

        if ($panel == 'rework') {
            $this->emit('qrInputFocus', 'rework');
        }
    }

    public function updateOutput()
    {
        $this->output = DB::
            connection('mysql_sb')->
            table('output_secondary_out')->
            where('status', 'rework')->
            count();

        $this->rework = DB::
            connection('mysql_sb')->
            table('output_secondary_out')->
            selectRaw('output_rfts.*, so_det.size')->
            leftJoin('output_secondary_in', 'output_secondary_in.id', '=', 'output_secondary_out.secondary_in_id')->
            leftJoin('output_rfts', 'output_rfts.id', '=', 'output_secondary_in.rft_id')->
            leftJoin('so_det', 'so_det.id', '=', 'output_rfts.so_det_id')->
            where('status', 'rework')->
            whereRaw("DATE(updated_at) = '".date('Y-m-d')."'")->
            get();
    }

    public function loadReworkPage()
    {
        $this->emit('loadReworkPageJs');
    }

    public function mount(SessionManager $session, $orderWsDetailSizes)
    {
        $this->orderWsDetailSizes = $orderWsDetailSizes;
        $session->put('orderWsDetailSizes', $orderWsDetailSizes);

        $this->massSize = '';

        $this->info = true;

        $this->output = 0;
        $this->sizeInput = null;
        $this->sizeInputText = null;
        $this->noCutInput = null;
        $this->numberingInput = null;

        $this->rapidRework = [];
        $this->rapidReworkCount = 0;
    }

    public function closeInfo()
    {
        $this->info = false;
    }

    public function setDefectAreaPosition($x, $y)
    {
        $this->defectPositionX = $x;
        $this->defectPositionY = $y;
    }

    public function showDefectAreaImage($defectImage, $x, $y)
    {
        $this->defectImage = $defectImage;
        $this->defectPositionX = $x;
        $this->defectPositionY = $y;

        $this->emit('showDefectAreaImage', $this->defectImage, $this->defectPositionX, $this->defectPositionY);
    }

    public function hideDefectAreaImage()
    {
        $this->defectImage = null;
        $this->defectPositionX = null;
        $this->defectPositionY = null;
    }

    public function updatingSearchDefect()
    {
        $this->resetPage('defectsPage');
    }

    public function updatingSearchRework()
    {
        $this->resetPage('reworksPage');
    }

    public function submitInput($value)
    {
        $this->emit('qrInputFocus', 'rework');

        $numberingInput = $value;

        if ($numberingInput) {
            // if (str_contains($numberingInput, 'WIP')) {
            //     $numberingData = DB::connection("mysql_nds")->table("stocker_numbering")->where("kode", $numberingInput)->first();
            // } else {
            //     $numberingCodes = explode('_', $numberingInput);

            //     if (count($numberingCodes) > 2) {
            //         $numberingInput = substr($numberingCodes[0],0,4)."_".$numberingCodes[1]."_".$numberingCodes[2];
            //         $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $numberingInput)->first();
            //     } else {
            //         $numberingData = DB::connection("mysql_nds")->table("month_count")->selectRaw("month_count.*, month_count.id_month_year no_cut_size")->where("id_month_year", $numberingInput)->first();
            //     }
            // }

            // One Straight Format
            $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $numberingInput)->first();

            if ($numberingData) {
                $this->sizeInput = $numberingData->so_det_id;
                $this->sizeInputText = $numberingData->size;
                $this->noCutInput = $numberingData->no_cut_size;
                $this->numberingInput = $numberingInput;
            }

            if (!$this->sizeInput) {
                return $this->emit('alert', 'error', "QR belum terdaftar.");
            }
        }

        $validatedData = $this->validate();

        if ($this->checkIfNumberingExists()) {
            return;
        }

        $scannedDefectData = SecondaryOutDefect::where("output_secondary_out.kode_numbering", $numberingInput)->first();

        if ($scannedDefectData) {
            $now = Carbon::now();

            // update defect detail
            $updateDefect = SecondaryOutDefect::where("id", $scannedDefectData->id)->update([
                "status" => "reworked",
                "reworked_by" => Auth::user()->line_id,
                "reworked_by_username" => Auth::user()->username,
                "reworked_at" => $now
            ]);

            // update defect
            $updateSecondaryOut = SecondaryOut::where("id", $scannedDefectData->secondary_out_id)->update([
                "status" => "reworked",
                "reworked_by" => Auth::user()->line_id,
                "reworked_by_username" => Auth::user()->username,
                "reworked_at" => $now
            ]);

            $secondaryInData = $scannedDefectData->secondaryOut->secondaryIn;

            $this->sizeInput = '';
            $this->sizeInputText = '';
            $this->noCutInput = '';
            $this->numberingInput = '';

            if ($updateDefect && $updateSecondaryOut) {
                $scannedDetail = $secondaryInData->rft;
                if ($scannedDetail) {
                    $this->worksheetReject = $scannedDetail->so_det->so->actCosting->kpno;
                    $this->styleReject = $scannedDetail->so_det->so->actCosting->styleno;
                    $this->colorReject = $scannedDetail->so_det->color;
                    $this->sizeReject = $scannedDetail->so_det->size;
                    $this->kodeReject = $scannedDetail->kode_numbering;
                    $this->lineReject = $scannedDetail->userLine->username;
                }

                $this->emit('alert', 'success', "DEFECT dengan ID : ".$scannedDefectData->kode_numbering." berhasil di REWORK.");

                // $this->emit('triggerDashboard', Auth::user()->line->username, Carbon::now()->format('Y-m-d'));
            } else {
                $this->emit('alert', 'error', "Terjadi kesalahan. DEFECT dengan ID : ".$scannedDefectData->kode_numbering." tidak berhasil di REWORK.");
            }
        }
    }

    public function setAndSubmitInput($scannedNumbering, $scannedSize, $scannedSizeText) {
        $this->numberingInput = $scannedNumbering;
        $this->sizeInput = $scannedSize;
        $this->sizeInputText = $scannedSizeText;

        $this->submitInput($scannedNumbering);
    }

    public function pushRapidRework($numberingInput, $sizeInput, $sizeInputText) {
        $exist = false;

        if (count($this->rapidRework) < 100) {
            foreach ($this->rapidRework as $item) {
                if (($numberingInput && $item['numberingInput'] == $numberingInput)) {
                    $exist = true;
                }
            }

            if (!$exist) {
                if ($numberingInput) {
                    $this->rapidReworkCount += 1;

                    array_push($this->rapidRework, [
                        'numberingInput' => $numberingInput,
                    ]);
                }
            }
        } else {
            $this->emit('alert', 'error', "Anda sudah mencapai batas rapid scan. Harap klik selesai dahulu.");
        }
    }

    public function submitRapidInput() {
        $defectIds = [];
        $rftData = [];
        $success = 0;
        $fail = 0;

        if ($this->rapidRework && count($this->rapidRework) > 0) {
            for ($i = 0; $i < count($this->rapidRework); $i++) {
                $scannedDefectData = DB::connection('mysql_sb')->
                    table('output_defects')->
                    selectRaw('output_defects.*, output_defects.master_plan_id, master_plan.sewing_line, output_defect_in_out.status in_out_status')->
                    leftJoin("output_defect_in_out", function ($join) {
                        $join->on("output_defect_in_out.defect_id", "=", "output_defects.id");
                        $join->on("output_defect_in_out.output_type", "=", DB::raw("'qc'"));
                    })->
                    leftJoin("master_plan", "master_plan.id", "=", "output_defects.master_plan_id")->
                    where("output_defects.defect_status", "defect")->
                    where("output_defects.kode_numbering", $this->rapidRework[$i]['numberingInput'])->
                    first();

                if (($scannedDefectData) && ($this->orderWsDetailSizes->where('so_det_id', $scannedDefectData->so_det_id)->count() > 0)) {
                    if ($scannedDefectData->master_plan_id == $this->orderInfo->id) {
                        if ($scannedDefectData->in_out_status != "defect") {
                            $createRework = ReworkModel::create([
                                'defect_id' => $scannedDefectData->id,
                                'status' => 'NORMAL',
                                "created_by" => Auth::user()->id
                            ]);

                            array_push($defectIds, $scannedDefectData->id);

                            array_push($rftData, [
                                'master_plan_id' => $this->orderInfo->id,
                                'so_det_id' => $scannedDefectData->so_det_id,
                                'no_cut_size' => $scannedDefectData->no_cut_size,
                                'kode_numbering' => $scannedDefectData->kode_numbering,
                                'rework_id' => $createRework->id,
                                'status' => 'REWORK',
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                "created_by" => Auth::user()->id
                            ]);

                            $success += 1;
                        }
                    } else {
                        $fail += 1;
                    }
                } else {
                    $fail += 1;
                }
            }
        }

        $rapidDefectUpdate = Defect::whereIn('id', $defectIds)->update(["defect_status" => "reworked"]);
        $rapidRftInsert = Rft::insert($rftData);

        if ($success > 0) {
            $this->emit('alert', 'success', $success." output berhasil terekam. ");

            // $this->emit('triggerDashboard', Auth::user()->line->username, Carbon::now()->format('Y-m-d'));
        }

        if ($fail > 0) {
            $this->emit('alert', 'error', $fail." output gagal terekam.");
        }

        $this->rapidRework = [];
        $this->rapidReworkCount = 0;
    }

    public function render(SessionManager $session)
    {
        // if (isset($this->errorBag->messages()['numberingInput']) && collect($this->errorBag->messages()['numberingInput'])->contains(function ($message) {return Str::contains($message, 'Kode QR sudah discan');})) {
        //     foreach ($this->errorBag->messages()['numberingInput'] as $message) {
        //         $this->emit('alert', 'warning', $message);
        //     }
        // } else if ((isset($this->errorBag->messages()['numberingInput']) && collect($this->errorBag->messages()['numberingInput'])->contains("Harap scan qr.")) || (isset($this->errorBag->messages()['sizeInput']) && collect($this->errorBag->messages()['sizeInput'])->contains("Harap scan qr."))) {
        //     $this->emit('alert', 'error', "Harap scan QR.");
        // }
        if (isset($this->errorBag->messages()['numberingInput'])) {
            foreach ($this->errorBag->messages()['numberingInput'] as $message) {
                $this->emit('alert', 'error', $message);
            }
        }

        $this->emit('loadReworkPageJs');

        $this->allDefectImage = MasterPlan::select('gambar')->find($this->orderInfo->id);

        $this->allDefectPosition = DB::connection('mysql_sb')->table('output_defects')->where('output_defects.defect_status', 'defect')->
            where('output_defects.master_plan_id', $this->orderInfo->id)->
            get();

        $allDefectList = DB::connection('mysql_sb')->table('output_defects')->selectRaw('output_defects.defect_type_id, output_defects.defect_area_id, output_defect_types.defect_type, output_defect_areas.defect_area, count(*) as total')->
            leftJoin('output_defect_areas', 'output_defect_areas.id', '=', 'output_defects.defect_area_id')->
            leftJoin('output_defect_types', 'output_defect_types.id', '=', 'output_defects.defect_type_id')->
            where('output_defects.defect_status', 'defect')->
            where('output_defects.master_plan_id', $this->orderInfo->id)->
            whereRaw("
                (
                    output_defect_types.defect_type LIKE '%".$this->allDefectListFilter."%' OR
                    output_defect_areas.defect_area LIKE '%".$this->allDefectListFilter."%'
                )
            ")->
            groupBy('output_defects.defect_type_id', 'output_defects.defect_area_id', 'output_defect_types.defect_type', 'output_defect_areas.defect_area')->
            orderBy('output_defects.updated_at', 'desc')->
            paginate(5, ['*'], 'allDefectListPage');

        $defects = Defect::selectRaw('output_defects.*, so_det.size as so_det_size, output_defect_types.defect_type, output_defect_areas.defect_area')->
            leftJoin('so_det', 'so_det.id', '=', 'output_defects.so_det_id')->
            leftJoin('output_defect_areas', 'output_defect_areas.id', '=', 'output_defects.defect_area_id')->
            leftJoin('output_defect_types', 'output_defect_types.id', '=', 'output_defects.defect_type_id')->
            where('output_defects.defect_status', 'defect')->
            where('output_defects.master_plan_id', $this->orderInfo->id)->
            whereRaw("(
                output_defects.id LIKE '%".$this->searchDefect."%' OR
                so_det.size LIKE '%".$this->searchDefect."%' OR
                output_defect_areas.defect_area LIKE '%".$this->searchDefect."%' OR
                output_defect_types.defect_type LIKE '%".$this->searchDefect."%' OR
                output_defects.defect_status LIKE '%".$this->searchDefect."%'
            )")->
            orderBy('output_defects.updated_at', 'desc')->paginate(10, ['*'], 'defectsPage');

        $reworks = ReworkModel::selectRaw('output_reworks.*, so_det.size as so_det_size, output_defect_types.defect_type, output_defect_areas.defect_area')->
            leftJoin('output_defects', 'output_defects.id', '=', 'output_reworks.defect_id')->
            leftJoin('output_defect_areas', 'output_defect_areas.id', '=', 'output_defects.defect_area_id')->
            leftJoin('output_defect_types', 'output_defect_types.id', '=', 'output_defects.defect_type_id')->
            leftJoin('so_det', 'so_det.id', '=', 'output_defects.so_det_id')->
            where('output_defects.defect_status', 'reworked')->
            where('output_defects.master_plan_id', $this->orderInfo->id)->
            whereRaw("(
                output_reworks.id LIKE '%".$this->searchRework."%' OR
                output_defects.id LIKE '%".$this->searchRework."%' OR
                so_det.size LIKE '%".$this->searchRework."%' OR
                output_defect_areas.defect_area LIKE '%".$this->searchRework."%' OR
                output_defect_types.defect_type LIKE '%".$this->searchRework."%' OR
                output_defects.defect_status LIKE '%".$this->searchRework."%'
            )")->
            orderBy('output_reworks.updated_at', 'desc')->paginate(10, ['*'], 'reworksPage');

        $this->massSelectedDefect = DB::connection('mysql_sb')->table('output_defects')->selectRaw('output_defects.so_det_id, so_det.size as size, count(*) as total')->
            leftJoin('so_det', 'so_det.id', '=', 'output_defects.so_det_id')->
            where('output_defects.defect_status', 'defect')->
            where('output_defects.master_plan_id', $this->orderInfo->id)->
            where('output_defects.defect_type_id', $this->massDefectType)->
            where('output_defects.defect_area_id', $this->massDefectArea)->
            groupBy('output_defects.so_det_id', 'so_det.size')->
            get();

        $this->output = DB::
            connection('mysql_sb')->
            table('output_defects')->
            where('master_plan_id', $this->orderInfo->id)->
            where('defect_status', 'reworked')->
            count();

        $this->rework = DB::
            connection('mysql_sb')->
            table('output_defects')->
            selectRaw('output_defects.*, so_det.size')->
            leftJoin('so_det', 'so_det.id', '=', 'output_defects.so_det_id')->
            where('master_plan_id', $this->orderInfo->id)->
            where('defect_status', 'reworked')->
            whereRaw("DATE(updated_at) = '".date('Y-m-d')."'")->
            get();

        return view('livewire.rework' , ['defects' => $defects, 'reworks' => $reworks, 'allDefectList' => $allDefectList]);
    }
}
