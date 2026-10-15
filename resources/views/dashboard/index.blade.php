@extends('layouts.app')

@section('title', 'Dashboard Operasional Pusat Data')

@section('content')
<div class="space-y-6">

    <!-- Top Headline & Status Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-900 to-cyan-950/40 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950 text-emerald-400 border border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Operasional Normal
                </span>
                <span class="text-xs text-slate-400">Site Utama: Gedung Cyber 1 Jakarta (Tier-3)</span>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Pusat Manajemen Infrastruktur & Pemeliharaan</h2>
            <p class="text-sm text-slate-400 mt-1">Monitoring kesehatan aset komputasi, inventaris suku cadang, dan kepatuhan disaster recovery terintegrasi MySQL 8.4.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('skkni.index') }}" class="px-4 py-2.5 rounded-xl bg-purple-600/30 hover:bg-purple-600/40 text-purple-200 border border-purple-500/50 text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-purple-950/50">
                <i class="fa-solid fa-graduation-cap text-purple-400 text-sm"></i>
                <span>Verifikasi Standar SKKNI</span>
            </a>
            <a href="{{ route('reports.print') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan Resmi</span>
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Komponen DC -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden group hover:border-cyan-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Komponen Pusat Data</span>
                    <div class="text-3xl font-extrabold text-white mt-1">{{ $totalComponents }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-950 text-cyan-400 border border-cyan-800/40 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-server"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800/80">
                <span class="text-emerald-400 font-medium"><i class="fa-solid fa-circle-check mr-1"></i>{{ $operationalCount }} Beroperasi</span>
                <span class="{{ $warningCount > 0 ? 'text-amber-400 font-bold' : 'text-slate-400' }}"><i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $warningCount }} Butuh Servis</span>
            </div>
        </div>

        <!-- 2. Rata-rata Kesehatan DC -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden group hover:border-emerald-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Skor Kesehatan Aset</span>
                    <div class="text-3xl font-extrabold text-white mt-1">{{ $avgHealth }}%</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-950 text-emerald-400 border border-emerald-800/40 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80">
                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-cyan-400 h-full rounded-full" style="width: {{ $avgHealth }}%"></div>
                </div>
            </div>
        </div>

        <!-- 3. Suku Cadang Kritis (MySQL View) -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden group hover:border-amber-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Suku Cadang Kritis</span>
                    <div class="text-3xl font-extrabold text-white mt-1 flex items-baseline gap-2">
                        <span>{{ $criticalPartsCount }}</span>
                        <span class="text-xs font-mono text-slate-400 font-normal">Item</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-950 text-amber-400 border border-amber-800/40 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800/80">
                <span class="font-mono text-cyan-400">MySQL View: view_critical_spareparts</span>
                <a href="{{ route('spareparts.index', ['critical_only' => 1]) }}" class="text-amber-400 hover:underline font-semibold">Lihat &rarr;</a>
            </div>
        </div>

        <!-- 4. Status Backup & DR -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden group hover:border-purple-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Backup & DR Terverifikasi</span>
                    <div class="text-3xl font-extrabold text-white mt-1">{{ $verifiedBackupCount }}</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-950 text-purple-400 border border-purple-800/40 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-800/80">
                <span class="text-emerald-400"><i class="fa-solid fa-lock text-[10px] mr-1"></i>Enkripsi AES-256</span>
                <a href="{{ route('backup.index') }}" class="text-purple-400 hover:underline font-semibold">Kelola DR &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Action Toolstrip -->
    <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800/80 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2 text-slate-400">
            <i class="fa-solid fa-bolt text-amber-400"></i>
            <span class="font-semibold text-slate-300">Aksi Cepat Pemeliharaan & Pengujian:</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('maintenance.schedules.create') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                <i class="fa-solid fa-calendar-plus mr-1 text-cyan-400"></i> Buat Jadwal PM
            </a>
            <a href="{{ route('maintenance.logs.create') }}" class="px-3 py-1.5 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/30 transition">
                <i class="fa-solid fa-database mr-1 text-blue-400"></i> Catat Log via Stored Procedure
            </a>
            <a href="{{ route('backup.index') }}" class="px-3 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 transition">
                <i class="fa-solid fa-cloud-arrow-up mr-1 text-emerald-400"></i> Buat Backup Database
            </a>
            <a href="{{ route('skkni.index', ['tab' => 'sql']) }}" class="px-3 py-1.5 rounded-lg bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 transition">
                <i class="fa-solid fa-vial mr-1 text-purple-400"></i> Uji Coba Commit & Rollback
            </a>
        </div>
    </div>

    <!-- Two Column Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Jadwal Pemeliharaan Mendatang (Preventive Maintenance) -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-cyan-950 text-cyan-400 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Jadwal Perencanaan Pemeliharaan</h3>
                        <p class="text-[11px] text-slate-400">SOP Pemeliharaan Komponen Pusat Data</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.index', ['tab' => 'schedules']) }}" class="text-xs text-cyan-400 hover:underline font-medium">Semua Jadwal &rarr;</a>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($upcomingSchedules as $sched)
                    <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase
                                    {{ $sched->priority === 'critical' ? 'bg-rose-950 text-rose-400 border border-rose-800/50' : 
                                      ($sched->priority === 'high' ? 'bg-amber-950 text-amber-400 border border-amber-800/50' : 'bg-slate-800 text-slate-300') }}">
                                    {{ $sched->priority }}
                                </span>
                                <span class="text-xs font-mono text-cyan-400">{{ $sched->component->component_code }}</span>
                                <span class="text-xs text-slate-400">• {{ $sched->component->rack_location }}</span>
                            </div>
                            <h4 class="text-sm font-semibold text-slate-200">{{ $sched->title }}</h4>
                            <p class="text-xs text-slate-400 font-mono">{{ $sched->sop_reference }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-mono font-semibold text-slate-300">{{ $sched->scheduled_date->format('d M Y') }}</span>
                            <div class="text-[11px] text-slate-400 mt-1">{{ $sched->assigned_technician }}</div>
                            <a href="{{ route('maintenance.logs.create', ['schedule_id' => $sched->id]) }}" class="inline-block mt-2 px-2.5 py-1 rounded text-[11px] font-semibold bg-cyan-950 text-cyan-300 border border-cyan-800/60 hover:bg-cyan-900 transition">
                                Selesaikan
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Tidak ada jadwal pemeliharaan yang tertunda.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Riwayat Pemeliharaan & Suku Cadang Terakhir -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-950 text-blue-400 flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Realisasi Pemeliharaan Terakhir</h3>
                        <p class="text-[11px] text-slate-400">Tercatat Transaksional & Riwayat Penggunaan Part</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.index', ['tab' => 'logs']) }}" class="text-xs text-blue-400 hover:underline font-medium">Semua Log &rarr;</a>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($recentLogs as $log)
                    <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-200">{{ $log->title }}</span>
                            <span class="text-xs font-mono text-slate-400">{{ $log->performed_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-400 line-clamp-1">{{ $log->actions_taken }}</p>
                        
                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-900 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-cyan-400">{{ $log->component->component_code }}</span>
                                <span class="text-slate-400">• Teknisi: {{ $log->technician_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($log->sparePartUsages->isNotEmpty())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-purple-950 text-purple-300 border border-purple-800/40">
                                        {{ $log->sparePartUsages->count() }} Suku Cadang Terpakai
                                    </span>
                                @endif
                                <span class="font-mono font-semibold text-emerald-400">Rp {{ number_format($log->total_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada catatan realisasi pemeliharaan.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Section: MySQL Views & Trigger Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Komponen Pusat Data Kritis (Dari MySQL View) -->
        <div class="lg:col-span-2 bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Peringatan Kesehatan Komponen</span>
                        <span class="text-[10px] font-mono font-normal px-2 py-0.5 rounded bg-cyan-950 text-cyan-400 border border-cyan-800/50">MySQL View: view_component_maintenance_status</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Komponen dengan skor kesehatan rendah atau mendekati jatuh tempo servis</p>
                </div>
                <a href="{{ route('components.index') }}" class="text-xs text-cyan-400 hover:underline">Semua Komponen &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-800">
                            <th class="pb-2.5 font-semibold">Kode & Nama Komponen</th>
                            <th class="pb-2.5 font-semibold">Lokasi Rack</th>
                            <th class="pb-2.5 font-semibold">Status</th>
                            <th class="pb-2.5 font-semibold text-center">Kondisi (%)</th>
                            <th class="pb-2.5 font-semibold text-right">Hari Menuju PM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($criticalComponents as $comp)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-2.5">
                                    <div class="font-bold text-slate-200">{{ $comp->component_code }}</div>
                                    <div class="text-[11px] text-slate-400 font-sans font-normal">{{ $comp->component_name }}</div>
                                </td>
                                <td class="py-2.5 text-slate-400 font-sans">{{ $comp->rack_location }} ({{ $comp->rack_unit }})</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        {{ $comp->health_level === 'CRITICAL' ? 'bg-rose-950 text-rose-400 border border-rose-800/50' : 'bg-amber-950 text-amber-400 border border-amber-800/50' }}">
                                        {{ $comp->health_level }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-center font-bold {{ $comp->condition_percent < 80 ? 'text-amber-400' : 'text-emerald-400' }}">
                                    {{ $comp->condition_percent }}%
                                </td>
                                <td class="py-2.5 text-right {{ $comp->days_to_maintenance < 0 ? 'text-rose-400 font-bold' : 'text-slate-400' }}">
                                    {{ $comp->days_to_maintenance }} Hari
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400 font-sans">Semua komponen berada dalam kondisi sehat (Optimal).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live MySQL Trigger & Audit Logs -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        <span>Audit & Trigger Terkini</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">MySQL Triggers & Transaction Logs</p>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <div class="space-y-3 overflow-y-auto max-h-80">
                @forelse($recentAudits as $audit)
                    <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold uppercase
                                {{ str_contains($audit->event_type, 'TRIGGER') ? 'bg-amber-950 text-amber-300 border border-amber-800/60' : 'bg-cyan-950 text-cyan-300 border border-cyan-800/60' }}">
                                {{ $audit->event_type }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">{{ $audit->created_at->format('H:i:s') }}</span>
                        </div>
                        <p class="text-slate-300 text-[11px] leading-relaxed">{{ $audit->message }}</p>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-slate-400">Belum ada trigger event tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
