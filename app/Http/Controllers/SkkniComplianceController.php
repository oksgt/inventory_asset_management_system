<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DcComponent;
use App\Models\SparePart;
use App\Services\BackupService;
use App\Services\DataStructureBenchmarkService;
use App\Services\MaintenanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkkniComplianceController extends Controller
{
    public function __construct(
        protected DataStructureBenchmarkService $benchmarkService,
        protected MaintenanceService $maintenanceService,
        protected BackupService $backupService
    ) {}

    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'overview');

        $frameworkMatrix = $this->benchmarkService->getFrameworkAnalysisMatrix();
        $dataStructureMatrix = $this->benchmarkService->getDataStructureComparisonMatrix();
        $drSpecs = $this->backupService->getDisasterRecoverySpecifications();

        $components = DcComponent::orderBy('component_code')->get();
        $spareParts = SparePart::orderBy('name')->get();
        $recentAuditLogs = AuditLog::latest('created_at')->take(10)->get();

        // Sample data for SQL Views
        $viewComponentStatus = DB::table('view_component_maintenance_status')->take(5)->get();
        $viewCriticalParts = DB::table('view_critical_spareparts')->take(5)->get();
        $viewMonthlySummary = DB::table('view_monthly_maintenance_summary')->take(5)->get();

        // Database Indexes query
        $indexes = DB::select("
            SHOW INDEX FROM dc_components WHERE Key_name != 'PRIMARY'
        ");

        return view('skkni.index', compact(
            'activeTab',
            'frameworkMatrix',
            'dataStructureMatrix',
            'drSpecs',
            'components',
            'spareParts',
            'recentAuditLogs',
            'viewComponentStatus',
            'viewCriticalParts',
            'viewMonthlySummary',
            'indexes'
        ));
    }

    /**
     * Endpoint API untuk Menjalankan Benchmark Struktur Data secara Live
     */
    public function runBenchmark(Request $request): JsonResponse
    {
        $type = $request->input('type', 'hash_vs_linear');

        if ($type === 'hash_vs_linear') {
            $result = $this->benchmarkService->benchmarkHashMapVsLinearSearch();

            return response()->json($result);
        }

        if ($type === 'binary_vs_linear') {
            $result = $this->benchmarkService->benchmarkBinaryVsLinear();

            return response()->json($result);
        }

        if ($type === 'priority_queue') {
            $result = $this->benchmarkService->demonstratePriorityQueue();

            return response()->json($result);
        }

        return response()->json(['error' => 'Tipe benchmark tidak valid'], 400);
    }

    /**
     * Endpoint API untuk Live Test Stored Procedure
     */
    public function testStoredProcedure(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'component_id' => ['required', 'exists:dc_components,id'],
            'title' => ['required', 'string'],
            'technician_name' => ['required', 'string'],
            'action_type' => ['required', 'string'],
            'actions_taken' => ['required', 'string'],
            'findings' => ['nullable', 'string'],
            'health_after' => ['required', 'integer'],
            'part_id' => ['nullable', 'exists:spare_parts,id'],
            'part_qty' => ['nullable', 'integer'],
        ]);

        $comp = DcComponent::findOrFail($validated['component_id']);

        $res = $this->maintenanceService->recordViaStoredProcedure(
            componentId: $comp->id,
            scheduleId: null,
            title: $validated['title'],
            techName: $validated['technician_name'],
            actionType: $validated['action_type'],
            actionsTaken: $validated['actions_taken'],
            findings: $validated['findings'] ?? '',
            healthBefore: $comp->condition_percent,
            healthAfter: (int) $validated['health_after'],
            partId: ! empty($validated['part_id']) ? (int) $validated['part_id'] : null,
            partQty: ! empty($validated['part_qty']) ? (int) $validated['part_qty'] : null
        );

        return response()->json($res);
    }

    /**
     * Endpoint API untuk Live Test Function
     */
    public function testStoredFunction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'component_id' => ['required', 'exists:dc_components,id'],
        ]);

        $metrics = $this->maintenanceService->getComponentSqlMetrics((int) $validated['component_id']);

        return response()->json($metrics);
    }

    /**
     * Endpoint API untuk Live Test Transaksi (Commit vs Rollback)
     */
    public function testTransactionSimulation(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'commit');
        $comp = DcComponent::first();
        $part = SparePart::where('stock_quantity', '>', 0)->first();

        $payload = [
            'component_id' => $comp->id,
            'title' => 'Simulasi Transaksi Uji Coba: '.strtoupper($mode),
            'technician_name' => 'Automated QA Engineer',
            'action_type' => 'preventive',
            'actions_taken' => 'Uji atomisitas transaksi MySQL InnoDB',
            'health_after' => 98,
            'spare_part_id' => $part ? $part->id : null,
            'quantity_used' => 1,
        ];

        if ($mode === 'rollback') {
            $res = $this->maintenanceService->recordWithTransaction($payload, forceFail: true);
        } else {
            $res = $this->maintenanceService->recordWithTransaction($payload, forceFail: false);
        }

        $latestAudit = AuditLog::latest('created_at')->first();

        return response()->json([
            'result' => $res,
            'latest_audit' => $latestAudit,
        ]);
    }
}
