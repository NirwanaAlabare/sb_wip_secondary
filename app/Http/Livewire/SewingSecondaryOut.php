<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SignalBit\UserPassword;
use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\DefectPacking;
use App\Models\SignalBit\OutputFinishing;
use App\Models\SignalBit\SewingSecondaryMaster;
use App\Models\SignalBit\SewingSecondaryIn as SewingSecondaryInModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;
use DB;

class SewingSecondaryOut extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $date;

    public $lines;
    public $orders;
    public $secondaryMaster;

    public $selectedSecondary;
    public $selectedSecondaryText;

    public $secondaryInOutFrom;
    public $secondaryInOutTo;

    public $mode;

    public $productTypeImage;
    public $defectPositionX;
    public $defectPositionY;

    public $loadingMasterPlan;

    public $baseUrl;

    public $listeners = [
        'setDate' => 'setDate',
        'hideDefectAreaImageClear' => 'hideDefectAreaImage'
    ];

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->mode = 'out';
        $this->lines = null;
        $this->orders = null;
        $this->secondaryMaster = null;

        $this->secondaryInOutFrom = date("Y-m-d", strtotime("-7 days"));
        $this->secondaryInOutTo = date("Y-m-d");

        $this->productTypeImage = null;
        $this->defectPositionX = null;
        $this->defectPositionY = null;

        $this->loadingMasterPlan = false;
        $this->baseUrl = url('/');

        $this->emit("qrInputFocus", "in");
    }

    public function changeMode($mode)
    {
        $this->mode = $mode;

        $this->emit('qrInputFocus', $mode);
    }

    // public function updatingsecondaryInSearch()
    // {
    //     $this->resetPage("secondaryInPage");
    // }

    // public function updatedPaginators($page, $pageName) {
    //     if ($this->secondaryInListAllChecked == true) {
    //         $this->selectAllsecondaryIn();
    //     }
    // }

    public function showDefectAreaImage($productTypeImage, $x, $y)
    {
        $this->productTypeImage = $productTypeImage;
        $this->defectPositionX = $x;
        $this->defectPositionY = $y;

        $this->emit('showDefectAreaImage', $this->productTypeImage, $this->defectPositionX, $this->defectPositionY);
    }

    public function hideDefectAreaImage()
    {
        $this->productTypeImage = null;
        $this->defectPositionX = null;
        $this->defectPositionY = null;
    }

    public function updatedSelectedSecondary()
    {
        $this->emit("qrInputFocus", $this->mode);
    }

    public function render()
    {
        $this->loadingMasterPlan = false;

        $this->lines = UserPassword::where("Groupp", "SEWING")->orderBy("line_id", "asc")->get();

        $this->secondaryMaster = SewingSecondaryMaster::get();

        // All Defect
        $secondaryInOutDaily = SewingSecondaryInModel::selectRaw("
                DATE(output_secondary_in.created_at) tanggal,
                COUNT(output_secondary_in.id) total_in,
                SUM(CASE WHEN output_secondary_out.status = 'rft' OR output_secondary_out.status = 'rework' THEN 1 ELSE 0 END) total_rft,
                SUM(CASE WHEN output_secondary_out.status = 'defect' THEN 1 ELSE 0 END) total_defect,
                SUM(CASE WHEN output_secondary_out.status = 'reject' THEN 1 ELSE 0 END) total_reject,
                SUM(CASE WHEN output_secondary_out.id IS NOT NULL THEN 1 ELSE 0 END) total_process
            ")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            whereBetween("output_secondary_in.created_at", [$this->secondaryInOutFrom." 00:00:00", $this->secondaryInOutTo." 23:59:59"])->
            where("output_secondary_master.id", $this->selectedSecondary)->
            groupByRaw("DATE(output_secondary_in.created_at)")->
            get();

        $secondaryInOutTotal = $secondaryInOutDaily ? $secondaryInOutDaily->sum("total_in") : 0;

        return view('livewire.sewing-secondary-out', [
            "totalSecondaryIn" => $secondaryInOutDaily ? $secondaryInOutDaily->sum("total_in") : 0,
            "totalSecondaryProcess" => $secondaryInOutDaily ? $secondaryInOutDaily->sum("total_process") : 0,
            "totalSecondaryOut" => $secondaryInOutDaily ? $secondaryInOutDaily->sum("total_out") : 0,
            "totalSecondaryInOut" => $secondaryInOutDaily->sum("total_in")
        ]);
    }

    public function refreshComponent()
    {
        $this->emit('$refresh');
    }
}
