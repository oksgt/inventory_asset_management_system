<?php

namespace App\Http\Controllers;

use App\Models\DcComponent;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->input('period', Carbon::now()->format('Y-m'));

        $components = DcComponent::with('category')->get();
        $criticalSpareParts = DB::table('view_critical_spareparts')->get();
        $schedules = MaintenanceSchedule::with('component')->where('scheduled_date', 'like', "{$period}%")->get();
        $logs = MaintenanceLog::with(['component', 'sparePartUsages.sparePart'])->where('performed_at', 'like', "{$period}%")->get();

        $monthlySummary = DB::table('view_monthly_maintenance_summary')->get();
        $totalCostMonth = $logs->sum('total_cost');

        return view('reports.index', compact(
            'period',
            'components',
            'criticalSpareParts',
            'schedules',
            'logs',
            'monthlySummary',
            'totalCostMonth'
        ));
    }

    /**
     * Tampilan Cetak / PDF Format Resmi Organisasi (KUK 3.1 & 3.2)
     */
    public function printView(Request $request): View
    {
        $period = $request->input('period', Carbon::now()->format('Y-m'));
        $dateFormatted = Carbon::parse($period.'-01')->translatedFormat('F Y');

        $components = DcComponent::with('category')->orderBy('rack_location')->get();
        $criticalSpareParts = DB::table('view_critical_spareparts')->get();
        $schedules = MaintenanceSchedule::with('component')->orderBy('scheduled_date')->get();
        $logs = MaintenanceLog::with(['component', 'sparePartUsages.sparePart'])->latest('performed_at')->get();

        $totalEstimatedBudget = $criticalSpareParts->sum('estimated_reorder_budget');
        $totalRealizedCost = $logs->sum('total_cost');

        return view('reports.print', compact(
            'period',
            'dateFormatted',
            'components',
            'criticalSpareParts',
            'schedules',
            'logs',
            'totalEstimatedBudget',
            'totalRealizedCost'
        ));
    }
}
