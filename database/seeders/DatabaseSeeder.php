<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\BackupLog;
use App\Models\DcCategory;
use App\Models\DcComponent;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use App\Models\SparePart;
use App\Models\SparePartUsage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive enterprise Data Center data.
     */
    public function run(): void
    {
        // 1. Administrator User
        User::updateOrCreate(
            ['email' => 'admin@datacenter.local'],
            [
                'name' => 'Sigit Oka - DC Operations Lead',
                'password' => Hash::make('okasigit'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Kategori Pusat Data (Klasifikasi Komponen Sesuai KUK)
        $categories = [
            [
                'code' => 'SRV',
                'name' => 'Server & Compute Node',
                'icon' => 'server',
                'maintenance_interval_days' => 90,
                'description' => 'Server rackmount fisik, hypervisor host, database engine, dan blade chassis compute.',
            ],
            [
                'code' => 'NET',
                'name' => 'Jaringan & Switching (Spine-Leaf)',
                'icon' => 'network-wired',
                'maintenance_interval_days' => 120,
                'description' => 'Core switch 100GbE, leaf switch Top-of-Rack, edge router, hardware firewall appliance.',
            ],
            [
                'code' => 'SAN',
                'name' => 'Storage Area Network (SAN/NAS)',
                'icon' => 'database',
                'maintenance_interval_days' => 90,
                'description' => 'All-Flash storage array, SAS disk shelves, Fiber Channel SAN switches, NAS backup node.',
            ],
            [
                'code' => 'PWR',
                'name' => 'Catu Daya (UPS, PDU, Genset)',
                'icon' => 'bolt',
                'maintenance_interval_days' => 30,
                'description' => 'Online double-conversion modular UPS 3-Phase, Intelligent PDU per rack, Automatic Transfer Switch (ATS), Genset backup.',
            ],
            [
                'code' => 'CLG',
                'name' => 'Pendingin Presisi (PAC & In-Row Cooling)',
                'icon' => 'snowflake',
                'maintenance_interval_days' => 45,
                'description' => 'Precision Air Conditioning (PAC) unit downflow, In-Row chilled water cooling, containment cold-aisle monitoring.',
            ],
            [
                'code' => 'SEC',
                'name' => 'Sensor Lingkungan & Proteksi Kebakaran',
                'icon' => 'shield-halved',
                'maintenance_interval_days' => 60,
                'description' => 'FM-200 / Novec 1230 gas fire suppression, humidity/temperature probe, biometric access controller.',
            ],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['code']] = DcCategory::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Komponen Pusat Data
        $components = [
            [
                'category_id' => $catModels['SRV']->id,
                'component_code' => 'SRV-R01-U12',
                'name' => 'Dell PowerEdge R750xs Primary Production DB',
                'model' => 'PowerEdge R750xs Dual Xeon Gold 6330',
                'serial_number' => 'DEL-SN-9843210-XC',
                'rack_location' => 'Rack A-01',
                'rack_unit' => 'U12-U13',
                'ip_address' => '10.240.10.21',
                'status' => 'operational',
                'condition_percent' => 96,
                'vendor_sla' => 'Dell ProSupport Plus 24x7 4-Hr Mission Critical',
                'last_maintenance_at' => Carbon::now()->subDays(25),
                'next_maintenance_at' => Carbon::now()->addDays(65),
                'notes' => 'Host kluster MySQL 8.4 Enterprise. Memory 256GB ECC DDR4, RAID 10 NVMe.',
            ],
            [
                'category_id' => $catModels['SRV']->id,
                'component_code' => 'SRV-R01-U14',
                'name' => 'Dell PowerEdge R750xs Secondary Standby DB',
                'model' => 'PowerEdge R750xs Dual Xeon Gold 6330',
                'serial_number' => 'DEL-SN-9843211-XD',
                'rack_location' => 'Rack A-01',
                'rack_unit' => 'U14-U15',
                'ip_address' => '10.240.10.22',
                'status' => 'operational',
                'condition_percent' => 92,
                'vendor_sla' => 'Dell ProSupport Plus 24x7 4-Hr Mission Critical',
                'last_maintenance_at' => Carbon::now()->subDays(30),
                'next_maintenance_at' => Carbon::now()->addDays(60),
                'notes' => 'Replika async standby ke offsite data center Cikarang.',
            ],
            [
                'category_id' => $catModels['SRV']->id,
                'component_code' => 'SRV-R02-U08',
                'name' => 'HPE ProLiant DL380 Gen10 App Virtualization Node 1',
                'model' => 'ProLiant DL380 Gen10 2x Xeon Silver 4214R',
                'serial_number' => 'HPE-SN-772901-MY',
                'rack_location' => 'Rack A-02',
                'rack_unit' => 'U08-U09',
                'ip_address' => '10.240.10.31',
                'status' => 'warning',
                'condition_percent' => 74,
                'vendor_sla' => 'HPE Pointnext Complete Care 24x7',
                'last_maintenance_at' => Carbon::now()->subDays(92),
                'next_maintenance_at' => Carbon::now()->subDays(2), // Overdue!
                'notes' => 'Sensor suhu slot PCI-e mendeteksi suhu di atas ambang normal (72°C). Perlu pembersihan fan & heatsink.',
            ],
            [
                'category_id' => $catModels['NET']->id,
                'component_code' => 'NET-R01-U41',
                'name' => 'Cisco Nexus 93180YC-FX3 Core Spine Switch 1',
                'model' => 'Nexus 9300 48-port 1/10/25G SFP28 + 6-port 40/100G QSFP28',
                'serial_number' => 'FOC241908LK',
                'rack_location' => 'Rack A-01',
                'rack_unit' => 'U41-U42',
                'ip_address' => '10.240.0.1',
                'status' => 'operational',
                'condition_percent' => 98,
                'vendor_sla' => 'Cisco Smart Net Total Care 24x7x4',
                'last_maintenance_at' => Carbon::now()->subDays(40),
                'next_maintenance_at' => Carbon::now()->addDays(80),
                'notes' => 'Dual redundant hot-swappable AC power supply, back-to-front airflow.',
            ],
            [
                'category_id' => $catModels['SAN']->id,
                'component_code' => 'SAN-R02-U18',
                'name' => 'NetApp AFF A400 All-Flash SAN Primary Storage',
                'model' => 'AFF A400 Dual Controller 24x 3.84TB NVMe SSD',
                'serial_number' => 'NTAP-AF400-88319',
                'rack_location' => 'Rack A-02',
                'rack_unit' => 'U18-U21',
                'ip_address' => '10.240.20.10',
                'status' => 'operational',
                'condition_percent' => 95,
                'vendor_sla' => 'NetApp SupportEdge Premium 4-Hour Onsite',
                'last_maintenance_at' => Carbon::now()->subDays(15),
                'next_maintenance_at' => Carbon::now()->addDays(75),
                'notes' => 'Storage tier 1 untuk database VM, target snapshot harian dan volume backup.',
            ],
            [
                'category_id' => $catModels['PWR']->id,
                'component_code' => 'PWR-MOD-UPS01',
                'name' => 'Schneider APC Symmetra PX 48kW Modular 3-Phase UPS',
                'model' => 'Symmetra PX 48kW Scalable to 96kW N+1',
                'serial_number' => 'APC-SY48K-99021',
                'rack_location' => 'Power Room Row-P',
                'rack_unit' => 'Floor Standing',
                'ip_address' => '10.240.50.2',
                'status' => 'operational',
                'condition_percent' => 88,
                'vendor_sla' => 'Schneider Electric Critical Power Care 24x7',
                'last_maintenance_at' => Carbon::now()->subDays(28),
                'next_maintenance_at' => Carbon::now()->addDays(2),
                'notes' => 'Menyuplai redundansi jalur Power Line A. Modul baterai string 3 mendekati umur kalibrasi 2 tahun.',
            ],
            [
                'category_id' => $catModels['CLG']->id,
                'component_code' => 'CLG-PAC-01',
                'name' => 'Vertiv Liebert CRV Precision In-Row Cooling Unit',
                'model' => 'Liebert CRV 300mm In-Row PAC 20kW Chilled Water',
                'serial_number' => 'VTV-CRV-54120',
                'rack_location' => 'Cold Aisle A',
                'rack_unit' => 'Bay 03',
                'ip_address' => '10.240.50.15',
                'status' => 'maintenance_required',
                'condition_percent' => 68,
                'vendor_sla' => 'Vertiv Service Edge 24x7 4-Hour Response',
                'last_maintenance_at' => Carbon::now()->subDays(50),
                'next_maintenance_at' => Carbon::now()->subDays(5), // Overdue!
                'notes' => 'Filter udara G4 perlu dicuci/ganti dan sensor kondensasi air perlu kalibrasi rutin.',
            ],
        ];

        $compModels = [];
        foreach ($components as $comp) {
            $compModels[$comp['component_code']] = DcComponent::updateOrCreate(
                ['component_code' => $comp['component_code']],
                $comp
            );
        }

        // 4. Suku Cadang Pusat Data (Perencanaan Suku Cadang Sesuai KUK)
        $spareParts = [
            [
                'category_id' => $catModels['SRV']->id,
                'part_code' => 'SP-RAM-64GB',
                'name' => '64GB DDR4-3200 ECC Registered RDIMM Server Memory',
                'part_number' => 'MTA36ASF8G72PZ-3G2R1',
                'stock_quantity' => 12,
                'min_stock_alert' => 4,
                'unit' => 'Unit',
                'unit_cost' => 3850000,
                'location_bin' => 'Bin SRV-01-A',
                'compatibility_notes' => 'Kompatibel dengan Dell R750xs, HPE DL380 Gen10, Cisco UCS C220 M5',
                'sop_replacement_ref' => 'SOP-SP-01: Prosedur Penggantian Memori Server Antistatis ESD',
            ],
            [
                'category_id' => $catModels['SRV']->id,
                'part_code' => 'SP-SSD-384TB',
                'name' => '3.84TB NVMe U.2 Enterprise Read-Intensive SSD 2.5"',
                'part_number' => 'KPM61RUG3T84',
                'stock_quantity' => 2, // LOW STOCK! (min: 3)
                'min_stock_alert' => 3,
                'unit' => 'Unit',
                'unit_cost' => 9200000,
                'location_bin' => 'Bin SAN-03-B',
                'compatibility_notes' => 'Kompatibel dengan Dell PowerEdge R750xs Backplane & NetApp AFF Shelf',
                'sop_replacement_ref' => 'SOP-SP-02: Prosedur Hot-Swap Disk Storage RAID Degraded',
            ],
            [
                'category_id' => $catModels['SRV']->id,
                'part_code' => 'SP-PSU-1400W',
                'name' => 'Dell 1400W Platinum AC Hot-Plug Redundant Power Supply',
                'part_number' => '072V60-1400W-PLT',
                'stock_quantity' => 1, // CRITICAL LOW STOCK! (min: 2)
                'min_stock_alert' => 2,
                'unit' => 'Unit',
                'unit_cost' => 4250000,
                'location_bin' => 'Bin PWR-02-C',
                'compatibility_notes' => 'Kompatibel dengan Dell PowerEdge R740, R750xs, R840',
                'sop_replacement_ref' => 'SOP-SP-03: Prosedur Penggantian Catu Daya Redundan Tanpa Downtime',
            ],
            [
                'category_id' => $catModels['NET']->id,
                'part_code' => 'SP-SFP-10G-SR',
                'name' => 'Cisco 10GBASE-SR SFP+ Optical Transceiver Multi-Mode',
                'part_number' => 'SFP-10G-SR-S',
                'stock_quantity' => 18,
                'min_stock_alert' => 5,
                'unit' => 'Pcs',
                'unit_cost' => 1450000,
                'location_bin' => 'Bin NET-01-D',
                'compatibility_notes' => 'Kompatibel dengan Cisco Nexus 9300, Catalyst 9500, Dell Switch S5248F',
                'sop_replacement_ref' => 'SOP-SP-04: Prosedur Pembersihan & Pemasangan Fiber Optic Transceiver',
            ],
            [
                'category_id' => $catModels['PWR']->id,
                'part_code' => 'SP-BAT-SYBT4',
                'name' => 'Schneider APC Battery Module Cartridge SYBT4 for Symmetra',
                'part_number' => 'SYBT4-MOD-12V',
                'stock_quantity' => 3,
                'min_stock_alert' => 2,
                'unit' => 'Module',
                'unit_cost' => 8750000,
                'location_bin' => 'Bin PWR-BAT-Heavy',
                'compatibility_notes' => 'Khusus APC Symmetra PX 48kW / 96kW Modular Frame',
                'sop_replacement_ref' => 'SOP-SP-05: Prosedur Penggantian Modul Baterai UPS Bertegangan Tinggi',
            ],
            [
                'category_id' => $catModels['CLG']->id,
                'part_code' => 'SP-FLT-PAC-G4',
                'name' => 'Filter Udara Pleated Vertiv Liebert G4 Class Air Filter',
                'part_number' => 'FLT-CRV-G4-300',
                'stock_quantity' => 0, // OUT OF STOCK!
                'min_stock_alert' => 4,
                'unit' => 'Set',
                'unit_cost' => 850000,
                'location_bin' => 'Bin CLG-FLT-Rack',
                'compatibility_notes' => 'Khusus Vertiv Liebert CRV In-Row PAC 300mm',
                'sop_replacement_ref' => 'SOP-SP-06: Prosedur Penggantian Filter Udara Cold-Aisle Containment',
            ],
        ];

        $spModels = [];
        foreach ($spareParts as $sp) {
            $spModels[$sp['part_code']] = SparePart::updateOrCreate(
                ['part_code' => $sp['part_code']],
                $sp
            );
        }

        // 5. Jadwal Perencanaan Pemeliharaan (Preventive & Corrective)
        $schedules = [
            [
                'component_id' => $compModels['SRV-R02-U08']->id,
                'title' => 'Inspeksi Thermal & Penggantian Kipas Redundan HPE DL380',
                'maintenance_type' => 'corrective',
                'priority' => 'high',
                'scheduled_date' => Carbon::today()->addDays(1),
                'sop_reference' => 'SOP-PM-02: Pemeliharaan Sistem Pendingin Kipas Chassis Server',
                'assigned_technician' => 'Budi Santoso (Sr. Infra Engineer)',
                'status' => 'scheduled',
                'notes' => 'Pembersihan debu heatsink prosesor dan pengujian RPM kipas redundan chassis.',
            ],
            [
                'component_id' => $compModels['CLG-PAC-01']->id,
                'title' => 'Penggantian Filter Udara G4 & Flush Jalur Drainase Kondensasi',
                'maintenance_type' => 'preventive',
                'priority' => 'critical',
                'scheduled_date' => Carbon::today(),
                'sop_reference' => 'SOP-PM-05: Pemeliharaan Triwulan Unit Pendingin Presisi PAC',
                'assigned_technician' => 'Agus Priyono (HVAC Specialist)',
                'status' => 'in_progress',
                'notes' => 'Perlu restock filter G4 segera atau gunakan cadangan darurat di Ruang Genset.',
            ],
            [
                'component_id' => $compModels['PWR-MOD-UPS01']->id,
                'title' => 'Kalibrasi String Baterai & Uji Simulasi Beban Discharge UPS',
                'maintenance_type' => 'preventive',
                'priority' => 'high',
                'scheduled_date' => Carbon::today()->addDays(2),
                'sop_reference' => 'SOP-PM-04: Pengujian Siklus Discharge Baterai UPS Modular',
                'assigned_technician' => 'Tim Maintenance Daya Schneider',
                'status' => 'scheduled',
                'notes' => 'Dijalankan pada maintenance window pukul 01:00 - 04:00 WIB.',
            ],
            [
                'component_id' => $compModels['NET-R01-U41']->id,
                'title' => 'Pembersihan Optik Transceiver & Inspeksi Konektor LC OM4',
                'maintenance_type' => 'preventive',
                'priority' => 'medium',
                'scheduled_date' => Carbon::today()->addDays(14),
                'sop_reference' => 'SOP-PM-03: Pemeliharaan Perangkat Jaringan Core & Distribusi',
                'assigned_technician' => 'Rahmat Hidayat (Network Lead)',
                'status' => 'scheduled',
                'notes' => 'Gunakan One-Click Fiber Cleaner 1.25mm untuk modul SFP28.',
            ],
        ];

        foreach ($schedules as $sched) {
            MaintenanceSchedule::updateOrCreate(
                ['title' => $sched['title'], 'component_id' => $sched['component_id']],
                $sched
            );
        }

        // 6. Log Realisasi Pemeliharaan (Maintenance Logs Historis)
        $pastLog1 = MaintenanceLog::create([
            'component_id' => $compModels['SRV-R01-U12']->id,
            'title' => 'Preventive Maintenance Berkala 90 Hari Dell PowerEdge R750xs',
            'performed_at' => Carbon::now()->subDays(25),
            'technician_name' => 'Budi Santoso',
            'action_type' => 'preventive',
            'actions_taken' => 'Pembersihan chassis menggunakan vacuum ESD, pembaruan firmware iDRAC9 ke v7.00.00, pengecekan event log RAID controller PERC H755.',
            'findings' => 'Semua modul NVMe dalam kondisi SMART 100%, kipas beroperasi pada normal 6500 RPM.',
            'health_before' => 90,
            'health_after' => 96,
            'total_cost' => 750000,
            'status' => 'success',
        ]);

        $pastLog2 = MaintenanceLog::create([
            'component_id' => $compModels['SAN-R02-U18']->id,
            'title' => 'Penggantian NVMe SSD Drive Degraded Bay 04 NetApp A400',
            'performed_at' => Carbon::now()->subDays(15),
            'technician_name' => 'Agus Priyono',
            'action_type' => 'part_replacement',
            'actions_taken' => 'Mengidentifikasi amber LED bay 04, melakukan offline drive via ONTAP CLI, mengganti dengan drive baru part MTA36/KPM61, verifikasi rebuild RAID-DP.',
            'findings' => 'Drive lama mengalami bad block count melebihi ambang 50. Rebuild selesai dalam 42 menit tanpa gangguan I/O.',
            'health_before' => 82,
            'health_after' => 95,
            'total_cost' => 9200000,
            'status' => 'success',
        ]);

        // Catat penggunaan suku cadang untuk pastLog2
        SparePartUsage::create([
            'maintenance_log_id' => $pastLog2->id,
            'spare_part_id' => $spModels['SP-SSD-384TB']->id,
            'quantity_used' => 1,
            'unit_cost' => 9200000,
            'notes' => 'Penggantian drive gagal bay 04 NetApp storage shelf',
        ]);

        // 7. Administrasi Backup & Disaster Recovery Logs
        $backups = [
            [
                'backup_name' => 'DC_PROD_FULL_20261014_010000.sql.enc',
                'backup_type' => 'full',
                'storage_location' => 'primary_local',
                'file_path' => 'backups/daily/DC_PROD_FULL_20261014_010000.sql.enc',
                'file_size_bytes' => 1572864000, // 1.46 GB
                'checksum_sha256' => hash('sha256', 'dummy_backup_20261014_data'),
                'is_encrypted' => true,
                'encryption_cipher' => 'AES-256-CBC',
                'retention_days' => 30,
                'status' => 'verified',
                'tested_at' => Carbon::now()->subHours(10),
                'test_result' => 'VERIFIKASI INTEGRITAS LOLOS: Checksum SHA-256 valid. Dekripsi AES-256 berhasil. Uji import dry-run 100% konsisten (0 error).',
                'verified_by' => 'Automated Backup Verifier & Budi Santoso',
            ],
            [
                'backup_name' => 'DC_PROD_DIFF_20261014_130000.sql.enc',
                'backup_type' => 'differential',
                'storage_location' => 'secondary_offsite_cikarang',
                'file_path' => 'backups/cikarang_offsite/DC_PROD_DIFF_20261014_130000.sql.enc',
                'file_size_bytes' => 348127232, // 332 MB
                'checksum_sha256' => hash('sha256', 'dummy_backup_diff_cikarang'),
                'is_encrypted' => true,
                'encryption_cipher' => 'AES-256-CBC',
                'retention_days' => 14,
                'status' => 'verified',
                'tested_at' => Carbon::now()->subHours(6),
                'test_result' => 'VERIFIKASI LOLOS: Replikasi offsite Cikarang terkonfirmasi via rsync over IPsec VPN tunnel. Dekripsi AES-256 OK.',
                'verified_by' => 'Offsite DR Daemon',
            ],
            [
                'backup_name' => 'DC_PROD_INCR_20261015_010000.sql.enc',
                'backup_type' => 'incremental',
                'storage_location' => 'primary_local',
                'file_path' => 'backups/daily/DC_PROD_INCR_20261015_010000.sql.enc',
                'file_size_bytes' => 89128960, // 85 MB
                'checksum_sha256' => hash('sha256', 'dummy_backup_incremental_today'),
                'is_encrypted' => true,
                'encryption_cipher' => 'AES-256-CBC',
                'retention_days' => 7,
                'status' => 'success',
                'tested_at' => null,
                'test_result' => 'Menunggu jadwal verifikasi otomatis pukul 12:00 WIB.',
                'verified_by' => null,
            ],
        ];

        foreach ($backups as $b) {
            BackupLog::updateOrCreate(['backup_name' => $b['backup_name']], $b);
        }

        // 8. Audit Logs
        AuditLogsSeeder::seed();
    }
}

class AuditLogsSeeder
{
    public static function seed(): void
    {
        $events = [
            [
                'event_type' => 'TRIGGER_STOCK_ALERT',
                'table_name' => 'spare_parts',
                'record_id' => 2,
                'message' => 'PERINGATAN SISTEM: Suku cadang [SP-SSD-384TB] 3.84TB NVMe U.2 Enterprise SSD tersisa 2 Unit (Di bawah batas minimum 3).',
                'payload_json' => ['part_code' => 'SP-SSD-384TB', 'stock' => 2, 'min_alert' => 3],
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'event_type' => 'TRIGGER_MAINT_LOGGED',
                'table_name' => 'maintenance_logs',
                'record_id' => 2,
                'message' => 'AUDIT SISTEM: Pemeliharaan "Penggantian NVMe SSD Drive Degraded Bay 04 NetApp A400" telah selesai dicatat oleh teknisi Agus Priyono.',
                'payload_json' => ['component_id' => 5, 'health_after' => 95, 'total_cost' => 9200000],
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'event_type' => 'DB_COMMIT_VERIFIED',
                'table_name' => 'dc_components',
                'record_id' => 1,
                'message' => 'TRANSAKSI DATABASE: Commit terverifikasi untuk perubahan telemetry status sistem.',
                'payload_json' => ['status' => 'operational', 'isolation_level' => 'REPEATABLE READ'],
                'created_at' => Carbon::now()->subHours(2),
            ],
        ];

        foreach ($events as $event) {
            AuditLog::create($event);
        }
    }
}
