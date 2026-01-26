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

    public $rft;

    public $worksheetRft;
    public $styleRft;
    public $colorRft;
    public $sizeRft;
    public $lineRft;

    public $selectedSecondary;
    public $selectedSecondaryText;

    protected $listeners = [
        'toInputPanel' => 'resetError',
        'updateSelectedSecondary' => 'updateSelectedSecondary'
    ];

    public function mount(SessionManager $session, $selectedSecondary)
    {
        $this->dateRft = null;

        $this->lines = null;
        $this->orders = null;

        $this->outputInput = 0;

        $this->worksheetRft = null;
        $this->styleRft = null;
        $this->colorRft = null;
        $this->sizeRft = null;
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

        return view('livewire.secondary-out.rft');
    }
}
