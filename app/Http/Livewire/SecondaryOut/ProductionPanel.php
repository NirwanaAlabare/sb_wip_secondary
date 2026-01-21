<?php

namespace App\Http\Livewire\SecondaryOut;

use Livewire\Component;
use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\DefectType;
use App\Models\SignalBit\DefectArea;
use App\Models\SignalBit\Reject;
use App\Models\SignalBit\Rework;
use App\Models\SignalBit\Undo;
use App\Models\Nds\Numbering;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class ProductionPanel extends Component
{
    public $date;

    // Data
    public $outputRft;
    public $outputDefect;
    public $outputReject;
    public $outputRework;

    // Panel views
    public $panels;
    public $rft;
    public $defect;
    public $reject;
    public $rework;

    // Input
    public $scannedNumberingCode;
    public $scannedNumberingInput;
    public $scannedSizeInput;
    public $scannedSizeInputText;

    // Selected Secondary
    public $selectedSecondary;
    public $selectedSecondaryText;

    public $inWip;
    public $outDefect;

    // Event listeners
    protected $listeners = [
        'toProductionPanel' => 'toProductionPanel',
        'toRft' => 'toRft',
        'toDefect' => 'toDefect',
        'toReject' => 'toReject',
        'toRework' => 'toRework',
        'countRft' => 'countRft',
        'countDefect' => 'countDefect',
        'countReject' => 'countReject',
        'countRework' => 'countRework',
        'updateSelectedSecondary' => 'updateSelectedSecondary',
    ];

    public function mount($selectedSecondary)
    {
        $this->panels = true;
        $this->rft = false;
        $this->defect = false;
        $this->reject = false;
        $this->rework = false;
        $this->outputRft = 0;
        $this->outputDefect = 0;
        $this->outputReject = 0;
        $this->outputRework = 0;
        $this->outputFiltered = 0;

        // Secondary
        $this->selectedSecondary = $selectedSecondary;
        $selectedSecondaryData = DB::table("output_secondary_master")->where("id", $this->selectedSecondary)->first();
        if ($this->selectedSecondary) {
            $this->selectedSecondaryText = $selectedSecondaryData->secondary;
        }

        $this->inWip = 0;
        $this->outDefect = 0;

        $this->date = date("Y-m-d");
    }

    public function toRft()
    {
        $this->panels = false;
        $this->rft = !($this->rft);
        $this->emit('toInputPanel', 'rft');
    }

    public function toDefect()
    {
        $this->panels = false;
        $this->defect = !($this->defect);
        $this->emit('toInputPanel', 'defect');
    }

    public function toReject()
    {
        $this->panels = false;
        $this->reject = !($this->reject);
        $this->emit('toInputPanel', 'reject');
    }

    public function toRework()
    {
        $this->panels = false;
        $this->rework = !($this->rework);
        $this->emit('toInputPanel', 'rework');
    }

    public function toProductionPanel()
    {
        $this->emit('fromInputPanel');
        $this->panels = true;
        $this->rft = false;
        $this->defect = false;
        $this->defectHistory = false;
        $this->reject = false;
        $this->rework = false;
    }

    public function setAndSubmitInput($type) {
        $this->emit('loadingStart');

        if ($this->scannedNumberingCode) {
            // One Straight Source
            $numberingData = DB::connection("mysql_nds")->table("year_sequence")->selectRaw("year_sequence.*, year_sequence.id_year_sequence no_cut_size")->where("id_year_sequence", $this->rapidRft[$i]['numberingInput'])->first();

            if ($numberingData) {
                $this->scannedSizeInput = $numberingData->so_det_id;
                $this->scannedSizeInputText = $numberingData->size;
                $this->scannedNumberingInput = $numberingData->no_cut_size;
            }
        }

        if ($type == "rft") {
            $this->toRft();
            $this->emit('setAndSubmitInputRft', $this->scannedNumberingInput, $this->scannedSizeInput, $this->scannedSizeInputText, $this->scannedNumberingCode);
        }

        if ($type == "defect") {
            $this->toDefect();
            $this->emit('setAndSubmitInputDefect', $this->scannedNumberingInput, $this->scannedSizeInput, $this->scannedSizeInputText, $this->scannedNumberingCode);
        }

        if ($type == "reject") {
            $this->toReject();
            $this->emit('setAndSubmitInputReject', $this->scannedNumberingInput, $this->scannedSizeInput, $this->scannedSizeInputText, $this->scannedNumberingCode);
        }

        if ($type == "rework") {
            $this->toRework();
            $this->emit('setAndSubmitInputRework', $this->scannedNumberingInput, $this->scannedSizeInput, $this->scannedSizeInputText, $this->scannedNumberingCode);
        }
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
        // Get total output
        $data = DB::connection('mysql_sb')->table('output_secondary_out')->selectRaw('output_secondary_out.*, output_secondary_in.secondary_id')->leftJoin('output_secondary_in', 'output_secondary_in.id', '=', 'output_secondary_out.secondary_in_id')->whereRaw("COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at) between '".$this->date." 00:00:00' and '".$this->date." 23:59:59'")->get();

        $this->inWip = DB::table("output_secondary_in")->leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->whereNull("output_secondary_out.id")->where("secondary_id", $this->selectedSecondary)->count();
        $this->outDefect = DB::table("output_secondary_out")->leftJoin("output_secondary_in", "output_secondary_in.id", "=", "output_secondary_out.secondary_in_id")->where("output_secondary_out.status", "defect")->where("secondary_id", $this->selectedSecondary)->count();

        $this->outputRft = $data->where('status', 'rft')->where('secondary_id', $this->selectedSecondary)->count();
        $this->outputDefect = $data->where('status', 'defect')->where('secondary_id', $this->selectedSecondary)->count();
        $this->outputReject = $data->where('status', 'reject')->where('secondary_id', $this->selectedSecondary)->count();
        $this->outputRework = $data->where('status', 'rework')->where('secondary_id', $this->selectedSecondary)->count();

        return view('livewire.secondary-out.production-panel');
    }

    public function dehydrate()
    {
        $this->resetValidation();
        $this->resetErrorBag();
    }
}
