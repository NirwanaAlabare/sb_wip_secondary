<?php

namespace App\Exports;

use App\Models\SignalBit\SewingSecondaryIn;
use App\Models\SignalBit\Defect;
use App\Models\SignalBit\DefectInOut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SecondaryInOutExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $dateFrom, $dateTo, $selectedSecondary;

    public function __construct($dateFrom, $dateTo, $selectedSecondary)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->selectedSecondary = $selectedSecondary;
    }

    public function view(): View
    {
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
                    output_secondary_in.created_at between '".$this->dateFrom." 00:00:00' and '".$this->dateTo." 23:59:59'
                    OR
                    output_secondary_out.created_at between '".$this->dateFrom." 00:00:00' and '".$this->dateTo." 23:59:59'
                )
            ")->
            whereRaw("
                (
                    output_secondary_in.id IS NOT NULL AND
                    output_rfts.id IS NOT NULL AND
                    output_secondary_master.id = '".$this->selectedSecondary."'
                )
            ")->
            groupBy("output_secondary_in.id")->
            get();

        return view('exports.secondary-in-out', [
            'secondaryInOut' => $secondaryInOutQuery
        ]);
    }
}
