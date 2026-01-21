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
use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\SewingSecondaryOut;
use App\Models\SignalBit\SewingSecondaryOutDefect;
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

    public $info;

    public $output;
    public $rework;
    public $sizeInput;
    public $sizeInputText;
    public $numberingInput;
    public $numberingCode;

    public $rapidRework;
    public $rapidReworkCount;

    public $selectedSecondary;

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
        'hideDefectAreaImageClear' => 'hideDefectAreaImage',
        'setAndSubmitInputRework' => 'setAndSubmitInput',
        'toInputPanel' => 'resetError',
        'updateSelectedSecondary' => 'updateSelectedSecondary'
    ];

    private function checkIfNumberingExists($numberingInput = null): bool
    {
        $currentData = DB::table('output_secondary_out')->where('kode_numbering', ($numberingInput ?? $this->numberingInput))->where("status", "!=", "defect")->first();
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

    public function loadReworkPage()
    {
        $this->emit('loadReworkPageJs');
    }

    public function mount(SessionManager $session, $selectedSecondary)
    {
        $this->massSize = '';

        $this->info = true;

        $this->output = 0;
        $this->sizeInput = null;
        $this->sizeInputText = null;
        $this->noCutInput = null;
        $this->numberingInput = null;

        $this->rapidRework = [];
        $this->rapidReworkCount = 0;

        $this->selectedSecondary = $selectedSecondary;
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

        $validatedData = $this->validate();

        if ($this->checkIfNumberingExists()) {
            return;
        }

        // Get Secondary Out Defect Detail
        $scannedDefectData = SewingSecondaryOutDefect::where("kode_numbering", $numberingInput)->first();

        if ($scannedDefectData) {
            $now = Carbon::now();

            // Get Secondary Out
            $secondaryInData = $scannedDefectData->secondaryOut->secondaryIn;

            if ($secondaryInData) {

                if ($secondaryInData->secondary_id == $this->selectedSecondary) {

                    // Update Secondary Out Defect Detail
                    $updateDefect = SewingSecondaryOutDefect::where("id", $scannedDefectData->id)->update([
                        "status" => "reworked",
                        "reworked_by" => Auth::user()->line_id,
                        "reworked_by_username" => Auth::user()->username,
                        "reworked_at" => $now
                    ]);

                    // Update Secondary Out Defect
                    $updateSecondaryOut = SewingSecondaryOut::where("id", $scannedDefectData->secondary_out_id)->update([
                        "status" => "rework",
                    ]);

                } else {
                    $this->emit('alert', 'error', "Secondary IN tidak ditemukan di ".$this->selectedSecondary);
                }

            }

            $this->sizeInput = '';
            $this->sizeInputText = '';
            $this->noCutInput = '';
            $this->numberingInput = '';

            if ($updateDefect && $updateSecondaryOut && $secondaryInData) {
                $scannedDetail = $secondaryInData->rft;
                if ($scannedDetail) {
                    $this->worksheetRework = $scannedDetail->soDet->so->actCosting->kpno;
                    $this->styleRework = $scannedDetail->soDet->so->actCosting->styleno;
                    $this->colorRework = $scannedDetail->soDet->color;
                    $this->sizeRework = $scannedDetail->soDet->size;
                    $this->kodeRework = $scannedDetail->kode_numbering;
                    $this->lineRework = $scannedDetail->userSbWip->userPassword->username;
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
                // Get Secondary Out Defect Detail
                $scannedDefectData = DB::connection('mysql_sb')->
                    table('output_secondary_out_defect')->
                    selectRaw('output_secondary_out_defect.*')->
                    leftJoin("output_secondary_out", "output_secondary_out.id", "=", "output_secondary_out_defect.secondary_out_id")->
                    where("output_secondary_out_defect.status", "defect")->
                    where("output_secondary_out.kode_numbering", $this->rapidRework[$i]['numberingInput'])->
                    first();

                if ($scannedDefectData) {
                    $now = Carbon::now();

                    // Update Secondary Out Defect Detail
                    $updateDefect = SewingSecondaryOutDefect::where("id", $scannedDefectData->id)->update([
                        'status' => 'reworked',
                        "reworked_by" => Auth::user()->id,
                        "reworked_by_username" => Auth::user()->username,
                        "reworked_at" => $now
                    ]);

                    // Update Secondary Out Defect
                    $updateSecondaryOut = SewingSecondaryOut::where("id", $scannedDefectData->secondary_out_id)->update([
                        'status' => 'rework',
                    ]);

                    $success += 1;
                } else {
                    $fail += 1;
                }
            }
        }

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

    public function updateSelectedSecondary($selectedSecondary) {
        $this->selectedSecondary = $selectedSecondary;
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

        return view('livewire.secondary-out.rework');
    }
}
