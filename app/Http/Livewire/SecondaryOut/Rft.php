<?php

namespace App\Http\Livewire\SecondaryOut;

use Livewire\Component;
use Illuminate\Session\SessionManager;
use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\SewingSecondaryOut;
use App\Models\SignalBit\Rft as RftModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Str;

class Rft extends Component
{
    public $dateRft;

    public $sizeInput;
    public $sizeInputText;
    public $numberingCode;
    public $numberingInput;
    public $rapidRft;
    public $rapidRftCount;
    public $rft;

    public $worksheetRft;
    public $styleRft;
    public $colorRft;
    public $sizeRft;
    public $kodeRft;
    public $lineRft;

    public $selectedSecondary;
    public $selectedSecondaryText;

    protected $rules = [
        'sizeInput' => 'required',
        'noCutInput' => 'required',
        'numberingInput' => 'required',
    ];

    protected $messages = [
        'sizeInput.required' => 'Harap scan qr.',
        'noCutInput.required' => 'Harap scan qr.',
        'numberingInput.required' => 'Harap scan qr.',
    ];

    protected $listeners = [
        'setAndSubmitInputRft' => 'setAndSubmitInput',
        'toInputPanel' => 'resetError',
        'updateSelectedSecondary' => 'updateSelectedSecondary'
    ];

