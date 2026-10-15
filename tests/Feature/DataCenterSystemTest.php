<?php

namespace Tests\Feature;

use App\Models\BackupLog;
use App\Models\DcComponent;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DataCenterSystemTest extends TestCase
{
    /**
     * Uji Halaman Utama Dashboard DC-Ops Dapat Dimuat
     */
    public function test_dashboard_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('DC-OPS');
        $response->assertSee('Pusat Manajemen Infrastruktur');
    }

    /**
     * Uji Modul Komponen Pusat Data (Index & Show)
     */
    public function test_components_index_and_detail_load(): void
    {
        $response = $this->get('/components');
        $response->assertStatus(200);
        $response->assertSee('Komponen Pusat Data');

        $component = DcComponent::first();
        if ($component) {
            $showResponse = $this->get("/components/{$component->id}");
            $showResponse->assertStatus(200);
            $showResponse->assertSee($component->component_code);
        }
    }

    /**
     * Uji Modul Suku Cadang & View Kritis MySQL
     */
    public function test_spare_parts_module_and_critical_view(): void
    {
        $response = $this->get('/spareparts');
        $response->assertStatus(200);
        $response->assertSee('Inventaris Suku Cadang');

        $criticalResponse = $this->get('/spareparts?critical_only=1');
        $criticalResponse->assertStatus(200);
    }

    /**
     * Uji Modul Pemeliharaan & Eksekusi Stored Procedure
     */
    public function test_maintenance_module_and_stored_procedure_execution(): void
    {
        $response = $this->get('/maintenance');
        $response->assertStatus(200);
        $response->assertSee('Jadwal Perencanaan Pemeliharaan');

        $component = DcComponent::first();
        $this->assertNotNull($component);

        // Eksekusi Stored Procedure via Endpoint API Lab
        $spResponse = $this->postJson('/skkni-compliance/api/test-procedure', [
            'component_id' => $component->id,
            'title' => 'Uji Otomatis Stored Procedure Test Suite',
            'technician_name' => 'PHPUnit Test Runner',
            'action_type' => 'preventive',
            'actions_taken' => 'Pembersihan otomatis test suite',
            'findings' => 'Semua sensor normal',
            'health_after' => 97,
        ]);

        $spResponse->assertStatus(200);
        $spResponse->assertJsonFragment(['success' => true]);
        $spResponse->assertJsonFragment(['status_code' => 'SUCCESS']);
    }

    /**
     * Uji Modul Backup & Uji Restorasi Berkala
     */
    public function test_backup_creation_and_restore_verification(): void
    {
        $response = $this->get('/backup');
        $response->assertStatus(200);
        $response->assertSee('Backup & Pemulihan Bencana', false);

        // Buat backup baru
        $createResponse = $this->post('/backup/create', [
            'backup_type' => 'incremental',
            'storage_location' => 'primary_local',
            'encrypt' => '1',
        ]);
        $createResponse->assertRedirect('/backup');

        $latestBackup = BackupLog::latest()->first();
        $this->assertNotNull($latestBackup);
        $this->assertEquals('incremental', $latestBackup->backup_type);
        $this->assertTrue($latestBackup->is_encrypted);

        // Jalankan pengujian restorasi
        $restoreTestResponse = $this->post("/backup/{$latestBackup->id}/test-restore");
        $restoreTestResponse->assertRedirect('/backup');

        $latestBackup->refresh();
        $this->assertEquals('verified', $latestBackup->status);
    }

    /**
     * Uji Modul Laporan & Pratinjau Cetak Resmi
     */
    public function test_reports_index_and_print_view(): void
    {
        $response = $this->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Laporan Perencanaan & Pemeliharaan', false);

        $printResponse = $this->get('/reports/print');
        $printResponse->assertStatus(200);
        $printResponse->assertSee('LEMBAR PENGESAHAN DOKUMEN PEMELIHARAAN');
    }

    /**
     * Uji Modul Kepatuhan SKKNI & Live Benchmark
     */
    public function test_skkni_compliance_hub_and_benchmarks(): void
    {
        $response = $this->get('/skkni-compliance');
        $response->assertStatus(200);
        $response->assertSee('Pusat Kepatuhan', false);
        $response->assertSee('Laboratorium Teknis SKKNI', false);

        // Benchmark Hash Map
        $benchHash = $this->postJson('/skkni-compliance/api/benchmark', ['type' => 'hash_vs_linear']);
        $benchHash->assertStatus(200);
        $benchHash->assertJsonStructure(['linear_search', 'hash_map', 'speedup_factor']);

        // Benchmark Binary Search
        $benchBinary = $this->postJson('/skkni-compliance/api/benchmark', ['type' => 'binary_vs_linear']);
        $benchBinary->assertStatus(200);
        $benchBinary->assertJsonStructure(['linear_search', 'binary_search', 'speedup_factor']);

        // Priority Queue
        $benchQueue = $this->postJson('/skkni-compliance/api/benchmark', ['type' => 'priority_queue']);
        $benchQueue->assertStatus(200);
        $benchQueue->assertJsonStructure(['input_order', 'dispatched_execution_order']);
    }

    /**
     * Uji MySQL Views, Stored Functions & Trigger
     */
    public function test_mysql_views_and_functions_integrity(): void
    {
        // 1. View Component
        $viewCompCount = DB::table('view_component_maintenance_status')->count();
        $this->assertGreaterThan(0, $viewCompCount);

        // 2. View Critical Parts
        $viewCriticalCount = DB::table('view_critical_spareparts')->count();
        $this->assertGreaterThanOrEqual(0, $viewCriticalCount);

        // 3. Stored Function
        $comp = DcComponent::first();
        $healthResult = DB::selectOne('SELECT fn_calculate_component_health(?) AS health', [$comp->id]);
        $this->assertNotNull($healthResult);
        $this->assertGreaterThan(0, (int) $healthResult->health);
    }
}
