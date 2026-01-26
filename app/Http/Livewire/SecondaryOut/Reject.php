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

    public $lines;
    public $orders;

    public $outputInput;

    protected $paginationTheme = 'bootstrap';

    public $worksheetReject;
    public $styleReject;
    public $colorReject;
    public $sizeReject;
    public $lineReject;

    public $info;

    public $defectTypes;
    public $defectAreas;
    public $rejectType;
    public $rejectArea;
    public $rejectAreaPositionX;
    public $rejectAreaPositionY;
    public $rejectImage;

    public $selectedSecondary;
    public $selectedSecondaryText;

    protected $rules = [
        'rejectType' => 'required',
        'rejectArea' => 'required',
        'rejectAreaPositionX' => 'required',
        'rejectAreaPositionY' => 'required',
    ];

    protected $messages = [
        'rejectType.required' => 'Harap tentukan jenis reject.',
        'rejectArea.required' => 'Harap tentukan area reject.',
        'rejectAreaPositionX.required' => "Harap tentukan posisi reject area dengan mengklik tombol 'gambar' di samping 'select product type'.",
        'rejectAreaPositionY.required' => "Harap tentukan posisi reject area dengan mengklik tombol 'gambar' di samping 'select product type'.",
    ];

    protected $listeners = [
        'toInputPanel' => 'resetError',
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
        $this->lines = null;
        $this->orders = null;

        $this->outputInput = 0;

        $this->sizeInput = null;

        $this->rapidReject = [];
        $this->rapidRejectCount = 0;

        $this->rejectType = null;
        $this->rejectArea = null;
        $this->rejectAreaPositionX = null;
        $this->rejectAreaPositionY = null;
        $this->rejectImage = null;

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
            where("master_plan.id_ws", $this->worksheetReject)->
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

    public function closeInfo()
    {
        $this->info = false;
    }

    public function setDefectAreaPosition($x, $y)
    {
        $this->rejectAreaPositionX = $x;
        $this->rejectAreaPositionY = $y;
    }

    public function showDefectAreaImage($rejectImage, $x, $y)
    {
        $this->rejectImage = $rejectImage;
        $this->rejectAreaPositionX = $x;
        $this->rejectAreaPositionY = $y;

        $this->emit('showDefectAreaImage', $this->rejectImage, $this->rejectAreaPositionX, $this->rejectAreaPositionY);
    }

    public function hideDefectAreaImage()
    {
        $this->rejectImage = null;
        $this->rejectAreaPositionX = null;
        $this->rejectAreaPositionY = null;
    }

    public function updateSelectedSecondary($selectedSecondary) {
        $this->selectedSecondary = $selectedSecondary;

        $selectedSecondaryData = DB::table("output_secondary_master")->where("id", $this->selectedSecondary)->first();

        if ($selectedSecondaryData) {
            $this->selectedSecondaryText = $selectedSecondaryData->secondary;
        }
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

    public function render(SessionManager $session)
    {
        $this->emit('loadRejectPageJs');

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

        // Defect types
        $this->defectTypes = DB::table("output_defect_types")->leftJoin(DB::raw("(select reject_type_id, count(id) total_reject from output_rejects where updated_at between '".date("Y-m-d", strtotime(date("Y-m-d").' -10 days'))." 00:00:00' and '".date("Y-m-d")." 23:59:59' group by reject_type_id) as rejects"), "rejects.reject_type_id", "=", "output_defect_types.id")->whereRaw("(hidden IS NULL OR hidden != 'Y')")->orderBy('defect_type')->get();

        // Defect areas
        $this->defectAreas = DB::table("output_defect_areas")->leftJoin(DB::raw("(select reject_area_id, count(id) total_reject from output_rejects where updated_at between '".date("Y-m-d", strtotime(date("Y-m-d").' -10 days'))." 00:00:00' and '".date("Y-m-d")." 23:59:59' group by reject_area_id) as rejects"), "rejects.reject_area_id", "=", "output_defect_areas.id")->whereRaw("(hidden IS NULL OR hidden != 'Y')")->orderBy('defect_area')->get();

        return view('livewire.secondary-out.reject');
    }
}
