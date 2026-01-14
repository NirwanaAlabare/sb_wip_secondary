<?php

namespace App\Http\Controllers;

use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\DefectPacking;
use App\Models\SignalBit\OutputFinishing;
use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\SewingSecondaryMaster;
use App\Exports\SecondaryInOutExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use DB;

class SecondaryOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index($id)
    // {
    //     $orderInfo = MasterPlan::selectRaw("
    //             master_plan.id as id,
    //             master_plan.tgl_plan as tgl_plan,
    //             REPLACE(master_plan.sewing_line, '_', ' ') as sewing_line,
    //             act_costing.kpno as ws_number,
    //             act_costing.styleno as style_name,
    //             mastersupplier.supplier as buyer_name,
    //             so_det.styleno_prod as reff_number,
    //             master_plan.color as color,
    //             so_det.size as size,
    //             so.qty as qty_order,
    //             CONCAT(masterproduct.product_group, ' - ', masterproduct.product_item) as product_type
    //         ")
    //         ->leftJoin('act_costing', 'act_costing.id', '=', 'master_plan.id_ws')
    //         ->leftJoin('so', 'so.id_cost', '=', 'act_costing.id')
    //         ->leftJoin('so_det', 'so_det.id_so', '=', 'so.id')
    //         ->leftJoin('mastersupplier', 'mastersupplier.id_supplier', '=', 'act_costing.id_buyer')
    //         ->leftJoin('master_size_new', 'master_size_new.size', '=', 'so_det.size')
    //         ->leftJoin('masterproduct', 'masterproduct.id', '=', 'act_costing.id_product')
    //         ->where('so_det.cancel', 'N')
    //         ->where('master_plan.cancel', 'N')
    //         ->where('master_plan.id', $id)
    //         ->first();

    //     $orderWsDetailsSql = MasterPlan::selectRaw("
    //             master_plan.id as id,
    //             master_plan.tgl_plan as tgl_plan,
    //             master_plan.color as color,
    //             mastersupplier.supplier as buyer_name,
    //             act_costing.styleno as style_name,
    //             mastersupplier.supplier as buyer_name
    //         ")
    //         ->leftJoin('act_costing', 'act_costing.id', '=', 'master_plan.id_ws')
    //         ->leftJoin('so', 'so.id_cost', '=', 'act_costing.id')
    //         ->leftJoin('so_det', 'so_det.id_so', '=', 'so.id')
    //         ->leftJoin('mastersupplier', 'mastersupplier.id_supplier', '=', 'act_costing.id_buyer')
    //         ->leftJoin('master_size_new', 'master_size_new.size', '=', 'so_det.size')
    //         ->leftJoin('masterproduct', 'masterproduct.id', '=', 'act_costing.id_product')
    //         ->where('so_det.cancel', 'N')
    //         ->where('master_plan.cancel', 'N');
    //         if (Auth::user()->Groupp != "ALLSEWING") {
    //             $orderWsDetailsSql->where('master_plan.sewing_line', Auth::user()->username);
    //         }
    //     $orderWsDetails = $orderWsDetailsSql->where('act_costing.kpno', $orderInfo->ws_number)
    //         ->where('master_plan.tgl_plan', $orderInfo->tgl_plan)
    //         ->groupBy(
    //             'master_plan.id',
    //             'master_plan.tgl_plan',
    //             'master_plan.color',
    //             'mastersupplier.supplier',
    //             'act_costing.styleno',
    //             'mastersupplier.supplier'
    //         )->get();

    //     return view('production-panel', ['orderInfo' => $orderInfo, 'orderWsDetails' => $orderWsDetails]);
    // }

    public function getMasterPlan(Request $request) {
        $additionalQuery = "";
        if ($request->date) {
            $additionalQuery .= " AND master_plan.tgl_plan = '".$request->date."' ";
        }
        if ($request->line) {
            $additionalQuery .= " AND master_plan.sewing_line = '".$request->line."' ";
        }

        $masterPlans = MasterPlan::selectRaw('
                master_plan.id,
                master_plan.tgl_plan as tanggal,
                master_plan.id_ws as id_ws,
                act_costing.kpno as no_ws,
                act_costing.styleno as style,
                master_plan.color as color
            ')->
            leftJoin('act_costing', 'act_costing.id', '=', 'master_plan.id_ws')->
            whereRaw('
                master_plan.cancel != "Y"
                '.$additionalQuery.'
            ')->
            get();

        return $masterPlans;
    }

    public function getSize(Request $request) {
        if ($request->master_plan) {
            $sizes = MasterPlan::selectRaw("
                so_det.id,
                so_det.color,
                so_det.size
            ")->
            leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
            leftJoin("so", "so.id_cost", "=", "act_costing.id")->
            leftJoin("so_det", "so_det.id_so", "=", "so.id")->
            whereRaw("master_plan.id = '".$request->master_plan."'")->
            whereRaw("so_det.color = master_plan.color")->
            groupBy("so_det.color", "so_det.size")->
            orderBy("so_det.id")->
            get();

            return $sizes;
        }

        return null;
    }

    public function getSecondaryMaster(Request $request) {
        return SewingSecondaryMaster::get();
    }

    public function getDefectType(Request $request) {
        $additionalQuery = "";
        if ($request->date) {
            $additionalQuery .= " AND master_plan.tgl_plan = '".$request->date."' ";
        }
        if ($request->line) {
            $additionalQuery .= " AND master_plan.sewing_line = '".$request->line."' ";
        }
        if ($request->master_plan) {
            $additionalQuery .= " AND master_plan.id = '".$request->master_plan."' ";
        }
        if ($request->size) {
            $additionalQuery .= " AND output_defects.so_det_id = '".$request->size."' ";
        }
        if ($request->defect_area) {
            $additionalQuery .= " AND output_defects.defect_area_id = '".$request->defect_area."' ";
        }

        $defects = DB::table("output_defects")->selectRaw("
                output_defects.defect_type_id as id,
                output_defect_types.defect_type,
                COUNT(output_defects.id) defect_qty
            ")->
            leftJoin("so_det", "so_det.id", "=", "output_defects.so_det_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_defects.master_plan_id")->
            leftJoin("output_defect_types", "output_defect_types.id", "=", "output_defects.defect_type_id")->
            whereRaw("
                output_defects.defect_status = 'defect'
                and output_defect_types.allocation = '".Auth::user()->Groupp."'
                ".$additionalQuery."
            ")->
            whereRaw("so_det.color = master_plan.color")->
            groupBy("output_defects.defect_type_id")->
            orderBy("output_defect_types.defect_type")->
            get();

        return $defects;
    }

    public function getDefectArea(Request $request) {
        $additionalQuery = "";
        if ($request->date) {
            $additionalQuery .= " AND master_plan.tgl_plan = '".$request->date."' ";
        }
        if ($request->line) {
            $additionalQuery .= " AND master_plan.sewing_line = '".$request->line."' ";
        }
        if ($request->master_plan) {
            $additionalQuery .= " AND master_plan.id = '".$request->master_plan."' ";
        }
        if ($request->size) {
            $additionalQuery .= " AND output_defects.so_det_id = '".$request->size."' ";
        }
        if ($request->defect_type) {
            $additionalQuery .= " AND output_defects.defect_type_id = '".$request->defect_type."' ";
        }

        $defects = DB::table("output_defects")->selectRaw("
                output_defects.defect_area_id as id,
                output_defect_areas.defect_area,
                COUNT(output_defects.id) defect_qty
            ")->
            leftJoin("so_det", "so_det.id", "=", "output_defects.so_det_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_defects.master_plan_id")->
            leftJoin("output_defect_types", "output_defect_types.id", "=", "output_defects.defect_type_id")->
            leftJoin("output_defect_areas", "output_defect_areas.id", "=", "output_defects.defect_area_id")->
            whereRaw("
                output_defects.defect_status = 'defect'
                and output_defect_types.allocation = '".Auth::user()->Groupp."'
                ".$additionalQuery."
            ")->
            whereRaw("so_det.color = master_plan.color")->
            groupBy("output_defects.defect_area_id")->
            orderBy("output_defect_areas.defect_area")->
            get();

        return $defects;
    }

    public function getSecondaryInOutDaily(Request $request) {
        $dateFrom = $request->dateFrom ? $request->dateFrom : date("Y-m-d");
        $dateTo = $request->dateTo ? $request->dateTo : date("Y-m-d");

        $secondary = $request->secondary ? $request->secondary : '';

        $secondaryInOutDaily = SewingSecondaryIn::selectRaw("
                DATE(output_secondary_in.created_at) tanggal,
                COUNT(output_secondary_in.id) total_in,
                SUM(CASE WHEN output_secondary_out.status = 'rft' OR output_secondary_out.status = 'rework' THEN 1 ELSE 0 END) total_rft,
                SUM(CASE WHEN output_secondary_out.status = 'defect' THEN 1 ELSE 0 END) total_defect,
                SUM(CASE WHEN output_secondary_out.status = 'reject' THEN 1 ELSE 0 END) total_reject,
                SUM(CASE WHEN output_secondary_out.id IS NOT NULL THEN 1 ELSE 0 END) total_process
            ")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            where("output_secondary_master.id", $secondary)->
            whereNotNull("output_secondary_out.id")->
            whereBetween("output_secondary_in.created_at", [$dateFrom." 00:00:00", $dateTo." 23:59:59"])->
            groupByRaw("DATE(output_secondary_in.created_at)")->
            get();

        return DataTables::of($secondaryInOutDaily)->toJson();
    }

    public function getSecondaryInOutDetail(Request $request) {
        $secondaryInOutQuery = SewingSecondaryIn::selectRaw("
                output_secondary_in.created_at time_in,
                output_secondary_out.created_at time_out,
                userpassword.username sewing_line,
                output_secondary_in.kode_numbering,
                act_costing.kpno no_ws,
                act_costing.styleno style,
                so_det.color color,
                so_det.size size,
                COALESCE(output_defect_types.defect_type, output_defect_types_reject.defect_type) defect_type,
                COALESCE(output_defect_areas.defect_area, output_defect_areas_reject.defect_area) defect_area,
                master_plan.gambar gambar,
                COALESCE(output_secondary_out_defect.defect_area_x, output_secondary_out_reject.defect_area_x) defect_area_x,
                COALESCE(output_secondary_out_defect.defect_area_y, output_secondary_out_reject.defect_area_y) defect_area_y,
                (CASE WHEN output_secondary_out.id IS NOT NULL THEN UPPER(output_secondary_out.status) ELSE 'WIP' END) as status,
                output_secondary_in.created_by_username user_in,
                output_secondary_out.created_by_username user_out
            ")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            leftJoin("output_secondary_out_defect", "output_secondary_out_defect.secondary_out_id", "=", "output_secondary_out.id")->
            leftJoin("output_secondary_out_reject", "output_secondary_out_reject.secondary_out_id", "=", "output_secondary_out.id")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_defect_types", "output_defect_types.id", "=", "output_secondary_out_defect.defect_type_id")->
            leftJoin("output_defect_types as output_defect_types_reject", "output_defect_types_reject.id", "=", "output_secondary_out_reject.defect_type_id")->
            leftJoin("output_defect_areas", "output_defect_areas.id", "=", "output_secondary_out_defect.defect_area_id")->
            leftJoin("output_defect_areas as output_defect_areas_reject", "output_defect_areas_reject.id", "=", "output_secondary_out_reject.defect_area_id")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("so", "so.id", "=", "so_det.id_so")->
            leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            // Conditional
            whereRaw("
                (
                    output_secondary_in.created_at between '".$request->tanggal." 00:00:00' and '".$request->tanggal." 23:59:59'
                    OR
                    output_secondary_out.created_at between '".$request->tanggal." 00:00:00' and '".$request->tanggal." 23:59:59'
                )
            ")->
            whereRaw("
                (
                    output_secondary_in.id IS NOT NULL AND
                    output_secondary_out.id IS NOT NULL AND
                    output_rfts.id IS NOT NULL AND
                    output_secondary_master.id = '".$request->secondary."'
                )
            ")->
            groupBy("output_secondary_in.id")->
            get();

        return DataTables::of($secondaryInOutQuery)->toJson();
    }

    public function getSecondaryInOutDetailTotal(Request $request) {
        $secondaryInOutQuery = SewingSecondaryIn::selectRaw("
                output_secondary_in.created_at time_in,
                output_secondary_out.created_at time_out,
                userpassword.username sewing_line,
                output_secondary_in.kode_numbering,
                act_costing.kpno no_ws,
                act_costing.styleno style,
                so_det.color color,
                so_det.size size,
                COALESCE(output_defect_types.defect_type, output_defect_types_reject.defect_type) defect_type,
                COALESCE(output_defect_areas.defect_area, output_defect_areas_reject.defect_area) defect_area,
                master_plan.gambar gambar,
                COALESCE(output_secondary_out_defect.defect_area_x, output_secondary_out_reject.defect_area_x) defect_area_x,
                COALESCE(output_secondary_out_defect.defect_area_y, output_secondary_out_reject.defect_area_y) defect_area_y,
                (CASE WHEN output_secondary_out.id IS NOT NULL THEN UPPER(output_secondary_out.status) ELSE 'WIP' END) as status,
                output_secondary_in.created_by_username user_in,
                output_secondary_out.created_by_username user_out
            ")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            leftJoin("output_secondary_out_defect", "output_secondary_out_defect.secondary_out_id", "=", "output_secondary_out.id")->
            leftJoin("output_secondary_out_reject", "output_secondary_out_reject.secondary_out_id", "=", "output_secondary_out.id")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_defect_types", "output_defect_types.id", "=", "output_secondary_out_defect.defect_type_id")->
            leftJoin("output_defect_types as output_defect_types_reject", "output_defect_types_reject.id", "=", "output_secondary_out_reject.defect_type_id")->
            leftJoin("output_defect_areas", "output_defect_areas.id", "=", "output_secondary_out_defect.defect_area_id")->
            leftJoin("output_defect_areas as output_defect_areas_reject", "output_defect_areas_reject.id", "=", "output_secondary_out_reject.defect_area_id")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("so", "so.id", "=", "so_det.id_so")->
            leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            // Conditional
            whereRaw("
                (
                    output_secondary_in.created_at between '".$request->tanggal." 00:00:00' and '".$request->tanggal." 23:59:59'
                    OR
                    output_secondary_out.created_at between '".$request->tanggal." 00:00:00' and '".$request->tanggal." 23:59:59'
                )
            ")->
            whereRaw("
                (
                    output_secondary_in.id IS NOT NULL AND
                    output_secondary_out.id IS NOT NULL AND
                    output_rfts.id IS NOT NULL AND
                    output_secondary_master.id = '".$request->selectedSecondary."'
                    ".($request->line ? "AND userpassword.username LIKE '%".$request->line."%'" : "")."
                )
            ")->
            groupBy("output_secondary_in.id")->
            get();

        return array("secondaryIn" => $secondaryInOutQuery->count(), "secondaryProcess" => $secondaryInOutQuery->where("status", "WIP")->count(), "secondaryRft" => $secondaryInOutQuery->where("status", "RFT")->count(), "secondaryDefect" => $secondaryInOutQuery->where("status", "DEFECT")->count(), "secondaryReject" => $secondaryInOutQuery->where("status", "REJECT")->count());
    }

    public function getSecondaryOutList(Request $request)
    {
        $secondaryInSearch = "";
        if ($request->secondaryInSearch) {
            $secondaryInSearch = "
                AND (
                    master_plan.tgl_plan LIKE '%".$request->secondaryInSearch."%' OR
                    master_plan.sewing_line LIKE '%".$request->secondaryInSearch."%' OR
                    act_costing.kpno LIKE '%".$request->secondaryInSearch."%' OR
                    act_costing.styleno LIKE '%".$request->secondaryInSearch."%' OR
                    master_plan.color LIKE '%".$request->secondaryInSearch."%' OR
                    so_det.size LIKE '%".$request->secondaryInSearch."%' OR
                    output_rfts.kode_numbering LIKE '%".$request->secondaryInSearch."%' OR
                    output_secondary_in.kode_numbering LIKE '%".$request->secondaryInSearch."%'
                )
            ";
        }

        $secondaryInLine = "";
        if ($request->secondaryInLine) {
            $secondaryInLine = "
                AND master_plan.sewing_line = '".$request->secondaryInLine."'
            ";
        }

        $secondaryInFilterMasterPlan = "";
        if ($request->secondaryInFilterMasterPlan) {
            $secondaryInFilterMasterPlan = "
                AND
                (
                    act_costing_ws.kpno LIKE '%".$request->secondaryInFilterMasterPlan."%' OR
                    act_costing.styleno LIKE '%".$request->secondaryInFilterMasterPlan."%' OR
                    so_det.color LIKE '%".$request->secondaryInFilterMasterPlan."%'
                )
            ";
        }

        $secondaryInFilterStyle = "";
        if ($request->secondaryInFilterStyle) {
            $secondaryInFilterStyle = "
            ";
        }

        $secondaryInFilterColor = "";
        if ($request->secondaryInFilterColor) {
            $secondaryInFilterColor = "
            ";
        }

        $secondaryInFilterSize = "";
        if ($request->secondaryInFilterSize) {
            $secondaryInFilterSize = " AND so_det.size LIKE '%".$request->secondaryInFilterSize."%' ";
        }

        $secondaryInFilterAuthor = "";
        if ($request->secondaryInFilterAuthor) {
            $secondaryInFilterAuthor = " AND output_secondary_in.created_by_username LIKE '%".$request->secondaryInFilterAuthor."%' ";
        }

        $secondaryInFilterWaktu = "";
        if ($request->secondaryInFilterWaktu) {
            $secondaryInFilterWaktu = " AND COALESCE(output_secondary_in.updated_at, output_secondary_in.created_at) LIKE '%".$request->secondaryInFilterWaktu."%' ";
        }

        $secondaryInList = collect(
            DB::select("
                SELECT
                    output_secondary_in.id,
                    output_secondary_in.updated_at secondary_in_time,
                    userpassword.username sewing_line,
                    output_secondary_in.kode_numbering,
                    output_rfts.so_det_id,
                    act_costing.kpno ws,
                    act_costing.styleno style,
                    so_det.color,
                    so_det.size,
                    output_rfts.id rft_id,
                    COUNT(output_secondary_in.id) as secondary_in_qty,
                    output_secondary_master.secondary,
                    output_secondary_in.created_by_username
                FROM
                    output_secondary_out
                    LEFT JOIN `output_secondary_in` ON `output_secondary_in`.`secondary_out_id` = `output_secondary_out`.`id`
                    LEFT JOIN `output_secondary_master` ON `output_secondary_master`.`id` = `output_secondary_in`.`secondary_id`
                    LEFT JOIN `output_rfts` ON `output_rfts`.`id` = `output_secondary_in`.`rft_id`
                    LEFT JOIN `user_sb_wip` ON `user_sb_wip`.`id` = `output_rfts`.`created_by`
                    LEFT JOIN `userpassword` ON `userpassword`.`line_id` = `user_sb_wip`.`line_id`
                    LEFT JOIN `so_det` ON `so_det`.`id` = `output_rfts`.`so_det_id`
                    LEFT JOIN `so` ON `so`.`id` = `so_det`.`id_so`
                    LEFT JOIN `act_costing` ON `act_costing`.`id` = `so`.`id_cost`
                    LEFT JOIN `master_plan` ON `master_plan`.`id` = `output_rfts`.`master_plan_id`
                WHERE
                    `output_rfts`.`id` IS NOT NULL
                    AND output_rfts.master_plan_id is not null
                    AND output_secondary_in.updated_at >= '2025-12-01 00:00:00'
                    AND output_secondary_master.id = '".$request->selectedSecondary."'
                    ".$secondaryInSearch."
                    ".$secondaryInFilterKode."
                    ".$secondaryInFilterLine."
                    ".$secondaryInFilterWs."
                    ".$secondaryInFilterStyle."
                    ".$secondaryInFilterColor."
                    ".$secondaryInFilterSize."
                    ".$secondaryInFilterSecondary."
                    ".$secondaryInFilterAuthor."
                    ".$secondaryInFilterWaktu."
                GROUP BY
                    output_rfts.id
                ORDER BY
                    output_secondary_in.updated_at DESC
            ")
        );

        return Datatables::of($secondaryInList)->toJson();
    }

    public function submitSecondaryIn(Request $request)
    {
        $validatedRequest = $request->validate([
            'scannedSecondaryIn' => 'required',
            'selectedSecondary' => 'required',
        ]);

        $status = "";
        $message = "";

        if ($request->scannedSecondaryIn) {
            $scannedOutput = Rft::selectRaw("
                    output_rfts.id,
                    output_rfts.kode_numbering,
                    output_rfts.so_det_id,
                    output_secondary_master.secondary,
                    act_costing.kpno ws,
                    act_costing.styleno style,
                    so_det.color,
                    so_det.size,
                    userpassword.username sewing_line,
                    output_secondary_in.id secondary_in_id
                ")->
                leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
                leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
                leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
                leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
                leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
                leftJoin("output_secondary_in", "output_secondary_in.rft_id", "=", "output_rfts.id")->
                leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
                whereNotNull("output_rfts.id")->
                where("output_rfts.kode_numbering", $validatedRequest['scannedSecondaryIn'])->
                first();

            if ($scannedOutput) {
                $secondaryIn = SewingSecondaryIn::where("rft_id", $scannedOutput->id)->first();

                if (!$secondaryIn) {
                    $createsecondaryIn = SewingSecondaryIn::create([
                        "kode_numbering" => $scannedOutput->kode_numbering,
                        "rft_id" => $scannedOutput->id,
                        "secondary_id" => $validatedRequest['selectedSecondary'],
                        "created_by" => Auth::user()->line_id,
                        "created_by_username" => Auth::user()->username,
                    ]);

                    if ($createsecondaryIn) {
                        $status = "success";
                        $message = "OUTPUT dengan KODE '".$validatedRequest['scannedSecondaryIn']."' berhasil masuk ke 'SECONDARY IN'";
                    } else {
                        $status = "error";
                        $message = "Terjadi Kesalahan.";
                    }
                } else {
                    $status = "warning";
                    $message = "QR sudah discan di ".strtoupper($scannedOutput->secondary);
                }
            } else {
                $status = "error";
                $message = "OUTPUT dengan QR '".$validatedRequest['scannedSecondaryIn']."' tidak ditemukan";
            }
        } else {
            $status = "error";
            $message = "QR tidak sesuai";
        }

        return array(
            "status" => $status,
            "message" => $message,
            "data" => $scannedOutput
        );
    }

    public function exportSecondaryInOut(Request $request) {
        return Excel::download(new SecondaryInOutExport($request->dateFrom, $request->dateTo, $request->selectedSecondary), 'Report Defect In Out.xlsx');
    }
}
