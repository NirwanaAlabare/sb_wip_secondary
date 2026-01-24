<?php

namespace App\Http\Livewire\SecondaryOut;

use Livewire\Component;
use Illuminate\Session\SessionManager;
use App\Models\SignalBit\UserPassword;
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

    public $lines;
    public $orders;

    public $outputInput;

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

        $this->lines = null;
        $this->orders = null;

        $this->outputInput = 0;

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

    public function outputIncrement()
    {
        $this->outputInput++;
    }

    public function outputDecrement()
    {
        if (($this->outputInput-1) < 1) {
            $this->emit('alert', 'warning', "Kuantitas output tidak bisa kurang dari 1.");
        } else {
            $this->outputInput--;
        }
    }

    public function clearInput()
    {
        $this->sizeInput = null;
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
        $this->lines = UserPassword::where("Groupp", "SEWING")->orderBy("line_id", "asc")->get();

        $this->orders = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('
                id as id_ws,
                kpno as no_ws,
                styleno as style
            ')->
            where('status', '!=', 'CANCEL')->
            where('cost_date', '>=', '2023-01-01')->
            where('type_ws', 'STD')->
            orderBy('cost_date', 'desc')->
            orderBy('kpno', 'asc')->
            groupBy('kpno')->
            get();

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