    public function mount(SessionManager $session, $selectedSecondary)
    {
        $this->dateRft = null;

        $this->sizeInput = null;
        $this->sizeInputText = null;
        $this->noCutInput = null;
        $this->numberingInput = null;
        $this->rapidRft = [];
        $this->rapidRftCount = 0;
        $this->submitting = false;

        $this->worksheetRft = null;
        $this->styleRft = null;
        $this->colorRft = null;
        $this->sizeRft = null;
        $this->kodeRft = null;
        $this->lineRft = null;

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

    private function checkIfNumberingExists($numberingInput = null): bool
    {
        $currentData = DB::table('output_secondary_out')->where('kode_numbering', ($numberingInput ?? $this->numberingInput))->first();
        if ($currentData) {
            $this->addError('numberingInput', 'Kode QR sudah discan di '.strtoupper($currentData->status).'.');

            return true;
        }

        return false;
    }

    public function clearInput()
    {
        $this->sizeInput = null;
    }

    public function submitInput($value)
    {
        $this->emit('qrInputFocus', 'rft');

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

                if (!$this->sizeInput) {
                    return $this->emit('alert', 'error', "QR belum terdaftar.");
                }

                $validatedData = $this->validate();

                if ($this->checkIfNumberingExists($numberingInput)) {
                    return;
                }

                // Check Secondary IN
                $secondaryInData = SewingSecondaryIn::where("kode_numbering", $numberingInput)->first();

                if ($secondaryInData) {
                    
                    // Stored Secondary IN Data
                    $scannedDetail = $secondaryInData->rft;

                    if ($secondaryInData->secondary_id == $this->selectedSecondary) {
                        $insertRft = SewingSecondaryOut::create([
                            'kode_numbering' => $secondaryInData->kode_numbering,
                            'secondary_in_id' => $secondaryInData->id,
                            'status' => 'rft',
                            'created_by' => Auth::user()->line_id,
                            'created_by_username' => Auth::user()->username,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                        if ($insertRft) {
                            $this->emit('alert', 'success', "1 output berukuran ".$this->sizeInputText." berhasil terekam.");

                            // Stored Secondary IN Data
                            $scannedDetail = $secondaryInData->rft;

                            if ($scannedDetail) {
                                $this->worksheetRft = $scannedDetail->soDet->so->actCosting->kpno;
                                $this->styleRft = $scannedDetail->soDet->so->actCosting->styleno;
                                $this->colorRft = $scannedDetail->soDet->color;
                                $this->sizeRft = $scannedDetail->soDet->size;
                                $this->kodeRft = $scannedDetail->kode_numbering;
                                $this->lineRft = $scannedDetail->userSbWip->userPassword->username;
                            }

                            // Clear
                            $this->sizeInput = '';
                            $this->sizeInputText = '';
                            $this->noCutInput = '';
                            $this->numberingInput = '';
                        } else {
                            $this->emit('alert', 'error', "Terjadi kesalahan. Output tidak berhasil direkam.");
                        }
                    } else {
                        $this->emit('alert', 'error', "Secondary IN tidak ditemukan di ".$this->selectedSecondaryText);
                    }

                } else {
                    $this->emit('alert', 'error', "Terjadi kesalahan. QR tidak ditemukan di Secondary IN.");
                }
            } else {
                $this->emit('alert', 'error', "QR tidak ditemukan.");
            }
        } else {
            $this->emit('alert', 'error', "QR tidak valid.");
        }
    }

    public function pushRapidRft($numberingInput, $sizeInput, $sizeInputText) {
        $exist = false;

        if (count($this->rapidRft) < 100) {
            foreach ($this->rapidRft as $item) {
                if (($numberingInput && $item['numberingInput'] == $numberingInput)) {
                    $exist = true;
                }
            }

            if (!$exist) {
                if ($numberingInput) {
                    $this->rapidRftCount += 1;

                    array_push($this->rapidRft, [
                        'numberingInput' => $numberingInput,
                    ]);
                }
            }
        } else {
            $this->emit('alert', 'error', "Anda sudah mencapai batas rapid scan. Harap klik selesai dahulu.");
        }
    }

    public function submitRapidInput() {
        $rapidRftFiltered = [];
        $success = 0;
        $fail = 0;

        if ($this->rapidRft && count($this->rapidRft) > 0) {
            for ($i = 0; $i < count($this->rapidRft); $i++) {
                // if (str_contains($this->rapidRft[$i]['numberingInput'], 'WIP')) {
                //     $numberingData = DB::connection("mysql_nds")->table("stocker_numbering")->where("kode", $this->rapidRft[$i]['numberingInput'])->first();
                // } else {
                //     $numberingCodes = explode('_', $this->rapidRft[$i]['numberingInput']);

                //     if (count($numberingCodes) > 2) {
                //         $this->rapidRft[$i]['numberingInput'] = substr($numberingCodes[0],0,4)."_".$numberingCodes[1]."_".$numberingCodes[2];
                //         $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->rapidRft[$i]['numberingInput'])->first();
                //     } else {
                //         $numberingData = DB::connection("mysql_nds")->table("month_count")->selectRaw("month_count.*, month_count.id_month_year no_cut_size")->where("id_month_year", $this->rapidRft[$i]['numberingInput'])->first();
                //     }
                // }

                // One Straight Source
                $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->rapidRft[$i]['numberingInput'])->first();

                // Check Secondary IN
                $secondaryInData = DB::connection('mysql_sb')->table('output_secondary_in')->where("kode_numbering", $this->rapidRft[$i]['numberingInput'])->first();

                if ($secondaryInData && (/*Check Secondary OUT*/(DB::connection("mysql_sb")->table("output_secondary_out")->where('kode_numbering', $this->rapidRft[$i]['numberingInput'])->count() < 1))) {
                    array_push($rapidRftFiltered, [
                        'kode_numbering' => $secondaryInData->kode_numbering,
                        'secondary_in_id' => $secondaryInData->id,
                        'status' => 'rft',
                        'created_by' => Auth::user()->line_id,
                        'created_by_username' => Auth::user()->username,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $success += 1;
                } else {
                    $fail += 1;
                }
            }
        }

        $rapidRftInsert = RftModel::insert($rapidRftFiltered);

        if ($success > 0) {
            $this->emit('alert', 'success', $success." output berhasil terekam. ");
        }

        if ($fail > 0) {
            $this->emit('alert', 'error', $fail." output gagal terekam. ");
        }

        $this->rapidRft = [];
        $this->rapidRftCount = 0;
    }

    public function setAndSubmitInput($scannedNumbering, $scannedSize, $scannedSizeText) {
        $this->numberingInput = $scannedNumbering;
        $this->sizeInput = $scannedSize;
        $this->sizeInputText = $scannedSizeText;

        $this->submitInput($scannedNumbering);
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

        return view('livewire.secondary-out.rft');
    }
}
