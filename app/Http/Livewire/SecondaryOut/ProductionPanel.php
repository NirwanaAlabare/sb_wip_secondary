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
    // Data
    public $outputRft;
    public $outputDefect;
    public $outputReject;
    public $outputRework;
    public $outputFiltered;

    // Panel views
    public $panels;
    public $rft;
    public $defect;
    public $defectHistory;
    public $reject;
    public $rework;

    // Undo
    public $undoSizes;
    public $undoType;
    public $undoQty;
    public $undoSize;
    public $undoDefectType;
    public $undoDefectArea;

    // Input
    public $scannedNumberingCode;
    public $scannedNumberingInput;
    public $scannedSizeInput;
    public $scannedSizeInputText;

    // Rules
    protected $rules = [
        'undoType' => 'required',
        'undoQty' => 'required|numeric|min:1',
        'undoSize' => 'required',
    ];

    protected $messages = [
        'undoType.required' => 'Terjadi kesalahan, tipe undo output tidak terbaca.',
        'undoQty.required' => 'Harap tentukan kuantitas undo output.',
        'undoQty.numeric' => 'Harap isi kuantitas undo output dengan angka.',
        'undoQty.min' => 'Kuantitas undo output tidak bisa kurang dari 1.',
        'undoSize.required' => 'Harap tentukan ukuran undo output.',
    ];

    // Event listeners
    protected $listeners = [
        'toProductionPanel' => 'toProductionPanel',
        'toRft' => 'toRft',
        'toDefect' => 'toDefect',
        'toDefectHistory' => 'toDefectHistory',
        'toReject' => 'toReject',
        'toRework' => 'toRework',
        'countRft' => 'countRft',
        'countDefect' => 'countDefect',
        'countReject' => 'countReject',
        'countRework' => 'countRework',
        'preSubmitUndo' => 'preSubmitUndo',
    ];

    public function mount()
    {
        $this->panels = true;
        $this->rft = false;
        $this->defect = false;
        $this->defectHistory = false;
        $this->reject = false;
        $this->rework = false;
        $this->outputRft = 0;
        $this->outputDefect = 0;
        $this->outputReject = 0;
        $this->outputRework = 0;
        $this->outputFiltered = 0;
        $this->undoType = "";
        $this->undoQty = 1;
        $this->undoSize = "";
        $this->undoDefectType = "";
        $this->undoDefectArea = "";
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

    public function toDefectHistory()
    {
        $this->panels = false;
        $this->defectHistory = !($this->defectHistory);
        $this->emit('toInputPanel', 'defect-history');
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

    public function preSubmitUndo($undoType)
    {
        $this->undoQty = 1;
        $this->undoSize = '';
        $this->undoType = $undoType;

        $this->emit('showModal', 'undo');
    }

    public function deleteRedundant() {
        $redundantData = DB::select(DB::raw(
            "select defect_id, jml from (select defect_id, COUNT(defect_id) jml from (SELECT a.* from output_reworks a inner join output_defects c on c.id = a.defect_id inner join master_plan b on b.id = c.master_plan_id where b.sewing_line = '".Auth::user()->line->username."' and DATE_FORMAT(a.created_at, '%Y-%m-%d') = CURRENT_DATE() order by a.defect_id asc) a GROUP BY a.defect_id) a where a.jml > 1"
        ));

        foreach ($redundantData as $redundant) {
            $reworkData = Rework::where('defect_id', $redundant->defect_id)->limit(1)->first();
            Rework::where('id', $reworkData->id)->limit(1)->delete();
            Rft::where('rework_id', $reworkData->id)->limit(1)->delete();
        }

        $this->emit('alert', 'success', 'Redundant deleted');
    }

    public function setAndSubmitInput($type) {
        $this->emit('loadingStart');

        if ($this->scannedNumberingCode) {
            if (str_contains($this->scannedNumberingCode, 'WIP')) {
                $numberingData = DB::connection("mysql_nds")->table("stocker_numbering")->where("kode", $this->scannedNumberingCode)->first();
            } else {
                $numberingData = DB::connection("mysql_nds")->table("month_count")->selectRaw("month_count.*, month_count.id_month_year no_cut_size")->where("id_month_year", $this->scannedNumberingCode)->first();
            }

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

    public function render(SessionManager $session)
    {
        // Get total output
        $this->outputRft = DB::connection('mysql_sb')->table('output_rfts')->
            where('master_plan_id', "-")->
            where('status', 'NORMAL')->
            count();
        $this->outputDefect = DB::connection('mysql_sb')->table('output_defects')->
            where('master_plan_id', "-")->
            where('defect_status', 'defect')->
            count();
        $this->outputReject = DB::connection('mysql_sb')->table('output_rejects')->
            where('master_plan_id', "-")->
            count();
        $this->outputRework = DB::connection('mysql_sb')->table('output_defects')->
            where('master_plan_id', "-")->
            where('defect_status', 'reworked')->
            count();

        // Defect
        $undoDefectTypes = DefectType::all();
        $undoDefectAreas = DefectArea::all();

        return view('livewire.secondary-out.production-panel', ['undoDefectTypes' => $undoDefectTypes, 'undoDefectAreas' => $undoDefectAreas]);
    }

    public function dehydrate()
    {
        $this->resetValidation();
        $this->resetErrorBag();
    }
}
