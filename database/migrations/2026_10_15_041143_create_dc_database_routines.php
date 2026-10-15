<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bangkitkan Indeks Tambahan untuk Optimalisasi Performa Query
        DB::statement('CREATE INDEX idx_comp_status ON dc_components (status)');
        DB::statement('CREATE INDEX idx_comp_rack ON dc_components (rack_location, rack_unit)');
        DB::statement('CREATE INDEX idx_sp_stock_alert ON spare_parts (stock_quantity, min_stock_alert)');
        DB::statement('CREATE INDEX idx_maint_sched_date ON maintenance_schedules (scheduled_date, status)');
        DB::statement('CREATE INDEX idx_backup_status ON backup_logs (status, backup_type)');

        // 2. Membentuk Database Views
        // View 1: Status Pemeliharaan & Kesehatan Komponen DC
        DB::statement("
            CREATE OR REPLACE VIEW view_component_maintenance_status AS
            SELECT 
                c.id,
                c.component_code,
                c.name AS component_name,
                c.model,
                c.rack_location,
                c.rack_unit,
                c.status,
                c.condition_percent,
                cat.name AS category_name,
                c.last_maintenance_at,
                c.next_maintenance_at,
                DATEDIFF(c.next_maintenance_at, CURDATE()) AS days_to_maintenance,
                CASE 
                    WHEN c.condition_percent < 50 OR c.status = 'warning' THEN 'CRITICAL'
                    WHEN c.condition_percent < 80 OR DATEDIFF(c.next_maintenance_at, CURDATE()) <= 7 THEN 'WARNING'
                    ELSE 'HEALTHY'
                END AS health_level
            FROM dc_components c
            JOIN dc_categories cat ON c.category_id = cat.id
        ");

        // View 2: Suku Cadang Kritis (Stok Di Bawah Batas Minimum)
        DB::statement("
            CREATE OR REPLACE VIEW view_critical_spareparts AS
            SELECT 
                sp.id,
                sp.part_code,
                sp.name AS part_name,
                sp.part_number,
                cat.name AS category_name,
                sp.stock_quantity,
                sp.min_stock_alert,
                (sp.min_stock_alert - sp.stock_quantity) AS stock_deficit,
                sp.unit,
                sp.unit_cost,
                (sp.min_stock_alert * sp.unit_cost) AS estimated_reorder_budget,
                sp.location_bin,
                CASE 
                    WHEN sp.stock_quantity <= 0 THEN 'OUT_OF_STOCK'
                    WHEN sp.stock_quantity <= sp.min_stock_alert THEN 'LOW_STOCK'
                    ELSE 'SUFFICIENT'
                END AS stock_status
            FROM spare_parts sp
            JOIN dc_categories cat ON sp.category_id = cat.id
            WHERE sp.stock_quantity <= sp.min_stock_alert
        ");

        // View 3: Ringkasan Pemeliharaan Bulanan
        DB::statement("
            CREATE OR REPLACE VIEW view_monthly_maintenance_summary AS
            SELECT 
                DATE_FORMAT(ml.performed_at, '%Y-%m') AS period_month,
                COUNT(ml.id) AS total_activities,
                SUM(ml.total_cost) AS total_cost_incurred,
                AVG(ml.health_after) AS avg_health_achieved,
                COUNT(DISTINCT ml.component_id) AS distinct_components_serviced
            FROM maintenance_logs ml
            GROUP BY DATE_FORMAT(ml.performed_at, '%Y-%m')
        ");

        // 3. Stored Functions
        // Function 1: Kalkulasi Skor Kesehatan Komponen Berdasarkan Usia & Pemeliharaan Terakhir
        DB::unprepared('
            DROP FUNCTION IF EXISTS fn_calculate_component_health;
            CREATE FUNCTION fn_calculate_component_health(p_component_id INT) 
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_current_cond INT DEFAULT 100;
                DECLARE v_days_since_maint INT DEFAULT 0;
                DECLARE v_health_score INT DEFAULT 100;
                
                SELECT condition_percent, COALESCE(DATEDIFF(NOW(), last_maintenance_at), 90)
                INTO v_current_cond, v_days_since_maint
                FROM dc_components
                WHERE id = p_component_id;
                
                IF v_days_since_maint > 180 THEN
                    SET v_health_score = GREATEST(10, v_current_cond - 30);
                ELSEIF v_days_since_maint > 90 THEN
                    SET v_health_score = GREATEST(20, v_current_cond - 15);
                ELSE
                    SET v_health_score = v_current_cond;
                END IF;
                
                RETURN v_health_score;
            END;
        ');

        // Function 2: Total Biaya Pemeliharaan Suatu Komponen
        DB::unprepared('
            DROP FUNCTION IF EXISTS fn_get_component_maintenance_cost;
            CREATE FUNCTION fn_get_component_maintenance_cost(p_component_id INT) 
            RETURNS DECIMAL(14,2)
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(14,2) DEFAULT 0.00;
                
                SELECT COALESCE(SUM(total_cost), 0.00)
                INTO v_total
                FROM maintenance_logs
                WHERE component_id = p_component_id;
                
                RETURN v_total;
            END;
        ');

        // 4. Stored Procedure
        // Prosedur Transaksional: Mencatat Pemeliharaan & Pemakaian Suku Cadang Terintegrasi
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_record_maintenance_activity;
            CREATE PROCEDURE sp_record_maintenance_activity(
                IN p_component_id INT,
                IN p_schedule_id INT,
                IN p_title VARCHAR(200),
                IN p_tech_name VARCHAR(100),
                IN p_action_type VARCHAR(50),
                IN p_actions_taken TEXT,
                IN p_findings TEXT,
                IN p_health_before INT,
                IN p_health_after INT,
                IN p_part_id INT,
                IN p_part_qty INT,
                OUT p_new_log_id INT,
                OUT p_status_code VARCHAR(30),
                OUT p_message VARCHAR(255)
            )
            proc_label: BEGIN
                DECLARE v_comp_count INT DEFAULT 0;
                DECLARE v_part_stock INT DEFAULT 0;
                DECLARE v_part_cost DECIMAL(14,2) DEFAULT 0.00;
                DECLARE v_total_maint_cost DECIMAL(14,2) DEFAULT 0.00;

                -- Validasi keberadaan komponen
                SELECT COUNT(*) INTO v_comp_count FROM dc_components WHERE id = p_component_id;
                IF v_comp_count = 0 THEN
                    SET p_new_log_id = 0;
                    SET p_status_code = 'ERR_NOT_FOUND';
                    SET p_message = 'Komponen Pusat Data tidak ditemukan.';
                    LEAVE proc_label;
                END IF;

                -- Validasi suku cadang jika disertakan
                IF p_part_id IS NOT NULL AND p_part_qty > 0 THEN
                    SELECT stock_quantity, unit_cost INTO v_part_stock, v_part_cost 
                    FROM spare_parts WHERE id = p_part_id;
                    
                    IF v_part_stock < p_part_qty THEN
                        SET p_new_log_id = 0;
                        SET p_status_code = 'ERR_INSUFFICIENT_STOCK';
                        SET p_message = CONCAT('Stok suku cadang tidak mencukupi. Tersedia: ', v_part_stock, ', Diminta: ', p_part_qty);
                        LEAVE proc_label;
                    END IF;
                    
                    SET v_total_maint_cost = v_part_cost * p_part_qty;
                END IF;

                -- 1. Insert Log Pemeliharaan
                INSERT INTO maintenance_logs (
                    component_id, schedule_id, title, performed_at, technician_name,
                    action_type, actions_taken, findings, health_before, health_after,
                    total_cost, status, created_at, updated_at
                ) VALUES (
                    p_component_id, p_schedule_id, p_title, NOW(), p_tech_name,
                    p_action_type, p_actions_taken, p_findings, p_health_before, p_health_after,
                    v_total_maint_cost, 'success', NOW(), NOW()
                );
                
                SET p_new_log_id = LAST_INSERT_ID();

                -- 2. Jika ada pemakaian suku cadang, catat dan kurangi stok
                IF p_part_id IS NOT NULL AND p_part_qty > 0 THEN
                    INSERT INTO spare_part_usages (
                        maintenance_log_id, spare_part_id, quantity_used, unit_cost, notes, created_at, updated_at
                    ) VALUES (
                        p_new_log_id, p_part_id, p_part_qty, v_part_cost, 'Digunakan via Stored Procedure sp_record_maintenance_activity', NOW(), NOW()
                    );
                    
                    UPDATE spare_parts 
                    SET stock_quantity = stock_quantity - p_part_qty, updated_at = NOW()
                    WHERE id = p_part_id;
                END IF;

                -- 3. Update Status & Kondisi Komponen
                UPDATE dc_components
                SET 
                    condition_percent = p_health_after,
                    status = IF(p_health_after >= 80, 'operational', 'warning'),
                    last_maintenance_at = NOW(),
                    next_maintenance_at = DATE_ADD(NOW(), INTERVAL 90 DAY),
                    updated_at = NOW()
                WHERE id = p_component_id;

                -- 4. Update Jadwal jika ada
                IF p_schedule_id IS NOT NULL THEN
                    UPDATE maintenance_schedules
                    SET status = 'completed', updated_at = NOW()
                    WHERE id = p_schedule_id;
                END IF;

                SET p_status_code = 'SUCCESS';
                SET p_message = CONCAT('Aktivitas pemeliharaan sukses dicatat dengan Log ID #', p_new_log_id);
            END;
        ");

        // 5. Database Triggers
        // Trigger 1: Deteksi Peringatan Stok Suku Cadang Kritis secara Otomatis
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_after_sparepart_stock_update;
            CREATE TRIGGER trg_after_sparepart_stock_update
            AFTER UPDATE ON spare_parts
            FOR EACH ROW
            BEGIN
                IF NEW.stock_quantity <= NEW.min_stock_alert AND OLD.stock_quantity > NEW.stock_quantity THEN
                    INSERT INTO audit_logs (event_type, table_name, record_id, message, payload_json, created_at)
                    VALUES (
                        'TRIGGER_STOCK_ALERT',
                        'spare_parts',
                        NEW.id,
                        CONCAT('PERINGATAN SISTEM: Suku cadang [', NEW.part_code, '] ', NEW.name, ' tersisa ', NEW.stock_quantity, ' ', NEW.unit, ' (Di bawah batas minimum ', NEW.min_stock_alert, ').'),
                        JSON_OBJECT('part_code', NEW.part_code, 'stock_quantity', NEW.stock_quantity, 'min_alert', NEW.min_stock_alert),
                        NOW()
                    );
                END IF;
            END;
        ");

        // Trigger 2: Audit Otomatis saat Pemeliharaan Dicatat
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_after_maintenance_log_insert;
            CREATE TRIGGER trg_after_maintenance_log_insert
            AFTER INSERT ON maintenance_logs
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (event_type, table_name, record_id, message, payload_json, created_at)
                VALUES (
                    'TRIGGER_MAINT_LOGGED',
                    'maintenance_logs',
                    NEW.id,
                    CONCAT('AUDIT SISTEM: Pemeliharaan \"', NEW.title, '\" telah selesai dicatat oleh teknisi ', NEW.technician_name, '.'),
                    JSON_OBJECT('component_id', NEW.component_id, 'health_after', NEW.health_after, 'total_cost', NEW.total_cost),
                    NOW()
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_maintenance_log_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_sparepart_stock_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_record_maintenance_activity');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_get_component_maintenance_cost');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_component_health');
        DB::statement('DROP VIEW IF EXISTS view_monthly_maintenance_summary');
        DB::statement('DROP VIEW IF EXISTS view_critical_spareparts');
        DB::statement('DROP VIEW IF EXISTS view_component_maintenance_status');
    }
};
