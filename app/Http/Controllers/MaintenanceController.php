<?php

namespace App\Http\Controllers;

use App\Models\DcComponent;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use App\Models\SparePart;
use App\Services\MaintenanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'schedules');

        $schedules = MaintenanceSchedule::with('component.category')
            ->orderBy('scheduled_date', 'asc')
            ->paginate(10, ['*'], 'schedules_page');

        $logs = MaintenanceLog::with(['component.category', 'sparePartUsages.sparePart', 'schedule'])
            ->latest('performed_at')
            ->paginate(10, ['*'], 'logs_page');

        // Statistik bulanan dari MySQL View 'view_monthly_maintenance_summary'
        $monthlySummary = DB::table('view_monthly_maintenance_summary')->get();

        return view('maintenance.index', compact('activeTab', 'schedules', 'logs', 'monthlySummary'));
    }

    public function createSchedule(): View
    {
        $components = DcComponent::orderBy('rack_location')->orderBy('component_code')->get();

        return view('maintenance.create_schedule', compact('components'));
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'component_id' => ['required', 'exists:dc_components,id'],
            'title' => ['required', 'string', 'max:200'],
            'maintenance_type' => ['required', 'in:preventive,corrective,emergency'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'scheduled_date' => ['required', 'date'],
            'sop_reference' => ['required', 'string', 'max:255'],
            'assigned_technician' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'scheduled';
        $schedule = MaintenanceSchedule::create($validated);

        return redirect()->route('maintenance.index', ['tab' => 'schedules'])
            ->with('success', "Jadwal pemeliharaan [{$schedule->title}] berhasil direncanakan.");
    }

    public function createLog(Request $request): View
    {
        $selectedScheduleId = $request->query('schedule_id');
        $selectedSchedule = $selectedScheduleId ? MaintenanceSchedule::with('component')->find($selectedScheduleId) : null;

        $components = DcComponent::orderBy('rack_location')->orderBy('component_code')->get();
        $spareParts = SparePart::where('stock_quantity', '>', 0)->orderBy('name')->get();

        return view('maintenance.create_log', compact('components', 'spareParts', 'selectedSchedule'));
    }

    /**
     * Menyimpan Log Pemeliharaan dengan Opsi Eksekusi:
     * 1. Via MySQL Stored Procedure (sp_record_maintenance_activity)
     * 2. Via Managed DB Transaction (Commit / Rollback)
     */
    public function storeLog(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'execution_mode' => ['required', 'in:procedure,transaction,rollback_demo'],
            'component_id' => ['required', 'exists:dc_components,id'],
            'schedule_id' => ['nullable', 'exists:maintenance_schedules,id'],
            'title' => ['required', 'string', 'max:200'],
            'technician_name' => ['required', 'string', 'max:100'],
            'action_type' => ['required', 'in:preventive,corrective,emergency,part_replacement'],
            'actions_taken' => ['required', 'string'],
            'findings' => ['nullable', 'string'],
            'health_after' => ['required', 'integer', 'min:1', 'max:100'],
            'spare_part_id' => ['nullable', 'exists:spare_parts,id'],
            'quantity_used' => ['nullable', 'integer', 'min:1'],
        ]);

        $component = DcComponent::findOrFail($validated['component_id']);
        $partId = ! empty($validated['spare_part_id']) ? (int) $validated['spare_part_id'] : null;
        $partQty = ! empty($validated['quantity_used']) ? (int) $validated['quantity_used'] : null;

        if ($validated['execution_mode'] === 'procedure') {
            // EKSEKUSI VIA MYSQL STORED PROCEDURE (KUK 5.1 & 5.2)
            $result = $this->maintenanceService->recordViaStoredProcedure(
                componentId: $component->id,
                scheduleId: ! empty($validated['schedule_id']) ? (int) $validated['schedule_id'] : null,
                title: $validated['title'],
                techName: $validated['technician_name'],
                actionType: $validated['action_type'],
                actionsTaken: $validated['actions_taken'],
                findings: $validated['findings'] ?? '',
                healthBefore: $component->condition_percent,
                healthAfter: (int) $validated['health_after'],
                partId: $partId,
                partQty: $partQty
            );

            if (! $result['success']) {
                return back()->withInput()->with('error', "Stored Procedure Error [{$result['status_code']}]: {$result['message']}");
            }

            return redirect()->route('maintenance.index', ['tab' => 'logs'])
                ->with('success', "BERHASIL VIA MYSQL STORED PROCEDURE: {$result['message']} (Status Code: {$result['status_code']})");
        }

        if ($validated['execution_mode'] === 'rollback_demo') {
            // EKSEKUSI DEMO PEMBATALAN TRANSAKSI DENGAN ROLLBACK (KUK 8.2)
            $result = $this->maintenanceService->recordWithTransaction($validated, forceFail: true);

            return redirect()->route('maintenance.index', ['tab' => 'logs'])
                ->with('warning', "DEMO ROLLBACK BERHASIL: {$result['message']}. Silakan periksa tabel audit_logs untuk melihat pencatatan kegagalan atomik.");
        }

        // EKSEKUSI TRANSAKSI STANDAR DENGAN COMMIT (KUK 8.1)
        $result = $this->maintenanceService->recordWithTransaction($validated, forceFail: false);

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()->route('maintenance.index', ['tab' => 'logs'])
            ->with('success', "BERHASIL VIA TRANSAKSI COMMIT: {$result['message']}");
    }

    public function showLog(MaintenanceLog $log): View
    {
        $log->load(['component.category', 'schedule', 'sparePartUsages.sparePart']);

        return view('maintenance.show_log', compact('log'));
    }
}
