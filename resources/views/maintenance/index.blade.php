@extends('layouts.app')

@section('title', 'Pemeliharaan & Log Realisasi Pusat Data')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Manajemen Pemeliharaan Pusat Data (Data Center Maintenance)</h2>
            <p class="text-xs text-slate-400 mt-0.5">Penjadwalan pemeliharaan preventif, realisasi servis berkala, dan eksekusi transaksional (Stored Procedure & Commit/Rollback).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('maintenance.schedules.create') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-plus text-cyan-400"></i>
                <span>Rencanakan PM Baru</span>
            </a>
            <a href="{{ route('maintenance.logs.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-lg shadow-blue-600/20 transition flex items-center gap-1.5">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Catat Realisasi Pemeliharaan</span>
            </a>
        </div>
    </div>

    <!-- Monthly Summary from MySQL View 'view_monthly_maintenance_summary' -->
    @if($monthlySummary->isNotEmpty())
        <div class="bg-gradient-to-r from-slate-900 to-blue-950/30 p-4 rounded-xl border border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-950 text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div>
                    <span class="font-bold text-white block">Ringkasan Pemeliharaan Bulanan (MySQL View)</span>
                    <span class="text-[11px] text-slate-400 font-mono">view_monthly_maintenance_summary</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6 font-mono">
                @foreach($monthlySummary as $sum)
                    <div class="flex items-center gap-4 border-l border-slate-800 pl-4">
                        <div>
                            <span class="text-slate-400 text-[10px] block font-sans">Periode</span>
                            <span class="font-bold text-cyan-400">{{ $sum->period_month }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block font-sans">Aktivitas</span>
                            <span class="font-bold text-slate-200">{{ $sum->total_activities }} Servis</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block font-sans">Total Biaya</span>
                            <span class="font-bold text-emerald-400">Rp {{ number_format($sum->total_cost_incurred, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="flex items-center space-x-2 border-b border-slate-800 text-xs font-semibold">
        <a href="{{ route('maintenance.index', ['tab' => 'schedules']) }}"
            class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 {{ $activeTab === 'schedules' ? 'border-cyan-400 text-cyan-400 bg-cyan-950/20' : 'border-transparent text-slate-400 hover:text-slate-200' }}">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Jadwal Perencanaan Pemeliharaan (Preventive Schedule)</span>
        </a>

        <a href="{{ route('maintenance.index', ['tab' => 'logs']) }}"
            class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 {{ $activeTab === 'logs' ? 'border-cyan-400 text-cyan-400 bg-cyan-950/20' : 'border-transparent text-slate-400 hover:text-slate-200' }}">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Log Realisasi Pemeliharaan (Executed Records)</span>
        </a>
    </div>

    @if($activeTab === 'schedules')
        <!-- Tab 1: Schedules Table -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950/70 text-slate-400 border-b border-slate-800 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4">Komponen & Lokasi</th>
                            <th class="py-3 px-4">Judul Pemeliharaan & SOP</th>
                            <th class="py-3 px-4">Tipe & Prioritas</th>
                            <th class="py-3 px-4 font-mono">Tanggal Rencana</th>
                            <th class="py-3 px-4">Teknisi Penanggung Jawab</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-sans">
                        @forelse($schedules as $sched)
                            <tr class="hover:bg-slate-800/30 transition">
                                <!-- Komponen -->
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-cyan-400">
                                        <a href="{{ route('components.show', $sched->component) }}" class="hover:underline">
                                            {{ $sched->component->component_code }}
                                        </a>
                                    </div>
                                    <div class="text-slate-300 font-medium mt-0.5">{{ $sched->component->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $sched->component->rack_location }} ({{ $sched->component->rack_unit }})</div>
                                </td>

                                <!-- Judul & SOP -->
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-200 text-sm">{{ $sched->title }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                        <i class="fa-solid fa-file-contract text-cyan-400 text-[10px]"></i>
                                        <span>{{ $sched->sop_reference }}</span>
                                    </div>
                                </td>

                                <!-- Tipe & Prioritas -->
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider block mb-1
                                        {{ $sched->priority === 'critical' ? 'bg-rose-950 text-rose-400 border border-rose-800/60' :
                                          ($sched->priority === 'high' ? 'bg-amber-950 text-amber-400 border border-amber-800/60' : 'bg-slate-800 text-slate-300') }}">
                                        {{ $sched->priority }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 uppercase font-mono">{{ $sched->maintenance_type }}</span>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-200">
                                    {{ $sched->scheduled_date->format('d M Y') }}
                                </td>

                                <!-- Teknisi -->
                                <td class="py-3.5 px-4 text-slate-300">
                                    <div class="font-medium">{{ $sched->assigned_technician }}</div>
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $sched->status === 'completed' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800/60' :
                                          ($sched->status === 'in_progress' ? 'bg-blue-950 text-blue-400 border border-blue-800/60' : 'bg-slate-800 text-slate-300') }}">
                                        {{ str_replace('_', ' ', $sched->status) }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right">
                                    @if($sched->status !== 'completed')
                                        <a href="{{ route('maintenance.logs.create', ['schedule_id' => $sched->id]) }}"
                                            class="px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-play text-[10px]"></i> Eksekusi
                                        </a>
                                    @else
                                        <span class="text-[11px] text-emerald-400 font-mono"><i class="fa-solid fa-check-double mr-1"></i>Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada jadwal pemeliharaan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schedules->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Tab 2: Logs Table -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950/70 text-slate-400 border-b border-slate-800 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4">Waktu & Komponen</th>
                            <th class="py-3 px-4">Aktivitas Pemeliharaan & Tindakan</th>
                            <th class="py-3 px-4">Teknisi Pelaksana</th>
                            <th class="py-3 px-4 text-center">Kesehatan Sebelum/Sesudah</th>
                            <th class="py-3 px-4 text-right">Biaya / Part Terpakai</th>
                            <th class="py-3 px-4 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-sans">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-800/30 transition">
                                <!-- Waktu & Komponen -->
                                <td class="py-3.5 px-4 font-mono">
                                    <div class="font-bold text-slate-200">{{ $log->performed_at->format('d M Y H:i') }}</div>
                                    <div class="text-cyan-400 font-semibold mt-0.5">
                                        <a href="{{ route('components.show', $log->component) }}" class="hover:underline">
                                            {{ $log->component->component_code }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-sans">{{ $log->component->rack_location }}</div>
                                </td>

                                <!-- Aktivitas & Tindakan -->
                                <td class="py-3.5 px-4 max-w-sm">
                                    <div class="font-bold text-slate-200 text-sm">{{ $log->title }}</div>
                                    <p class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">{{ $log->actions_taken }}</p>
                                    @if($log->findings)
                                        <div class="text-[10px] text-amber-400/90 mt-1">
                                            <strong>Temuan:</strong> {{ $log->findings }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Teknisi -->
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-200">{{ $log->technician_name }}</div>
                                    <span class="text-[10px] font-mono text-slate-400 uppercase">{{ $log->action_type }}</span>
                                </td>

                                <!-- Kesehatan -->
                                <td class="py-3.5 px-4 text-center font-mono">
                                    <div class="flex items-center justify-center gap-1.5 font-bold">
                                        <span class="text-slate-400">{{ $log->health_before }}%</span>
                                        <i class="fa-solid fa-arrow-right text-[10px] text-slate-500"></i>
                                        <span class="text-emerald-400 text-sm">{{ $log->health_after }}%</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-sans">Kondisi Fisik</div>
                                </td>

                                <!-- Biaya & Suku Cadang -->
                                <td class="py-3.5 px-4 text-right font-mono">
                                    <div class="font-bold text-emerald-400 text-sm">
                                        Rp {{ number_format($log->total_cost, 0, ',', '.') }}
                                    </div>
                                    @if($log->sparePartUsages->isNotEmpty())
                                        <div class="text-[10px] text-purple-400 mt-0.5">
                                            {{ $log->sparePartUsages->count() }} Suku Cadang
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-500 mt-0.5">Tanpa part</div>
                                    @endif
                                </td>

                                <!-- Detail -->
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('maintenance.logs.show', $log) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Lihat Riwayat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada catatan log pemeliharaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
