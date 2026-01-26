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

class GeneralController extends Controller
{
    public function index(Request $request) {
        $userData = Auth::user();

        $redirect = $userData->Groupp == 'SECONDARYSEWINGIN' ? url('/in') : ($userData->Groupp == 'SECONDARYSEWINGOUT' ? url('/out') : url('/in'));

        return redirect()->to($redirect);
    }

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

    public function getMasterPlanSize(Request $request) {
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

    public function getColor(Request $request) {
        $colors = DB::connection("mysql_sb")->select("select color from so_det left join so on so.id = so_det.id_so left join act_costing on act_costing.id = so.id_cost where act_costing.id = '" . $request->worksheet . "' group by color");

        return $colors ? $colors : null;
    }

    public function getSize(Request $request) {
        $sizes = DB::connection("mysql_sb")->table("so_det")->selectRaw("
                so_det.id as so_det_id,
                act_costing.kpno no_ws,
                so_det.color,
                so_det.size,
                so_det.dest,
                (CASE WHEN so_det.dest IS NOT NULL AND so_det.dest != '-' THEN CONCAT(so_det.size, ' - ', so_det.dest) ELSE so_det.size END) size_dest
            ")->
            leftJoin("so", "so.id", "=", "so_det.id_so")->
            leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
            leftJoin("master_size_new", "master_size_new.size", "=", "so_det.size")->
            where("act_costing.id", $request->worksheet)->
            where("so_det.color", $request->color)->
            groupBy("act_costing.kpno", "so_det.color", "so_det.size")->
            orderBy("master_size_new.urutan")->
            get();

        return $sizes ? $sizes : null;
    }

    public function getSewingQty(Request $request) {
        $outputs = DB::connection("mysql_sb")->table("output_rfts")->selectRaw("
                COUNT(output_rfts.id) total_rft
            ")->
            leftJoin("so_det", "so_det.id", "=", "output_rfts.so_det_id")->
            leftJoin("so", "so.id", "=", "so_det.id_so")->
            leftJoin("act_costing", "act_costing.id", "=", "so.id_cost")->
            leftJoin("user_sb_wip", "user_sb_wip.id", "=", "output_rfts.created_by")->
            leftJoin("userpassword", "userpassword.line_id", "=", "user_sb_wip.line_id")->
            leftJoin("output_secondary_in", "output_secondary_in.rft_id", "=", "output_rfts.id")->
            whereRaw("
                output_secondary_in.id is null and
                output_rfts.kode_numbering is null and
                act_costing.id = '".$request->worksheet."' and
                so_det.color = '".$request->color."' and
                so_det.size = '".$request->size."'
                ".($request->sewingLine ? " and userpassword.username = '".$request->sewingLine."' " : "")."
            ")->
            first();

        return $outputs ? $outputs->total_rft : 0;
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
}
