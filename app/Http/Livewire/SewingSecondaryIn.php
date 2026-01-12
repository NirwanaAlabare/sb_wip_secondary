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

class SewingSecondaryIn extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $date;

    public $lines;
    public $orders;
    public $secondaryMaster;

    public $secondaryInShowPage;
    public $secondaryInDate;
    public $secondaryInLine;
    public $secondaryInQty;

    public $selectedSecondary;
    public $selectedSecondaryText;

    public $secondaryInOutFrom;
    public $secondaryInOutTo;

    // Filter
    public $secondaryInFilterKode;
    public $secondaryInFilterWaktu;
    public $secondaryInFilterLine;
    public $secondaryInFilterMasterPlan;
    public $secondaryInFilterSize;
    public $secondaryInFilterType;

    public $secondaryInMasterPlanOutput;

    public $secondaryInFrom;
    public $secondaryInTo;
    public $secondaryInSearch;

    public $scannedSecondaryIn;

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
        $this->mode = 'in';
        $this->lines = null;
        $this->orders = null;
        $this->secondaryMaster = null;

        // Defect In init value
        $this->secondaryInList = null;
        $this->secondaryInShowPage = 10;
        $this->secondaryInDate = date('Y-m-d');
        $this->secondaryInLine = null;
        $this->secondaryInMasterPlan = null;
        $this->secondaryInMasterPlanOutput = null;
        $this->secondaryInSearch = null;
        $this->secondaryInListAllChecked = null;

        $this->secondaryInFilterKode = null;
        $this->secondaryInFilterWaktu = null;
        $this->secondaryInFilterLine = null;
        $this->secondaryInFilterMasterPlan = null;
        $this->secondaryInFilterSize = null;
        $this->secondaryInFilterType = null;

        $this->scannedSecondaryIn = null;

        $this->secondaryInOutFrom = date("Y-m-d", strtotime("-7 days"));
        $this->secondaryInOutTo = date("Y-m-d");

        $this->secondaryInShowPage = 10;
        $this->secondaryInFrom = date("Y-m-d", strtotime("-7 days"));
        $this->secondaryInTo = date("Y-m-d");

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

    public function submitsecondaryIn()
    {
        if ($this->scannedSecondaryIn) {
            $scannedOutput = Rft::selectRaw("
                    output_rfts.id,
                    output_rfts.kode_numbering,
                    output_rfts.so_det_id,
                    output_defect_types.defect_type,
                    act_costing.kpno ws,
                    act_costing.styleno style,
                    so_det.color,
                    so_det.size,
                    userpassword.username as sewing_line,
                    output_secondary_in.id secondary_in_id,
                    'qc' output_type
                ")->
                leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
                leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
                leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
                leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
                leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
                leftJoin("output_secondary_in", "output_secondary_in.rft_id", "=", "output_rfts.id")->
                whereNotNull("output_rfts.id")->
                where("output_rfts.kode_numbering", $this->scannedSecondaryIn)->
                first();

            if ($scannedOutput) {
                $secondaryIn = SewingSecondaryInModel::where("rft_id", $scannedOutput->id)->first();

                if (!$secondaryIn) {
                    $createsecondaryIn = SewingSecondaryInModel::create([
                        "kode_numbering" => $scannedOutput->kode_numbering,
                        "rft_id" => $scannedOutput->id,
                        "sewing_secondary_id" => $selectedSecondary,
                        "created_by" => Auth::user()->id,
                        "created_by_username" => Auth::user()->username,
                        "output_type" => $scannedOutput->output_type,
                    ]);

                    if ($createsecondaryIn) {
                        $this->emit('alert', 'success', "DEFECT '".$scannedOutput->defect_type."' dengan KODE '".$this->scannedSecondaryIn."' berhasil masuk ke 'SECONDARY'");
                    } else {
                        $this->emit('alert', 'error', "Terjadi kesalahan.");
                    }
                } else {
                    $this->emit('alert', 'warning', "QR sudah discan.");
                }
            } else {
                $this->emit('alert', 'error', "Defect dengan QR '".$this->scannedSecondaryIn."' tidak ditemukan di 'SECONDARY'.");
            }
        } else {
            $this->emit('alert', 'error', "QR tidak sesuai.");
        }

        $this->scannedSecondaryIn = null;
        $this->emit('qrInputFocus', $this->mode);
    }

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

        return view('livewire.sewing-secondary-in', [
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
