<?php

namespace App\Http\Controllers;

use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\Rft;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\DefectPacking;
use App\Models\SignalBit\OutputFinishing;
use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\SewingSecondaryOut;
use App\Models\SignalBit\SewingSecondaryOutDefect;
use App\Models\SignalBit\SewingSecondaryOutReject;
use App\Models\SignalBit\SewingSecondaryMaster;
use App\Exports\SecondaryOutExport;
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
                SUM(CASE WHEN output_secondary_out.status = 'rft' THEN 1 ELSE 0 END) total_rft,
                SUM(CASE WHEN output_secondary_out.status = 'defect' THEN 1 ELSE 0 END) total_defect,
                SUM(CASE WHEN output_secondary_out.status = 'reject' THEN 1 ELSE 0 END) total_reject,
                SUM(CASE WHEN output_secondary_out.status = 'rework' THEN 1 ELSE 0 END) total_rework,
                SUM(CASE WHEN output_secondary_out.id IS NOT NULL THEN 1 ELSE 0 END) total_process
            ")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            where("output_secondary_master.id", $secondary)->
            whereNotNull("output_secondary_out.id")->
            whereBetween(DB::raw("COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at)"), [$dateFrom." 00:00:00", $dateTo." 23:59:59"])->
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

        return array("secondaryIn" => $secondaryInOutQuery->count(), "secondaryProcess" => $secondaryInOutQuery->where("status", "WIP")->count(), "secondaryRft" => $secondaryInOutQuery->where("status", "RFT")->count(), "secondaryDefect" => $secondaryInOutQuery->where("status", "DEFECT")->count(), "secondaryRework" => $secondaryInOutQuery->where("status", "REWORK")->count(), "secondaryReject" => $secondaryInOutQuery->where("status", "REJECT")->count());
    }

    public function getSecondaryOutList(Request $request)
    {
        $secondaryOutSearch = "";
        if ($request->secondaryOutSearch) {
            $secondaryOutSearch = "
                AND (
                    master_plan.tgl_plan LIKE '%".$request->secondaryOutSearch."%' OR
                    master_plan.sewing_line LIKE '%".$request->secondaryOutSearch."%' OR
                    act_costing.kpno LIKE '%".$request->secondaryOutSearch."%' OR
                    act_costing.styleno LIKE '%".$request->secondaryOutSearch."%' OR
                    master_plan.color LIKE '%".$request->secondaryOutSearch."%' OR
                    so_det.size LIKE '%".$request->secondaryOutSearch."%' OR
                    output_rfts.kode_numbering LIKE '%".$request->secondaryOutSearch."%' OR
                    output_secondary_in.kode_numbering LIKE '%".$request->secondaryOutSearch."%'
                )
            ";
        }

        $secondaryOutLine = "";
        if ($request->secondaryOutLine) {
            $secondaryOutLine = "
                AND master_plan.sewing_line = '".$request->secondaryOutLine."'
            ";
        }

        $secondaryOutFilterMasterPlan = "";
        if ($request->secondaryOutFilterMasterPlan) {
            $secondaryOutFilterMasterPlan = "
                AND
                (
                    act_costing_ws.kpno LIKE '%".$request->secondaryOutFilterMasterPlan."%' OR
                    act_costing.styleno LIKE '%".$request->secondaryOutFilterMasterPlan."%' OR
                    so_det.color LIKE '%".$request->secondaryOutFilterMasterPlan."%'
                )
            ";
        }

        $secondaryOutFilterStyle = "";
        if ($request->secondaryOutFilterStyle) {
            $secondaryOutFilterStyle = "
            ";
        }

        $secondaryOutFilterColor = "";
        if ($request->secondaryOutFilterColor) {
            $secondaryOutFilterColor = "
            ";
        }

        $secondaryOutFilterSize = "";
        if ($request->secondaryOutFilterSize) {
            $secondaryOutFilterSize = " AND so_det.size LIKE '%".$request->secondaryOutFilterSize."%' ";
        }

        $secondaryOutFilterAuthor = "";
        if ($request->secondaryOutFilterAuthor) {
            $secondaryOutFilterAuthor = " AND output_secondary_in.created_by_username LIKE '%".$request->secondaryOutFilterAuthor."%' ";
        }

        $secondaryOutFilterWaktu = "";
        if ($request->secondaryOutFilterWaktu) {
            $secondaryOutFilterWaktu = " AND COALESCE(output_secondary_in.updated_at, output_secondary_in.created_at) LIKE '%".$request->secondaryOutFilterWaktu."%' ";
        }

        $secondaryOutList = collect(
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
                    ".$secondaryOutSearch."
                    ".$secondaryOutFilterKode."
                    ".$secondaryOutFilterLine."
                    ".$secondaryOutFilterWs."
                    ".$secondaryOutFilterStyle."
                    ".$secondaryOutFilterColor."
                    ".$secondaryOutFilterSize."
                    ".$secondaryOutFilterSecondary."
                    ".$secondaryOutFilterAuthor."
                    ".$secondaryOutFilterWaktu."
                GROUP BY
                    output_rfts.id
                ORDER BY
                    output_secondary_in.updated_at DESC
            ")
        );

        return Datatables::of($secondaryOutList)->toJson();
    }

    public function getSecondaryOutLog(Request $request)
    {
        // Date filter
        $date = $request->date ? $request->date : date("Y-m-d");

        // Status filter
        $status = $request->status;

        // Selected Secondary Filter
        $selectedSecondary = $request->selectedSecondary;

        $log = collect(DB::connection("mysql_sb")->select("
            select
                output_secondary_out.*,
                output_secondary_in.kode_numbering,
                act_costing.kpno ws,
                act_costing.styleno style,
                so_det.color,
                so_det.size,
                userpassword.username as sewing_line,
                output_secondary_master.secondary,
                defect_types.defect_type,
                defect_areas.defect_area,
                output_secondary_out_defect.defect_area_x,
                output_secondary_out_defect.defect_area_y,
                reject_types.defect_type as reject_type,
                reject_areas.defect_area as reject_area,
                output_secondary_out_reject.defect_area_x as reject_area_x,
                output_secondary_out_reject.defect_area_y as reject_area_y,
                COUNT(output_secondary_out.id) output,
                output_secondary_out.created_by_username,
                COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at) as secondary_out_time,
                master_plan.gambar
            from
                `output_secondary_out`
                left join `output_secondary_out_defect` on `output_secondary_out_defect`.`secondary_out_id` = `output_secondary_out`.`id`
                left join `output_secondary_out_reject` on `output_secondary_out_reject`.`secondary_out_id` = `output_secondary_out`.`id`
                left join `output_defect_types` defect_types on `defect_types`.`id` = `output_secondary_out_defect`.`defect_type_id`
                left join `output_defect_areas` defect_areas on `defect_areas`.`id` = `output_secondary_out_defect`.`defect_area_id`
                left join `output_defect_types` reject_types on `reject_types`.`id` = `output_secondary_out_reject`.`defect_type_id`
                left join `output_defect_areas` reject_areas on `reject_areas`.`id` = `output_secondary_out_reject`.`defect_area_id`
                left join `output_secondary_in` on `output_secondary_in`.`id` = `output_secondary_out`.`secondary_in_id`
                left join `output_secondary_master` on `output_secondary_master`.`id` = `output_secondary_in`.`secondary_id`
                left join `output_rfts` on `output_rfts`.`id` = `output_secondary_in`.`rft_id`
                left join `user_sb_wip` on `user_sb_wip`.`id` = `output_rfts`.`created_by`
                left join `userpassword` on `userpassword`.`line_id` = `user_sb_wip`.`line_id`
                left join `so_det` on `so_det`.`id` = `output_rfts`.`so_det_id`
                left join `so` on `so`.`id` = `so_det`.`id_so`
                left join `act_costing` on `act_costing`.`id` = `so`.`id_cost`
                left join `master_plan` on `master_plan`.`id` = `output_rfts`.`master_plan_id`
            where
                COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at) between '".$date." 00:00:00' and '".$date." 23:59:59'
                ".($status ? " and output_secondary_out.status = '".$status."' " : "")."
                ".($selectedSecondary ? " and output_secondary_in.secondary_id = '".$selectedSecondary."' " : "")."
            group by
                act_costing.kpno,
                act_costing.styleno,
                so_det.color,
                so_det.size,
                output_secondary_out.created_at,
                output_secondary_out.kode_numbering
        "));

        return Datatables::of($log)->toJson();
    }

    public function getSecondaryOutLogSingle(Request $request)
    {
        // Date filter
        $date = $request->date ? $request->date : date("Y-m-d");

        // Status filter
        $status = $request->status;

        // Selected Secondary Filter
        $selectedSecondary = $request->selectedSecondary;

        // Selected Order Filter
        $additionalQuery = "";
        if ($request->worksheet) {
            $additionalQuery .= " and act_costing.id = '".$request->worksheet."' ";
        }
        if ($request->style) {
            $additionalQuery .= " and act_costing.styleno = '".$request->style."' ";
        }
        if ($request->color) {
            $additionalQuery .= " and so_det.color = '".$request->color."' ";
        }
        if ($request->size) {
            $additionalQuery .= " and so_det.size = '".$request->size."' ";
        }
        if ($request->sewingLine) {
            $additionalQuery .= " and userpassword.username = '".$request->sewingLine."' ";
        }

        $log = collect(DB::connection("mysql_sb")->select("
            select
                output_secondary_out.*,
                output_secondary_in.kode_numbering,
                act_costing.kpno ws,
                act_costing.styleno style,
                so_det.color,
                so_det.size,
                userpassword.username as sewing_line,
                output_secondary_master.secondary,
                defect_types.defect_type,
                defect_areas.defect_area,
                output_secondary_out_defect.defect_area_x,
                output_secondary_out_defect.defect_area_y,
                reject_types.defect_type as reject_type,
                reject_areas.defect_area as reject_area,
                output_secondary_out_reject.defect_area_x as reject_area_x,
                output_secondary_out_reject.defect_area_y as reject_area_y,
                output_secondary_out.created_by_username,
                output_secondary_out_defect.status as defect_status,
                output_secondary_out_reject.status as reject_status,
                COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at) as secondary_out_time,
                master_plan.gambar
            from
                `output_secondary_out`
                left join `output_secondary_out_defect` on `output_secondary_out_defect`.`secondary_out_id` = `output_secondary_out`.`id`
                left join `output_secondary_out_reject` on `output_secondary_out_reject`.`secondary_out_id` = `output_secondary_out`.`id`
                left join `output_defect_types` defect_types on `defect_types`.`id` = `output_secondary_out_defect`.`defect_type_id`
                left join `output_defect_areas` defect_areas on `defect_areas`.`id` = `output_secondary_out_defect`.`defect_area_id`
                left join `output_defect_types` reject_types on `reject_types`.`id` = `output_secondary_out_reject`.`defect_type_id`
                left join `output_defect_areas` reject_areas on `reject_areas`.`id` = `output_secondary_out_reject`.`defect_area_id`
                left join `output_secondary_in` on `output_secondary_in`.`id` = `output_secondary_out`.`secondary_in_id`
                left join `output_secondary_master` on `output_secondary_master`.`id` = `output_secondary_in`.`secondary_id`
                left join `output_rfts` on `output_rfts`.`id` = `output_secondary_in`.`rft_id`
                left join `user_sb_wip` on `user_sb_wip`.`id` = `output_rfts`.`created_by`
                left join `userpassword` on `userpassword`.`line_id` = `user_sb_wip`.`line_id`
                left join `so_det` on `so_det`.`id` = `output_rfts`.`so_det_id`
                left join `so` on `so`.`id` = `so_det`.`id_so`
                left join `act_costing` on `act_costing`.`id` = `so`.`id_cost`
                left join `master_plan` on `master_plan`.`id` = `output_rfts`.`master_plan_id`
            where
                output_secondary_out.kode_numbering is null
                ".($status && $status == 'defect' ? "" : " and COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at) between '".$date." 00:00:00' and '".$date." 23:59:59' ")."
                ".($status ? " and output_secondary_out.status = '".$status."' " : "")."
                ".($selectedSecondary ? " and output_secondary_in.secondary_id = '".$selectedSecondary."' " : "")."
                ".($additionalQuery)."
            group by
                output_secondary_out.id
        "));

        return Datatables::of($log)->toJson();
    }

    public function getSecondaryOutTotal(Request $request)
    {
        // Date filter
        $date = $request->date ? $request->date : date("Y-m-d");

        // Status filter
        $status = $request->status;

        // Selected Secondary Filter
        $selectedSecondary = $request->selectedSecondary;

        $total = collect(DB::connection("mysql_sb")->select("
            select
                act_costing.kpno ws,
                act_costing.styleno style,
                so_det.color,
                so_det.size,
                userpassword.username as sewing_line,
                output_secondary_master.secondary,
                COUNT(output_secondary_out.id) output,
                output_secondary_out.created_by_username,
                master_plan.tgl_plan master_plan_tanggal,
                master_plan.id as master_plan_id,
                master_plan_ws.kpno as master_plan_ws,
                master_plan_ws.styleno as master_plan_style,
                master_plan.color as master_plan_color,
                master_plan.sewing_line as master_plan_line,
                SUM(CASE WHEN output_secondary_out.status = 'rft' THEN 1 ELSE 0 END) total_rft,
                SUM(CASE WHEN output_secondary_out.status = 'defect' THEN 1 ELSE 0 END) total_defect,
                SUM(CASE WHEN output_secondary_out.status = 'rework' THEN 1 ELSE 0 END) total_rework,
                SUM(CASE WHEN output_secondary_out.status = 'reject' THEN 1 ELSE 0 END) total_reject,
                master_plan.gambar
            from
                `output_secondary_out`
                left join `output_secondary_out_defect` on `output_secondary_out_defect`.`secondary_out_id` = `output_secondary_out`.`id`
                left join `output_secondary_out_reject` on `output_secondary_out_reject`.`secondary_out_id` = `output_secondary_out`.`id`
                left join `output_secondary_in` on `output_secondary_in`.`id` = `output_secondary_out`.`secondary_in_id`
                left join `output_secondary_master` on `output_secondary_master`.`id` = `output_secondary_in`.`secondary_id`
                left join `output_rfts` on `output_rfts`.`id` = `output_secondary_in`.`rft_id`
                left join `master_plan` on `master_plan`.`id` = `output_rfts`.`master_plan_id`
                left join `act_costing` master_plan_ws on master_plan_ws.id = master_plan.id_ws
                left join `user_sb_wip` on `user_sb_wip`.`id` = `output_rfts`.`created_by`
                left join `userpassword` on `userpassword`.`line_id` = `user_sb_wip`.`line_id`
                left join `so_det` on `so_det`.`id` = `output_rfts`.`so_det_id`
                left join `so` on `so`.`id` = `so_det`.`id_so`
                left join `act_costing` on `act_costing`.`id` = `so`.`id_cost`
            where
                COALESCE(output_secondary_out.updated_at, output_secondary_out.created_at) between '".$date." 00:00:00' and '".$date." 23:59:59'
                ".($selectedSecondary ? " and output_secondary_in.secondary_id = '".$selectedSecondary."' " : "")."
            group by
                master_plan.id,
                output_secondary_in.secondary_id
        "));

        return Datatables::of($total)->toJson();
    }

    public function getSecondaryInWipTotal(Request $request) {
        $secondaryInOutputs = DB::connection("mysql_sb")->table("output_secondary_in")->selectRaw("
                COUNT(output_secondary_in.id) total_secondary_in_wip
            ")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("so", "so.id", "=", "so_det.id_so")->
            leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            whereRaw("
                output_rfts.kode_numbering is null and
                output_secondary_out.id is null and
                act_costing.id = '".$request->worksheet."' and
                so_det.color = '".$request->color."' and
                so_det.size = '".$request->size."'
                ".($request->sewingLine ? " and userpassword.username = '".$request->sewingLine."' " : "")."
            ")->
            first();

        return $secondaryInOutputs ? $secondaryInOutputs->total_secondary_in_wip : 0;
    }

    public function getSecondaryOutLogTotal(Request $request) {
        $secondaryOutOutputs = DB::connection("mysql_sb")->table("output_secondary_out")->selectRaw("
                COUNT(output_secondary_out.id) total_secondary_out
            ")->
            leftJoin("output_secondary_in", "output_secondary_in.id", "=", "output_secondary_out.secondary_in_id")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("so", "so.id", "=", "so_det.id_so")->
            leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            whereRaw("
                output_rfts.kode_numbering is null and
                output_secondary_out.id is not null and
                act_costing.id = '".$request->worksheet."' and
                so_det.color = '".$request->color."' and
                so_det.size = '".$request->size."'
                ".($request->status ? " and output_secondary_out.status = '".$request->status."' " : "")."
                ".($request->sewingLine ? " and userpassword.username = '".$request->sewingLine."' " : "")."
            ")->
            first();

        return $secondaryOutOutputs ? $secondaryOutOutputs->total_secondary_out : 0;
    }

    public function submitSecondaryOutRft(Request $request) {
        $validatedRequest = $request->validate([
            'selectedSecondary' => 'required',
            'sewingLine' => 'required',
            'worksheet' => 'required',
            'color' => 'required',
            'size' => 'required',
            'qty' => 'required|gt:0',
        ],[
            'selectedSecondary.required' => 'Harap tentukan secondary <br>',
            'sewingLine.required' => 'Harap tentukan sewing line <br>',
            'worksheet.required' => 'Harap tentukan worksheet <br>',
            'color.required' => 'Harap tentukan color <br>',
            'size.required' => 'Harap tentukan size <br>',
            'qty.required' => 'Harap tentukan qty <br>',
            'qty.gt' => 'Minimal qty : 1 <br>',
        ]);

        // Check Output Sewing
        $secondaryInOutputs = SewingSecondaryIn::selectRaw("
                output_secondary_in.id,
                output_rfts.so_det_id,
                output_secondary_master.secondary,
                act_costing.kpno ws,
                act_costing.styleno style,
                so_det.color,
                so_det.size,
                userpassword.username sewing_line
            ")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            whereNull("output_rfts.kode_numbering")->
            whereNull("output_secondary_out.id")->
            where("output_secondary_in.secondary_id", $validatedRequest['selectedSecondary'])->
            where("userpassword.username", $validatedRequest['sewingLine'])->
            where("act_costing.id", $validatedRequest['worksheet'])->
            where("so_det.color", $validatedRequest['color'])->
            where("so_det.size", $validatedRequest['size'])->
            limit($validatedRequest['qty'])->
            get();

        if ($secondaryInOutputs) {

            // Prepare Secondary OUT Array
            $secondaryOutInputArray = [];
            $secondaryOutExistArr = [];
            foreach ($secondaryInOutputs as $secondaryInOutput) {

                // Check Secondary Out Availibility
                $secondaryOut = SewingSecondaryOut::where("secondary_in_id", $secondaryInOutput->id)->first();
                if (!$secondaryOut) {
                    array_push($secondaryOutInputArray, [
                        "secondary_in_id" => $secondaryInOutput->id,
                        "status" => "rft",
                        "created_by" => Auth::user()->line_id,
                        "created_by_username" => Auth::user()->username,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ]);
                } else {
                    array_push($secondaryOutExistArr, $secondaryInOutput->id);
                }

            }

            // Store Secondary OUT
            $secondaryOutStore = SewingSecondaryOut::insert($secondaryOutInputArray);

            return array(
                "status" => 200,
                "message" => "Transaksi Selesai",
                "success" => count($secondaryOutInputArray),
                "fail" => $validatedRequest['qty'] - (count($secondaryOutInputArray) + count($secondaryOutExistArr)),
                "exist" => count($secondaryOutExistArr),
            );

        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
            "success" => 0,
            "fail" => $validatedRequest['qty'],
            "exist" => 0,
        );
    }

    public function submitSecondaryOutDefect(Request $request) {
        $validatedRequest = $request->validate([
            'selectedSecondary' => 'required',
            'sewingLine' => 'required',
            'worksheet' => 'required',
            'color' => 'required',
            'size' => 'required',
            'defectType' => 'required',
            'defectArea' => 'required',
            'defectAreaPositionX' => 'required',
            'defectAreaPositionY' => 'required',
            'qty' => 'required|gt:0',
        ],[
            'selectedSecondary.required' => 'Harap tentukan secondary <br>',
            'sewingLine.required' => 'Harap tentukan sewing line <br>',
            'worksheet.required' => 'Harap tentukan worksheet <br>',
            'color.required' => 'Harap tentukan color <br>',
            'size.required' => 'Harap tentukan size <br>',
            'defectType.required' => 'Harap tentukan Defect Type <br>',
            'defectArea.required' => 'Harap tentukan Defect Area <br>',
            'defectAreaPositionX.required' => 'Harap tentukan posisi defect area dengan mengklik tombol di samping "select defect area".',
            'defectAreaPositionY.required' => 'Harap tentukan posisi defect area dengan mengklik tombol di samping "select defect area".',
            'qty.required' => 'Harap tentukan qty <br>',
            'qty.gt' => 'Minimal Qty : 1 <br>',
        ]);

        // Check Output Sewing
        $secondaryInOutputs = SewingSecondaryIn::selectRaw("
                output_secondary_in.id,
                output_rfts.so_det_id,
                output_secondary_master.secondary,
                act_costing.kpno ws,
                act_costing.styleno style,
                so_det.color,
                so_det.size,
                userpassword.username sewing_line
            ")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            whereNull("output_rfts.kode_numbering")->
            whereNull("output_secondary_out.id")->
            where("output_secondary_in.secondary_id", $validatedRequest['selectedSecondary'])->
            where("userpassword.username", $validatedRequest['sewingLine'])->
            where("act_costing.id", $validatedRequest['worksheet'])->
            where("so_det.color", $validatedRequest['color'])->
            where("so_det.size", $validatedRequest['size'])->
            limit($validatedRequest['qty'])->
            get();

        if ($secondaryInOutputs) {

            // Prepare Secondary OUT Array
            $secondaryOutInputArray = [];
            $secondaryOutExistArr = [];
            foreach ($secondaryInOutputs as $secondaryInOutput) {

                // Check Secondary Out Availibility
                $secondaryOut = SewingSecondaryOut::where("secondary_in_id", $secondaryInOutput->id)->first();
                if (!$secondaryOut) {

                    // Create Secondary Out Defect
                    $insertDefect = SewingSecondaryOut::create([
                        'kode_numbering' => $secondaryInOutput->kode_numbering,
                        'secondary_in_id' => $secondaryInOutput->id,
                        'status' => 'defect',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::user()->line_id,
                        'created_by_username' => Auth::user()->username
                    ]);

                    if ($insertDefect) {

                        // Prepare Secondary Out Defect Detail
                        array_push($secondaryOutInputArray, [
                            'secondary_out_id' => $insertDefect->id,
                            'defect_type_id' => $validatedRequest['defectType'],
                            'defect_area_id' => $validatedRequest['defectArea'],
                            'defect_area_x' => $validatedRequest['defectAreaPositionX'],
                            'defect_area_y' => $validatedRequest['defectAreaPositionY'],
                            'status' => 'defect',
                            'created_by' => Auth::user()->line_id,
                            'created_by_username' => Auth::user()->username,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                    }
                } else {
                    array_push($secondaryOutExistArr, $secondaryInOutput->id);
                }
            }

            // Store Secondary OUT Defect Detail
            $secondaryOutStore = SewingSecondaryOutDefect::insert($secondaryOutInputArray);

            return array(
                "status" => 200,
                "message" => "Transaksi Selesai",
                "success" => count($secondaryOutInputArray),
                "fail" => $validatedRequest['qty'] - (count($secondaryOutInputArray) + count($secondaryOutExistArr)),
                "exist" => count($secondaryOutExistArr),
            );

        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
            "success" => 0,
            "fail" => $validatedRequest['qty'],
            "exist" => 0,
        );
    }

    public function submitSecondaryOutRework(Request $request) {
        $validatedRequest = $request->validate([
            'id' => 'required'
        ]);

        $id = $validatedRequest["id"];

        // Get Secondary Out Defect Detail
        $selectedSecondaryOut = SewingSecondaryOut::where("id", $id)->where("status", "defect")->first();

        if ($selectedSecondaryOut) {
            // Update Secondary Out Defect
            $updateSecondaryOut = SewingSecondaryOut::where("id", $selectedSecondaryOut->id)->update([
                "status" => "rework",
            ]);

            // Update Secondary Out Defect Detail
            $updateSecondaryOutDefect = SewingSecondaryOutDefect::where("secondary_out_id", $selectedSecondaryOut->id)->update([
                "status" => "reworked",
                "reworked_by" => Auth::user()->line_id,
                "reworked_by_username" => Auth::user()->username,
                "reworked_at" => Carbon::now(),
            ]);

            return array(
                "status" => 200,
                "message" => "Data defect ".$selectedSecondaryOut->id." berhasil di-rework",
            );
        } else {
            return array(
                "status" => 400,
                "message" => "Data defect tidak ditemukan",
            );
        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
        );
    }

    public function cancelSecondaryOutRework(Request $request) {
        $validatedRequest = $request->validate([
            'id' => 'required'
        ]);

        $id = $validatedRequest["id"];

        // Get Secondary Out Defect Detail
        $selectedSecondaryOut = SewingSecondaryOut::where("id", $id)->where("status", "rework")->first();

        if ($selectedSecondaryOut) {
            // Update Secondary Out Defect
            $updateSecondaryOut = SewingSecondaryOut::where("id", $selectedSecondaryOut->id)->update([
                "status" => "defect",
            ]);

            // Update Secondary Out Defect Detail
            $updateSecondaryOutDefect = SewingSecondaryOutDefect::where("secondary_out_id", $selectedSecondaryOut->id)->update([
                "status" => "defect",
                "reworked_by" => null,
                "reworked_by_username" => null,
                "reworked_at" => null,
            ]);

            return array(
                "status" => 200,
                "message" => "Data rework ".$selectedSecondaryOut->id." berhasil di-cancel",
            );
        } else {
            return array(
                "status" => 400,
                "message" => "Data rework tidak ditemukan",
            );
        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
        );
    }

    public function submitSecondaryOutReject(Request $request) {
        $validatedRequest = $request->validate([
            'selectedSecondary' => 'required',
            'sewingLine' => 'required',
            'worksheet' => 'required',
            'color' => 'required',
            'size' => 'required',
            'defectType' => 'required',
            'defectArea' => 'required',
            'defectAreaPositionX' => 'required',
            'defectAreaPositionY' => 'required',
            'qty' => 'required|gt:0',
        ],[
            'selectedSecondary.required' => 'Harap tentukan secondary <br>',
            'sewingLine.required' => 'Harap tentukan sewing line <br>',
            'worksheet.required' => 'Harap tentukan worksheet <br>',
            'color.required' => 'Harap tentukan color <br>',
            'size.required' => 'Harap tentukan size <br>',
            'defectType.required' => 'Harap tentukan Defect Type <br>',
            'defectArea.required' => 'Harap tentukan Defect Area <br>',
            'defectAreaPositionX.required' => 'Harap tentukan Defect Area Position X <br>',
            'defectAreaPositionY.required' => 'Harap tentukan Defect Area Position Y <br>',
            'qty.required' => 'Harap tentukan qty <br>',
            'qty.gt' => 'Minimal Qty : 1 <br>',
        ]);

        // Check Output Sewing
        $secondaryInOutputs = SewingSecondaryIn::selectRaw("
                output_secondary_in.id,
                output_rfts.so_det_id,
                output_secondary_master.secondary,
                act_costing.kpno ws,
                act_costing.styleno style,
                so_det.color,
                so_det.size,
                userpassword.username sewing_line
            ")->
            leftJoin("output_rfts", "output_rfts.id", "=", "output_secondary_in.rft_id")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("master_plan", "master_plan.id", "=", "output_rfts.master_plan_id")->
            leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
            leftJoin("output_secondary_master", "output_secondary_master.id", "=", "output_secondary_in.secondary_id")->
            leftJoin("output_secondary_out", "output_secondary_out.secondary_in_id", "=", "output_secondary_in.id")->
            whereNull("output_rfts.kode_numbering")->
            whereNull("output_secondary_out.id")->
            where("output_secondary_in.secondary_id", $validatedRequest['selectedSecondary'])->
            where("userpassword.username", $validatedRequest['sewingLine'])->
            where("act_costing.id", $validatedRequest['worksheet'])->
            where("so_det.color", $validatedRequest['color'])->
            where("so_det.size", $validatedRequest['size'])->
            limit($validatedRequest['qty'])->
            get();

        if ($secondaryInOutputs) {

            // Prepare Secondary OUT Array
            $secondaryOutInputArray = [];
            $secondaryOutExistArr = [];
            foreach ($secondaryInOutputs as $secondaryInOutput) {

                // Check Secondary Out Availibility
                $secondaryOut = SewingSecondaryOut::where("secondary_in_id", $secondaryInOutput->id)->first();
                if (!$secondaryOut) {

                    // Create Secondary Out Defect
                    $insertDefect = SewingSecondaryOut::create([
                        'kode_numbering' => $secondaryInOutput->kode_numbering,
                        'secondary_in_id' => $secondaryInOutput->id,
                        'status' => 'reject',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::user()->line_id,
                        'created_by_username' => Auth::user()->username
                    ]);

                    if ($insertDefect) {

                        // Prepare Secondary Out Defect Detail
                        array_push($secondaryOutInputArray, [
                            'secondary_out_id' => $insertDefect->id,
                            'defect_type_id' => $validatedRequest['defectType'],
                            'defect_area_id' => $validatedRequest['defectArea'],
                            'defect_area_x' => $validatedRequest['defectAreaPositionX'],
                            'defect_area_y' => $validatedRequest['defectAreaPositionY'],
                            'status' => 'mati',
                            'created_by' => Auth::user()->line_id,
                            'created_by_username' => Auth::user()->username,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);

                    }
                } else {
                    array_push($secondaryOutExistArr, $secondaryInOutput->id);
                }
            }

            // Store Secondary OUT Reject Detail
            $secondaryOutStore = SewingSecondaryOutReject::insert($secondaryOutInputArray);

            return array(
                "status" => 200,
                "message" => "Transaksi Selesai",
                "success" => count($secondaryOutInputArray),
                "fail" => $validatedRequest['qty'] - (count($secondaryOutInputArray) + count($secondaryOutExistArr)),
                "exist" => count($secondaryOutExistArr),
            );
        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
            "success" => 0,
            "fail" => $validatedRequest['qty'],
            "exist" => 0,
        );
    }

    public function submitSecondaryOutRejectDefect(Request $request) {
        $validatedRequest = $request->validate([
            'id' => 'required'
        ]);

        $id = $validatedRequest["id"];

        // Get Secondary Out Defect Detail
        $selectedSecondaryOut = SewingSecondaryOut::where("id", $id)->where("status", "defect")->first();

        if ($selectedSecondaryOut) {
            // Update Secondary Out Defect
            $updateSecondaryOut = SewingSecondaryOut::where("id", $selectedSecondaryOut->id)->update([
                "status" => "reject",
            ]);

            // Update Secondary Out Defect Detail
            $updateSecondaryOutDefect = SewingSecondaryOutDefect::where("secondary_out_id", $selectedSecondaryOut->id)->update([
                "status" => "rejected",
            ]);

            // Get Secondary Out
            $secondaryOutDefectDetail = SewingSecondaryOutDefect::where("secondary_out_id", $selectedSecondaryOut->id)->first();

            // Create Secondary OUT Reject Detail
            $insertReject = SewingSecondaryOutReject::create([
                "secondary_out_id" => $selectedSecondaryOut->id,
                'defect_type_id' => $secondaryOutDefectDetail->defect_type_id,
                'defect_area_id' => $secondaryOutDefectDetail->defect_area_id,
                'defect_area_x' => $secondaryOutDefectDetail->defect_area_x,
                'defect_area_y' => $secondaryOutDefectDetail->defect_area_y,
                'status' => 'defect',
                'created_by' => Auth::user()->line_id,
                'created_by_username' => Auth::user()->username,
            ]);

            return array(
                "status" => 200,
                "message" => "Data defect ".$selectedSecondaryOut->id." berhasil di-reject",
            );
        } else {
            return array(
                "status" => 400,
                "message" => "Data defect tidak ditemukan",
            );
        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
        );
    }


    public function cancelSecondaryOutRejectDefect(Request $request) {
        $validatedRequest = $request->validate([
            'id' => 'required'
        ]);

        $id = $validatedRequest["id"];

        // Get Secondary Out Reject
        $selectedSecondaryOut = SewingSecondaryOut::where("id", $id)->where("status", "reject")->first();

        if ($selectedSecondaryOut) {
            // Update Secondary Out Reject
            $updateSecondaryOut = SewingSecondaryOut::where("id", $selectedSecondaryOut->id)->update([
                "status" => "defect",
            ]);

            // Update Secondary Out Defect Detail
            $updateSecondaryOutDefect = SewingSecondaryOutDefect::where("secondary_out_id", $selectedSecondaryOut->id)->update([
                "status" => "defect",
            ]);

            // Delete Secondary OUT Reject Detail
            $deleteReject = SewingSecondaryOutReject::where("secondary_out_id", $selectedSecondaryOut->id)->delete();

            return array(
                "status" => 200,
                "message" => "Data reject ".$selectedSecondaryOut->id." berhasil di-cancel",
            );
        } else {
            return array(
                "status" => 400,
                "message" => "Data reject tidak ditemukan",
            );
        }

        return array(
            "status" => 400,
            "message" => "Data tidak ditemukan",
        );
    }

    public function exportSecondaryInOut(Request $request) {
        return Excel::download(new SecondaryOutExport($request->dateFrom, $request->dateTo, $request->selectedSecondary), 'Report Defect In Out.xlsx');
    }
}
