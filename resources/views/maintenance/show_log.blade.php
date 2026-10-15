@extends('layouts.app')

@section('title', 'Detail Log Pemeliharaan #' . $log->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('maintenance.index') }}" class="hover:text-slate-200">Pemeliharaan</a>
            <span>/</span>
            <a href="{{ route('maintenance.index', ['tab' => 'logs']) }}" class="hover:text-slate-200">Log Realisasi</a>
            <span>/</span>
            <span class="text-slate-200 font-semibold font-mono">#LOG-{{ str_pad($log->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <a href="{{ route('maintenance.index', ['tab' => 'logs']) }}" class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Log
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-800">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2.5 py-0.5 rounded font-mono font-bold bg-blue-950 text-blue-400 border border-blue-800/60 uppercase">
                        {{ $log->action_type }}
                    </span>
                    <span class="text-slate-400 font-mono">{{ $log->performed_at->format('d F Y, H:i:s') }} WIB</span>
                </div>
                <h1 class="text-xl font-bold text-white tracking-tight">{{ $log->title }}</h1>
                <p class="text-xs text-slate-400 font-mono">
                    Komponen: <a href="{{ route('components.show', $log->component) }}" class="text-cyan-400 hover:underline font-bold">[{{ $log->component->component_code }}] {{ $log->component->name }}</a>
                </p>
            </div>

            <div class="text-right shrink-0">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Total Biaya Realisasi</span>
                <div class="text-2xl font-bold font-mono text-emerald-400 mt-0.5">
                    Rp {{ number_format($log->total_cost, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Metric Details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-950/80 border border-slate-800 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Teknisi Pelaksana</span>
                <strong class="text-slate-200">{{ $log->technician_name }}</strong>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Kondisi Sebelum</span>
                <span class="font-mono text-slate-300 font-bold">{{ $log->health_before }}%</span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Kondisi Sesudah</span>
                <span class="font-mono text-emerald-400 font-bold">{{ $log->health_after }}%</span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Status Verifikasi</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-950 text-emerald-400 border border-emerald-800/60 font-mono">
                    {{ $log->status }}
                </span>
            </div>
        </div>

        <!-- Rincian Tindakan -->
        <div class="space-y-2 text-xs">
            <h3 class="font-bold text-white text-sm">Rincian Tindakan Teknis</h3>
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 text-slate-300 leading-relaxed font-sans whitespace-pre-line">
                {{ $log->actions_taken }}
            </div>
        </div>

        @if($log->findings)
            <div class="space-y-2 text-xs">
                <h3 class="font-bold text-white text-sm">Temuan Anomali & Kondisi Lapangan</h3>
                <div class="p-4 rounded-xl bg-slate-950 border border-amber-800/40 text-amber-200/90 leading-relaxed font-sans">
                    {{ $log->findings }}
                </div>
            </div>
        @endif

        <!-- Suku Cadang yang Digunakan -->
        <div class="space-y-3 text-xs">
            <h3 class="font-bold text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-purple-400"></i>
                <span>Suku Cadang yang Dikeluarkan dari Gudang</span>
            </h3>

            @if($log->sparePartUsages->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 text-slate-400 font-semibold border-b border-slate-800">
                                <th class="py-2.5 px-3">Kode & Nama Part</th>
                                <th class="py-2.5 px-3">Part Number</th>
                                <th class="py-2.5 px-3 text-center">Jumlah Dipakai</th>
                                <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 font-mono">
                            @foreach($log->sparePartUsages as $usage)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="py-2.5 px-3 font-sans">
                                        <div class="font-mono font-bold text-cyan-400">{{ $usage->sparePart->part_code }}</div>
                                        <div class="text-slate-200">{{ $usage->sparePart->name }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-400">{{ $usage->sparePart->part_number }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-slate-200">
                                        {{ $usage->quantity_used }} {{ $usage->sparePart->unit }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right text-slate-300">
                                        Rp {{ number_format($usage->unit_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-emerald-400">
                                        Rp {{ number_format($usage->unit_cost * $usage->quantity_used, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 rounded-xl bg-slate-950 text-slate-400 text-center">
                    Tidak ada penggunaan suku cadang dalam pemeliharaan ini (Hanya pembersihan/pengecekan rutin).
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
