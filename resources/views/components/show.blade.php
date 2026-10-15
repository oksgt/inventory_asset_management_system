@extends('layouts.app')

@section('title', 'Detail Komponen - ' . $component->component_code)

@section('content')
<div class="space-y-6">

    <!-- Header Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('components.index') }}" class="hover:text-slate-200">Komponen Pusat Data</a>
            <span>/</span>
            <span class="font-mono text-cyan-400 font-semibold">{{ $component->component_code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('components.edit', $component) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i> Ubah Data
            </a>
            <a href="{{ route('maintenance.logs.create', ['component_id' => $component->id]) }}" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-lg shadow-cyan-600/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-screwdriver-wrench"></i> Servis Komponen Ini
            </a>
        </div>
    </div>

    <!-- Main Detail Hero Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 rounded-2xl border border-slate-800 p-6 shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3 py-1 rounded-md bg-cyan-950 text-cyan-400 border border-cyan-800/60 font-mono font-bold text-sm">
                        {{ $component->component_code }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 text-xs font-medium">
                        <i class="fa-solid fa-{{ $component->category->icon ?? 'server' }} mr-1.5 text-slate-400"></i>{{ $component->category->name }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $component->status === 'operational' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800/60' :
                          ($component->status === 'warning' ? 'bg-amber-950 text-amber-400 border border-amber-800/60' : 'bg-rose-950 text-rose-400 border border-rose-800/60') }}">
                        {{ str_replace('_', ' ', $component->status) }}
                    </span>
                </div>

                <h1 class="text-2xl font-bold text-white tracking-tight">{{ $component->name }}</h1>
                <p class="text-xs text-slate-400 font-mono flex items-center gap-2">
                    <span>Model: {{ $component->model }}</span>
                    <span>•</span>
                    <span>SN: {{ $component->serial_number }}</span>
                    @if($component->ip_address)
                        <span>•</span>
                        <span class="text-cyan-400 font-bold">IP: {{ $component->ip_address }}</span>
                    @endif
                </p>
            </div>

            <!-- Health Score Widget -->
            <div class="bg-slate-950/80 p-4 rounded-xl border border-slate-800 flex items-center gap-4 shrink-0">
                <div class="text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Kondisi Fisik</span>
                    <div class="text-3xl font-extrabold font-mono mt-0.5 {{ $component->condition_percent >= 80 ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ $component->condition_percent }}%
                    </div>
                </div>
                <div class="h-10 w-[1px] bg-slate-800"></div>
                <div class="text-center">
                    <span class="text-[10px] font-semibold text-purple-400 uppercase tracking-wider block flex items-center gap-1 justify-center">
                        <i class="fa-solid fa-bolt text-[9px]"></i> SQL Function
                    </span>
                    <div class="text-3xl font-extrabold font-mono text-purple-300 mt-0.5" title="Dihitung oleh MySQL fn_calculate_component_health()">
                        {{ $sqlMetrics['calculated_health'] }}%
                    </div>
                    <span class="text-[9px] font-mono text-slate-400">fn_health()</span>
                </div>
            </div>
        </div>

        <!-- Technical Specification Metadata Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-800/80 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Lokasi Rak & Elevasi</span>
                <span class="font-mono text-slate-200 font-semibold flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-cyan-400"></i>
                    {{ $component->rack_location }} ({{ $component->rack_unit }})
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Total Biaya Pemeliharaan</span>
                <span class="font-mono text-emerald-400 font-semibold flex items-center gap-1.5" title="Dihitung oleh MySQL fn_get_component_maintenance_cost()">
                    <i class="fa-solid fa-calculator text-emerald-500"></i>
                    Rp {{ number_format($sqlMetrics['total_maintenance_cost'], 0, ',', '.') }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Servis Terakhir</span>
                <span class="font-mono text-slate-300">
                    {{ $component->last_maintenance_at ? $component->last_maintenance_at->format('d M Y') : 'Belum pernah' }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Jatuh Tempo Servis Berikutnya</span>
                <span class="font-mono font-semibold {{ $component->next_maintenance_at && $component->next_maintenance_at->isPast() ? 'text-rose-400' : 'text-cyan-400' }}">
                    {{ $component->next_maintenance_at ? $component->next_maintenance_at->format('d M Y') : '-' }}
                </span>
            </div>
        </div>

        @if($component->vendor_sla)
            <div class="mt-4 p-3 rounded-xl bg-slate-950/50 border border-slate-800/60 text-xs text-slate-300 flex items-center gap-2">
                <i class="fa-solid fa-handshake text-cyan-400"></i>
                <span><strong>Kontrak Vendor & SLA:</strong> {{ $component->vendor_sla }}</span>
            </div>
        @endif

        @if($component->notes)
            <div class="mt-3 text-xs text-slate-400">
                <strong class="text-slate-300">Catatan Konfigurasi:</strong> {{ $component->notes }}
            </div>
        @endif
    </div>

    <!-- Two Columns: Maintenance Schedules & History Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Jadwal Pemeliharaan Mendatang -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-cyan-400"></i>
                    <h3 class="text-sm font-bold text-white">Jadwal Perencanaan Pemeliharaan</h3>
                </div>
                <a href="{{ route('maintenance.schedules.create', ['component_id' => $component->id]) }}" class="text-xs text-cyan-400 hover:underline font-semibold">
                    + Buat Jadwal
                </a>
            </div>

            <div class="space-y-3">
                @forelse($component->schedules as $sched)
                    <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase
                                    {{ $sched->priority === 'critical' ? 'bg-rose-950 text-rose-400 border border-rose-800/50' : 'bg-slate-800 text-slate-300' }}">
                                    {{ $sched->priority }}
                                </span>
                                <span class="font-mono text-slate-400">{{ $sched->status }}</span>
                            </div>
                            <h4 class="font-semibold text-slate-200">{{ $sched->title }}</h4>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $sched->sop_reference }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-mono text-slate-300 font-bold block">{{ $sched->scheduled_date->format('d M Y') }}</span>
                            <span class="text-[11px] text-slate-400 block">{{ $sched->assigned_technician }}</span>
                            @if($sched->status !== 'completed')
                                <a href="{{ route('maintenance.logs.create', ['schedule_id' => $sched->id]) }}" class="inline-block mt-2 px-2.5 py-1 rounded bg-cyan-950 text-cyan-300 border border-cyan-800/60 font-semibold hover:bg-cyan-900 transition">
                                    Eksekusi
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada jadwal pemeliharaan terencana untuk komponen ini.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Riwayat Pemeliharaan & Suku Cadang Terpakai -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-400"></i>
                    <h3 class="text-sm font-bold text-white">Log Realisasi Pemeliharaan</h3>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($component->logs as $log)
                    <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-200">{{ $log->title }}</span>
                            <span class="font-mono text-slate-400 text-[11px]">{{ $log->performed_at->format('d M Y H:i') }}</span>
                        </div>
                        <p class="text-slate-300 text-[11px]">{{ $log->actions_taken }}</p>
                        
                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-900 text-[11px]">
                            <span class="text-slate-400">Teknisi: <strong class="text-slate-300">{{ $log->technician_name }}</strong></span>
                            <span class="font-mono text-emerald-400 font-semibold">Biaya: Rp {{ number_format($log->total_cost, 0, ',', '.') }}</span>
                        </div>

                        @if($log->sparePartUsages->isNotEmpty())
                            <div class="mt-2 pt-2 border-t border-slate-900/80">
                                <span class="text-[10px] text-purple-400 font-semibold uppercase tracking-wider block mb-1">Suku Cadang Diganti:</span>
                                <div class="space-y-1">
                                    @foreach($log->sparePartUsages as $usage)
                                        <div class="text-[11px] font-mono text-slate-300 flex items-center justify-between bg-slate-900 px-2 py-1 rounded">
                                            <span>[{{ $usage->sparePart->part_code }}] {{ $usage->sparePart->name }}</span>
                                            <span class="text-cyan-400 font-bold">{{ $usage->quantity_used }} {{ $usage->sparePart->unit }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada riwayat pemeliharaan tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
