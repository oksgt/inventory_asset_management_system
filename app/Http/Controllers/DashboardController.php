<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BackupLog;
use App\Models\DcCategory;
use App\Models\DcComponent;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilan Utama Dashboard Pusat Data (DC-Ops)
     */
    public function index(): View
    {
        // 1. Metrik Komponen Pusat Data
        $totalComponents = DcComponent::count();
        $operationalCount = DcComponent::where('status', 'operational')->count();
        $warningCount = DcComponent::whereIn('status', ['warning', 'maintenance_required'])->count();
        $avgHealth = round(DcComponent::avg('condition_percent') ?? 100, 1);

        // 2. Metrik Suku Cadang dari MySQL View 'view_critical_spareparts'
        $criticalPartsCount = DB::table('view_critical_spareparts')->count();

        // 3. Status Backup Terkini
        $latestBackup = BackupLog::latest()->first();
        $verifiedBackupCount = BackupLog::where('status', 'verified')->count();

        // 4. Jadwal Pemeliharaan Mendatang (Eager Loading untuk cegah N+1)
        $upcomingSchedules = MaintenanceSchedule::with('component.category')
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get();

        // 5. Riwayat Pemeliharaan Terakhir
        $recentLogs = MaintenanceLog::with(['component', 'sparePartUsages.sparePart'])
            ->latest('performed_at')
            ->take(5)
            ->get();

        // 6. Data Komponen Kritis dari MySQL View 'view_component_maintenance_status'
        $criticalComponents = DB::table('view_component_maintenance_status')
            ->where('health_level', '!=', 'HEALTHY')
            ->take(6)
            ->get();

        // 7. Audit & Trigger Logs Terkini
        $recentAudits = AuditLog::latest('created_at')->take(6)->get();

        // 8. Breakdown Kategori
        $categoryStats = DcCategory::withCount('components')->get();

        return view('dashboard.index', compact(
            'totalComponents',
            'operationalCount',
            'warningCount',
            'avgHealth',
            'criticalPartsCount',
            'latestBackup',
            'verifiedBackupCount',
            'upcomingSchedules',
            'recentLogs',
            'criticalComponents',
            'recentAudits',
            'categoryStats'
        ));
    }
}
