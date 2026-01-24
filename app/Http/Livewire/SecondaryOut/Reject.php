<?php

namespace App\Http\Livewire\SecondaryOut;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use App\Models\SignalBit\UserPassword;
use App\Models\SignalBit\Reject as RejectModel;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\DefectType;
use App\Models\SignalBit\DefectArea;
use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\SewingSecondaryOut;
use App\Models\SignalBit\SewingSecondaryOutDefect;
use App\Models\SignalBit\SewingSecondaryOutReject;
use App\Models\Nds\Numbering;
use Carbon\Carbon;
use Validator;
use DB;

class Reject extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $worksheetReject;
    public $styleReject;
    public $colorReject;
    public $sizeReject;
    public $kodeReject;
    public $lineReject;

    public $sizeInput;
    public $sizeInputText;
    public $noCutInput;
    public $numberingInput;

    public $rapidReject;
    public $rapidRejectCount;

    public $info;

    public $defectTypes;
    public $defectAreas;
    public $rejectType;
    public $rejectArea;
    public $rejectAreaPositionX;
    public $rejectAreaPositionY;

    public $selectedSecondary;
    public $selectedSecondaryText;

    protected $rules = [
        'sizeInput' => 'required',
        'noCutInput' => 'required',
        'numberingInput' => 'required',

        'rejectType' => 'required',
        'rejectArea' => 'required',
        'rejectAreaPositionX' => 'required',
        'rejectAreaPositionY' => 'required',
    ];

    protected $messages = [
        'sizeInput.required' => 'Harap scan qr.',
        'noCutInput.required' => 'Harap scan qr.',
        'numberingInput.required' => 'Harap scan qr.',

        'rejectType.required' => 'Harap tentukan jenis reject.',
        'rejectArea.required' => 'Harap tentukan area reject.',
        'rejectAreaPositionX.required' => "Harap tentukan posisi reject area dengan mengklik tombol 'gambar' di samping 'select product type'.",
        'rejectAreaPositionY.required' => "Harap tentukan posisi reject area dengan mengklik tombol 'gambar' di samping 'select product type'.",
    ];

    protected $listeners = [
        'setAndSubmitInputReject' => 'setAndSubmitInput',
        'toInputPanel' => 'resetError',

        'submitInputReject' => 'submitInput',
        'hideDefectAreaImageClear' => 'hideDefectAreaImage',

        'setRejectAreaPosition' => 'setRejectAreaPosition',
        'clearInput' => 'clearInput',
        'updateSelectedSecondary' => 'updateSelectedSecondary',
    ];

    private function checkIfNumberingExists($numberingInput = null): bool
    {
        $currentData = DB::table('output_secondary_out')->where('kode_numbering', ($numberingInput ?? $this->numberingInput))->where('status', '!=', 'defect')->first();
        if ($currentData) {
            $this->addError('numberingInput', 'Kode QR sudah discan di '.strtoupper($currentData->status).'.');

            return true;
        }

        return false;
    }

    public function mount(SessionManager $session, $selectedSecondary)
    {
        $this->sizeInput = null;

        $this->rapidReject = [];
        $this->rapidRejectCount = 0;

        $this->rejectType = null;
        $this->rejectArea = null;
        $this->rejectAreaPositionX = null;
        $this->rejectAreaPositionY = null;

        $this->worksheetReject = null;
        $this->styleReject = null;
        $this->colorReject = null;
        $this->sizeReject = null;
        $this->kodeReject = null;
        $this->lineReject = null;

        $this->selectedSecondary = $selectedSecondary;

        $selectedSecondaryData = DB::table("output_secondary_master")->where("id", $this->selectedSecondary)->first();

        if ($selectedSecondaryData) {
            $this->selectedSecondaryText = $selectedSecondaryData->secondary;
        }
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

    public function loadRejectPage()
    {
        $this->emit('loadRejectPageJs');
    }

    public function clearInput()
    {
        $this->sizeInput = null;
        $this->noCutInput = null;
        $this->numberingInput = null;
    }

    public function selectRejectAreaPosition()
    {
        $masterPlan = DB::connection('mysql_sb')->table('output_secondary_in')->
            select("master_plan.gambar")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            where("output_secondary_in.kode_numbering", $this->numberingInput)->
            first();

        if ($masterPlan) {
            $this->emit('showSelectRejectArea', $masterPlan->gambar);
        } else {
            $this->emit('alert', 'error', 'Harap pilih tipe produk terlebih dahulu');
        }
    }

    public function setRejectAreaPosition($x, $y)
    {
        $this->rejectAreaPositionX = $x;
        $this->rejectAreaPositionY = $y;
    }

    public function preSubmitInput($value)
    {
        $this->emit('qrInputFocus', 'reject');

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

            // One Straight Source
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

        // Get Secondary OUT Defect Detail
        $scannedDefectData = SewingSecondaryOutDefect::where("kode_numbering", $numberingInput)->first();

        // When it is Defect
        if ($scannedDefectData) {
            // When Secondary OUT Defect Detail is still Defect
            if ($scannedDefectData->status == "defect") {
                $this->rejectType = $scannedDefectData->defect_type_id;
                $this->rejectArea = $scannedDefectData->defect_area_id;
                $this->rejectAreaPositionX = $scannedDefectData->defect_area_x;
                $this->rejectAreaPositionY = $scannedDefectData->defect_area_y;

                $this->emit('loadingStart');

                $this->emitSelf('submitInputReject');
            } else {
                $this->emit('qrInputFocus', 'reject');

                $this->emit('alert', 'warning', "Kode qr sudah discan di REWORK.");
            }
        }
        // When it's not
        else {
            $validation = Validator::make([
                'sizeInput' => $this->sizeInput,
                'noCutInput' => $this->noCutInput,
                'numberingInput' => $numberingInput
            ], [
                'sizeInput' => 'required',
                'noCutInput' => 'required',
                'numberingInput' => 'required'
            ], [
                'sizeInput.required' => 'Harap scan qr.',
                'noCutInput.required' => 'Harap scan qr.',
                'numberingInput.required' => 'Harap scan qr.',
            ]);

            if ($this->checkIfNumberingExists($numberingInput)) {
                return;
            }

            if ($validation->fails()) {
                $this->emit('qrInputFocus', 'reject');

                $validation->validate();
            } else {
                // Get Secondary IN
                $secondaryInData = SewingSecondaryIn::where("kode_numbering", $numberingInput)->first();

                if ($secondaryInData) {
                    $this->emit('clearSelectRejectAreaPoint');

                    $this->rejectType = null;
                    $this->rejectArea = null;
                    $this->rejectAreaPositionX = null;
                    $this->rejectAreaPositionY = null;

                    $this->numberingInput = $numberingInput;

                    $this->validateOnly('sizeInput');

                    if ($secondaryInData->secondary_id == $this->selectedSecondary) {
                        // Get Detail
                        $scannedDetail = $secondaryInData->rft;
                        if ($scannedDetail) {
                            $this->worksheetReject = $scannedDetail->soDet->so->actCosting->kpno;
                            $this->styleReject = $scannedDetail->soDet->so->actCosting->styleno;
                            $this->colorReject = $scannedDetail->soDet->color;
                            $this->sizeReject = $scannedDetail->soDet->size;
                            $this->kodeReject = $scannedDetail->kode_numbering;
                            $this->lineReject = $scannedDetail->userSbWip->userPassword->username;
                        }
                    } else {
                        $this->emit('alert', 'error', "Secondary IN tidak ditemukan di ".$this->selectedSecondaryText);
                    }
                }
            }
        }
    }

    public function submitInput()
    {
        $this->emit('qrInputFocus', 'reject');

        if ($this->numberingInput) {
            // if (str_contains($this->numberingInput, 'WIP')) {
            //     $numberingData = DB::connection("mysql_nds")->table("stocker_numbering")->where("kode", $this->numberingInput)->first();
            // } else {
            //     $numberingCodes = explode('_', $this->numberingInput);

            //     if (count($numberingCodes) > 2) {
            //         $this->numberingInput = substr($numberingCodes[0],0,4)."_".$numberingCodes[1]."_".$numberingCodes[2];
            //         $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->numberingInput)->first();
            //     } else {
            //         $numberingData = DB::connection("mysql_nds")->table("month_count")->selectRaw("month_count.*, month_count.id_month_year no_cut_size")->where("id_month_year", $this->numberingInput)->first();
            //     }
            // }

            // One Straight Format
            $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->numberingInput)->first();

            if ($numberingData) {
                $this->sizeInput = $numberingData->so_det_id;
                $this->sizeInputText = $numberingData->size;
                $this->noCutInput = $numberingData->no_cut_size;
            }
        }

        $validatedData = $this->validate();

        if ($this->checkIfNumberingExists()) {
            return;
        }

        $continue = false;

        // Get Secondary OUT Defect Detail
        $scannedDefectData = SewingSecondaryOutDefect::where("kode_numbering", $this->numberingInput)->first();

        // When it is Defect
        if ($scannedDefectData) {
            // Update Secondary OUT Defect Detail
            if ($scannedDefectData->status == "defect") {
                $scannedDefectData->status = "rejected";
                $scannedDefectData->save();

                $this->rejectType = $scannedDefectData->defect_type_id;
                $this->rejectArea = $scannedDefectData->defect_area_id;
                $this->rejectAreaPositionX = $scannedDefectData->defect_area_x;
                $this->rejectAreaPositionY = $scannedDefectData->defect_area_y;

                $continue = true;
            } else {
                $continue = false;

                $this->emit('alert', 'error', "Data DEFECT status sudah : <b>'".$scannedDefectData->status."'</b>");
            }
        }
        // When it's not
        else {
            // Get Secondary IN Data
            $secondaryInData = DB::connection('mysql_sb')->table('output_secondary_in')->where("kode_numbering", $this->numberingInput)->first();

            if ($secondaryInData) {
                $continue = true;
            } else {
                $continue = false;

                $this->emit('alert', 'error', "Data tidak ditemukan di Secondary In");
            }
        }

        // continue
        if ($continue) {

            // Get Secondary OUT Data
            $secondaryOutData = null;
            if ($scannedDefectData) {
                // Update Secondary OUT Defect
                SewingSecondaryOut::where("kode_numbering", $this->numberingInput)->
                    update([
                        'status' => 'reject'
                    ]);

                // Get Secondary OUT Defect
                $secondaryOutData = SewingSecondaryOut::where("kode_numbering", $this->numberingInput)->first();
            } else {
                // Get Secondary IN
                $secondaryInData = DB::connection('mysql_sb')->table('output_secondary_in')->where("kode_numbering", $this->numberingInput)->first();

                if ($secondaryInData) {
                    // Create Secondary OUT Reject
                    $secondaryOutData = SewingSecondaryOut::create([
                        'kode_numbering' => $secondaryInData->kode_numbering,
                        'secondary_in_id' => $secondaryInData->id,
                        'status' => 'reject',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::user()->line_id,
                        'created_by_username' => Auth::user()->username
                    ]);
                } else {
                    $this->emit("alert", "error", "Secondary IN tidak ditemukan");
                }
            }

            if ($secondaryOutData) {
                // Create Secondary OUT Reject Detail
                $insertReject = SewingSecondaryOutReject::create([
                    "secondary_out_id" => $secondaryOutData ? $secondaryOutData->id : null,
                    'kode_numbering' => $this->numberingInput,
                    'defect_type_id' => $this->rejectType,
                    'defect_area_id' => $this->rejectArea,
                    'defect_area_x' => $this->rejectAreaPositionX,
                    'defect_area_y' => $this->rejectAreaPositionY,
                    'status' => $scannedDefectData ? 'defect' : 'mati',
                    'created_by' => Auth::user()->line_id,
                    'created_by_username' => Auth::user()->username,
                ]);

                if ($insertReject) {
                    $this->emit('alert', 'success', "1 output berukuran ".$this->sizeInputText." berhasil terekam.");
                    $this->emit('hideModal', 'reject', 'regular');

                    $this->sizeInput = '';
                    $this->sizeInputText = '';
                    $this->noCutInput = '';
                    $this->numberingInput = '';
                } else {
                    $this->emit('alert', 'error', "Terjadi kesalahan. Output tidak berhasil direkam.");
                }
            } else {
                $this->emit('alert', 'error', "Gagal Secondary Out.");
            }
        }

        $this->emit('qrInputFocus', 'reject');
    }

    public function setAndSubmitInput($scannedNumbering, $scannedSize, $scannedSizeText) {
        $this->numberingInput = $scannedNumbering;
        $this->sizeInput = $scannedSize;
        $this->sizeInputText = $scannedSizeText;

        $this->preSubmitInput($scannedNumbering);
    }

    public function pushRapidReject($numberingInput, $sizeInput, $sizeInputText) {
        $exist = false;

        if (count($this->rapidReject) < 100) {
            foreach ($this->rapidReject as $item) {
                if (($numberingInput && $item['numberingInput'] == $numberingInput)) {
                    $exist = true;
                }
            }

            if (!$exist) {
                if ($numberingInput) {
                    $this->rapidRejectCount += 1;

                    array_push($this->rapidReject, [
                        'numberingInput' => $numberingInput,
                    ]);
                }
            }
        } else {
            $this->emit('alert', 'error', "Anda sudah mencapai batas rapid scan. Harap klik selesai dahulu.");
        }
    }

    public function preSubmitRapidInput()
    {
        $this->rejectType = null;
        $this->rejectArea = null;
        $this->rejectAreaPositionX = null;
        $this->rejectAreaPositionY = null;

        $this->emit('showModal', 'reject', 'rapid');
    }

    public function submitRapidInput() {
        $rapidRejectFiltered = [];
        $success = 0;
        $fail = 0;

        if ($this->rapidReject && count($this->rapidReject) > 0) {
            for ($i = 0; $i < count($this->rapidReject); $i++) {
                // if (str_contains($this->rapidReject[$i]['numberingInput'], 'WIP')) {
                //     $numberingData = DB::connection("mysql_nds")->table("stocker_numbering")->where("kode", $this->rapidReject[$i]['numberingInput'])->first();
                // } else {
                //     $numberingCodes = explode('_', $this->rapidReject[$i]['numberingInput']);

                //     if (count($numberingCodes) > 1) {
                //         $this->rapidReject[$i]['numberingInput'] = substr($numberingCodes[0],0,4)."_".$numberingCodes[1]."_".$numberingCodes[2];
                //         $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->rapidReject[$i]['numberingInput'])->first();
                //     } else {
                //         $numberingData = DB::connection("mysql_nds")->table("month_count")->selectRaw("month_count.*, month_count.id_month_year no_cut_size")->where("id_month_year", $this->rapidReject[$i]['numberingInput'])->first();
                //     }
                // }

                // One Straight Source
                $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->rapidReject[$i]['numberingInput'])->first();

                if ((DB::connection("mysql_sb")->table("output_secondary_out")->where('kode_numbering', $this->rapidDefect[$i]['numberingInput'])->count() < 1)) {

                    // Get Secondary OUT Data
                    $secondaryOutData = null;
                    if ($scannedDefectData) {
                        // Update Secondary OUT
                        SewingSecondaryOut::where("kode_numbering", $this->rapidReject[$i]['numberingInput'])->
                            update([
                                'status' => 'reject'
                            ]);

                        // Get Secondary OUT
                        $secondaryOutData = SewingSecondaryOut::where("kode_numbering", $this->rapidReject[$i]['numberingInput']);
                    } else {
                        // Get Secondary IN
                        $secondaryInData = DB::connection('mysql_sb')->table('output_secondary_in')->where("kode_numbering")->first();

                        if ($secondaryInData) {
                            // Create Secondary OUT
                            $secondaryOutData = SewingSecondaryOut::create([
                                'kode_numbering' => $secondaryInData->kode_numbering,
                                'secondary_in_id' => $secondaryInData->id,
                                'status' => 'reject',
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'created_by' => Auth::user()->line_id,
                                'created_by_username' => Auth::user()->username
                            ]);
                        }
                    }

                    if ($secondaryOutData) {
                        array_push($rapidRejectFiltered, [
                            "secondary_out_id" => $secondaryOutData->id,
                            'kode_numbering' => $this->rapidReject[$i]['numberingInput'],
                            'defect_type_id' => $this->rejectType,
                            'defect_area_id' => $this->rejectArea,
                            'defect_area_x' => $this->rejectAreaPositionX,
                            'defect_area_y' => $this->rejectAreaPositionY,
                            'status' => $scannedDefectData ? 'defect' : 'mati',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'created_by' => Auth::user()->line_id,
                            'created_by_username' => Auth::user()->username,
                        ]);

                        $success += 1;
                    } else {
                        $fail += 1;
                    }
                } else {
                    $fail += 1;
                }
            }
        }

        // Create Mass Secondary OUT Reject Detail
        $rapidRejectInsert = RejectModel::insert($rapidRejectFiltered);

        if ($success > 0) {
            $this->emit('alert', 'success', $success." output berhasil terekam. ");
        }

        if ($fail > 0) {
            $this->emit('alert', 'error', $fail." output gagal terekam.");
        }

        $this->rapidReject = [];
        $this->rapidRejectCount = 0;
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

    public function updateSelectedSecondary($selectedSecondary) {
        $this->selectedSecondary = $selectedSecondary;

        $selectedSecondaryData = DB::table("output_secondary_master")->where("id", $this->selectedSecondary)->first();

        if ($selectedSecondaryData) {
            $this->selectedSecondaryText = $selectedSecondaryData->secondary;
        }
    }

    public function render(SessionManager $session)
    {
        $this->emit('loadRejectPageJs');

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

        // Defect types
        $this->defectTypes = DB::table("output_defect_types")->leftJoin(DB::raw("(select reject_type_id, count(id) total_reject from output_rejects where updated_at between '".date("Y-m-d", strtotime(date("Y-m-d").' -10 days'))." 00:00:00' and '".date("Y-m-d")." 23:59:59' group by reject_type_id) as rejects"), "rejects.reject_type_id", "=", "output_defect_types.id")->whereRaw("(hidden IS NULL OR hidden != 'Y')")->orderBy('defect_type')->get();

        // Defect areas
        $this->defectAreas = DB::table("output_defect_areas")->leftJoin(DB::raw("(select reject_area_id, count(id) total_reject from output_rejects where updated_at between '".date("Y-m-d", strtotime(date("Y-m-d").' -10 days'))." 00:00:00' and '".date("Y-m-d")." 23:59:59' group by reject_area_id) as rejects"), "rejects.reject_area_id", "=", "output_defect_areas.id")->whereRaw("(hidden IS NULL OR hidden != 'Y')")->orderBy('defect_area')->get();

        return view('livewire.secondary-out.reject');
    }
}
