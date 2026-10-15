<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kategori Komponen Pusat Data
        Schema::create('dc_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('icon', 50)->default('server');
            $table->integer('maintenance_interval_days')->default(90);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Komponen Pusat Data (Server, Network, Power, Cooling, Storage, etc.)
        Schema::create('dc_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('dc_categories')->cascadeOnDelete();
            $table->string('component_code', 50)->unique();
            $table->string('name', 150);
            $table->string('model', 100);
            $table->string('serial_number', 100)->unique();
            $table->string('rack_location', 50);
            $table->string('rack_unit', 20);
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['operational', 'warning', 'maintenance_required', 'under_maintenance', 'decommissioned'])->default('operational');
            $table->unsignedTinyInteger('condition_percent')->default(100);
            $table->string('vendor_sla', 255)->nullable();
            $table->dateTime('last_maintenance_at')->nullable();
            $table->dateTime('next_maintenance_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Suku Cadang Pusat Data (Spare Parts)
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('dc_categories')->cascadeOnDelete();
            $table->string('part_code', 50)->unique();
            $table->string('name', 150);
            $table->string('part_number', 100);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->string('unit', 30)->default('Unit');
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->string('location_bin', 50)->nullable();
            $table->text('compatibility_notes')->nullable();
            $table->string('sop_replacement_ref', 255)->nullable();
            $table->timestamps();
        });

        // 4. Jadwal Perencanaan Pemeliharaan (Maintenance Schedules)
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('dc_components')->cascadeOnDelete();
            $table->string('title', 200);
            $table->enum('maintenance_type', ['preventive', 'corrective', 'emergency'])->default('preventive');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->date('scheduled_date');
            $table->string('sop_reference', 255);
            $table->string('assigned_technician', 100)->default('Tim Infra DC');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Log Realisasi Pemeliharaan (Maintenance Logs)
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('dc_components')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('maintenance_schedules')->nullOnDelete();
            $table->string('title', 200);
            $table->dateTime('performed_at');
            $table->string('technician_name', 100);
            $table->enum('action_type', ['preventive', 'corrective', 'emergency', 'part_replacement'])->default('preventive');
            $table->text('actions_taken');
            $table->text('findings')->nullable();
            $table->unsignedTinyInteger('health_before')->default(100);
            $table->unsignedTinyInteger('health_after')->default(100);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->enum('status', ['success', 'warning', 'failed'])->default('success');
            $table->timestamps();
        });

        // 6. Pemakaian Suku Cadang pada Pemeliharaan
        Schema::create('spare_part_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_log_id')->constrained('maintenance_logs')->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->integer('quantity_used')->default(1);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });

        // 7. Administrasi Backup & Disaster Recovery
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('backup_name', 200);
            $table->enum('backup_type', ['full', 'incremental', 'differential'])->default('full');
            $table->enum('storage_location', ['primary_local', 'secondary_offsite_cikarang', 'immutable_cloud'])->default('primary_local');
            $table->string('file_path', 255);
            $table->unsignedBigInteger('file_size_bytes')->default(0);
            $table->string('checksum_sha256', 64);
            $table->boolean('is_encrypted')->default(true);
            $table->string('encryption_cipher', 50)->default('AES-256-CBC');
            $table->unsignedInteger('retention_days')->default(30);
            $table->enum('status', ['success', 'failed', 'testing', 'verified'])->default('success');
            $table->dateTime('tested_at')->nullable();
            $table->text('test_result')->nullable();
            $table->string('verified_by', 100)->nullable();
            $table->timestamps();
        });

        // 8. Audit Trail & Trigger Log (Menangkap event database, trigger alert, rollback log)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50);
            $table->string('table_name', 50);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->text('message');
            $table->json('payload_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('spare_part_usages');
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('maintenance_schedules');
        Schema::dropIfExists('spare_parts');
        Schema::dropIfExists('dc_components');
        Schema::dropIfExists('dc_categories');
    }
};
