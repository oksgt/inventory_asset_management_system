<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DcComponent;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use App\Models\SparePart;
use App\Models\SparePartUsage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    /**
     * Memanggil MySQL Stored Procedure sp_record_maintenance_activity
     * Memenuhi KUK: Membuat stored procedure & Menguji prosedur diperiksa input dan outputnya.
     *
     * @return array{success: bool, log_id: int, status_code: string, message: string}
     */
    public function recordViaStoredProcedure(
        int $componentId,
        ?int $scheduleId,
        string $title,
        string $techName,
        string $actionType,
        string $actionsTaken,
        ?string $findings,
        int $healthBefore,
        int $healthAfter,
        ?int $partId = null,
        ?int $partQty = null
    ): array {
        // Eksekusi CALL dengan prepared statement PDO untuk menangkap OUT parameter
        $pdo = DB::getPdo();

        // Panggil procedure dengan session variables untuk menangkap output
        $stmt = $pdo->prepare('
            CALL sp_record_maintenance_activity(
                :comp_id, :sched_id, :title, :tech, :action_type, 
                :actions, :findings, :health_before, :health_after, 
                :part_id, :part_qty, 
                @p_new_log_id, @p_status_code, @p_message
            )
        ');

        $stmt->execute([
            ':comp_id' => $componentId,
            ':sched_id' => $scheduleId,
            ':title' => $title,
            ':tech' => $techName,
            ':action_type' => $actionType,
            ':actions' => $actionsTaken,
            ':findings' => $findings ?? '',
            ':health_before' => $healthBefore,
            ':health_after' => $healthAfter,
            ':part_id' => $partId,
            ':part_qty' => $partQty,
        ]);
        $stmt->closeCursor();

        // Ambil nilai OUT parameters dari session
        $output = $pdo->query('SELECT @p_new_log_id AS log_id, @p_status_code AS status_code, @p_message AS message')->fetch(\PDO::FETCH_ASSOC);

        $logId = (int) ($output['log_id'] ?? 0);
        $statusCode = (string) ($output['status_code'] ?? 'UNKNOWN');
        $message = (string) ($output['message'] ?? '');

        return [
            'success' => $statusCode === 'SUCCESS',
            'log_id' => $logId,
            'status_code' => $statusCode,
            'message' => $message,
        ];
    }

    /**
     * Pencatatan Pemeliharaan Berbasis Transaksi Terkelola (Commit & Rollback)
     * Memenuhi KUK: Melakukan perintah commit dan rollback
     *
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string, log_id?: int}
     */
    public function recordWithTransaction(array $data, bool $forceFail = false): array
    {
        DB::beginTransaction();

        try {
            $component = DcComponent::findOrFail($data['component_id']);

            $log = MaintenanceLog::create([
                'component_id' => $component->id,
                'schedule_id' => $data['schedule_id'] ?? null,
                'title' => $data['title'],
                'performed_at' => Carbon::now(),
                'technician_name' => $data['technician_name'],
                'action_type' => $data['action_type'] ?? 'preventive',
                'actions_taken' => $data['actions_taken'],
                'findings' => $data['findings'] ?? null,
                'health_before' => $component->condition_percent,
                'health_after' => $data['health_after'] ?? 95,
                'total_cost' => 0.00,
                'status' => 'success',
            ]);

            $totalCost = 0.00;

            // Pengurangan stok suku cadang
            if (! empty($data['spare_part_id']) && ! empty($data['quantity_used'])) {
                $sparePart = SparePart::lockForUpdate()->findOrFail($data['spare_part_id']);

                if ($sparePart->stock_quantity < $data['quantity_used']) {
                    throw new Exception("Stok suku cadang [{$sparePart->part_code}] {$sparePart->name} tidak mencukupi (Tersisa: {$sparePart->stock_quantity}, Dibutuhkan: {$data['quantity_used']}).");
                }

                $sparePart->decrement('stock_quantity', (int) $data['quantity_used']);

                $cost = $sparePart->unit_cost * (int) $data['quantity_used'];
                $totalCost += $cost;

                SparePartUsage::create([
                    'maintenance_log_id' => $log->id,
                    'spare_part_id' => $sparePart->id,
                    'quantity_used' => (int) $data['quantity_used'],
                    'unit_cost' => $sparePart->unit_cost,
                    'notes' => 'Pengurangan stok transaksional commit/rollback demo',
                ]);
            }

            $log->update(['total_cost' => $totalCost]);

            // Perbarui kondisi dan jadwal komponen
            $component->update([
                'condition_percent' => $data['health_after'] ?? 95,
                'status' => ($data['health_after'] ?? 95) >= 80 ? 'operational' : 'warning',
                'last_maintenance_at' => Carbon::now(),
                'next_maintenance_at' => Carbon::now()->addDays(90),
            ]);

            if (! empty($data['schedule_id'])) {
                MaintenanceSchedule::where('id', $data['schedule_id'])->update(['status' => 'completed']);
            }

            // Simulasi kegagalan untuk menguji perintah ROLLBACK
            if ($forceFail) {
                throw new Exception('SIMULASI ROLLBACK: Terjadi kegagalan integritas daya pada sensor (Sengaja dipicu untuk demonstrasi pembatalan transaksi).');
            }

            // Catat audit commit berhasil
            AuditLog::create([
                'event_type' => 'TRANSACTION_COMMIT',
                'table_name' => 'maintenance_logs',
                'record_id' => $log->id,
                'message' => "Operasi COMMIT berhasil untuk pemeliharaan [{$log->title}] pada komponen {$component->component_code}.",
                'payload_json' => [
                    'log_id' => $log->id,
                    'component_id' => $component->id,
                    'total_cost' => $totalCost,
                ],
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Transaksi berhasil di-COMMIT. Data pemeliharaan dan stok suku cadang tersimpan konsisten (Log ID #{$log->id}).",
                'log_id' => $log->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            // Catat audit rollback
            AuditLog::create([
                'event_type' => 'TRANSACTION_ROLLBACK',
                'table_name' => 'maintenance_logs',
                'record_id' => null,
                'message' => "Operasi ROLLBACK dieksekusi: {$e->getMessage()}",
                'payload_json' => [
                    'component_id' => $data['component_id'] ?? null,
                    'error' => $e->getMessage(),
                ],
            ]);

            return [
                'success' => false,
                'message' => 'Transaksi di-ROLLBACK secara penuh. Tidak ada perubahan yang disimpan ke database: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Memanggil Fungsi Database MySQL fn_calculate_component_health & fn_get_component_maintenance_cost
     */
    public function getComponentSqlMetrics(int $componentId): array
    {
        $health = DB::selectOne('SELECT fn_calculate_component_health(?) AS health', [$componentId]);
        $cost = DB::selectOne('SELECT fn_get_component_maintenance_cost(?) AS cost', [$componentId]);

        return [
            'calculated_health' => (int) ($health->health ?? 0),
            'total_maintenance_cost' => (float) ($cost->cost ?? 0.0),
        ];
    }
}
