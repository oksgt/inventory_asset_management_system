@extends('layouts.app')

@section('title', 'Laporan Perencanaan & Pemeliharaan Pusat Data')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-cyan-400"></i>
                <span>Laporan Perencanaan & Pemeliharaan Komponen Pusat Data</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Standar KUK: Menyusun laporan pemeliharaan sesuai format organisasi dan mengarsipkan laporan untuk pihak terkait.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print', ['period' => $period]) }}" target="_blank"
                class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-lg shadow-cyan-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Ekspor Format Resmi</span>
            </a>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800 flex items-center justify-between gap-4 text-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-3">
            <span class="font-semibold text-slate-300">Pilih Periode Laporan:</span>
            <input type="month" name="period" value="{{ $period }}"
                class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
            <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium border border-slate-700 transition">
                Tampilkan
            </button>
        </form>

        <span class="text-slate-400 font-mono text-[11px]">
            Format: Standar Audit ISO/IEC 27001 & TIA-942
        </span>
    </div>

    <!-- Summary KPI for Selected Period -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">Aset Komponen DC</span>
            <div class="text-2xl font-bold font-mono text-white mt-1">{{ $components->count() }} Unit</div>
        </div>

        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">Realisasi Pemeliharaan</span>
            <div class="text-2xl font-bold font-mono text-cyan-400 mt-1">{{ $logs->count() }} Kegiatan</div>
        </div>

        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">Total Biaya Realisasi</span>
            <div class="text-2xl font-bold font-mono text-emerald-400 mt-1">
                Rp {{ number_format($totalCostMonth, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">Estimasi Anggaran Suku Cadang</span>
            <div class="text-2xl font-bold font-mono text-amber-400 mt-1">
                Rp {{ number_format($criticalSpareParts->sum('estimated_reorder_budget'), 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Section 1: Kebutuhan Pengadaan Suku Cadang Kritis (MySQL View) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-amber-400"></i>
                <span>Perencanaan Kebutuhan Suku Cadang Kritis (Buffer Stock Reorder)</span>
            </h3>
            <span class="text-[11px] font-mono text-cyan-400">MySQL View: view_critical_spareparts</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950 text-slate-400 font-semibold border-b border-slate-800">
                        <th class="py-2.5 px-3">Kode & Nama Suku Cadang</th>
                        <th class="py-2.5 px-3">Part Number</th>
                        <th class="py-2.5 px-3 text-center">Stok Fisik / Min</th>
                        <th class="py-2.5 px-3 text-center">Defisit Unit</th>
                        <th class="py-2.5 px-3 text-right">Estimasi Anggaran Pengadaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-mono">
                    @forelse($criticalSpareParts as $cp)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-2.5 px-3 font-sans">
                                <div class="font-mono font-bold text-cyan-400">{{ $cp->part_code }}</div>
                                <div class="text-slate-200">{{ $cp->part_name }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-400">{{ $cp->part_number }}</td>
                            <td class="py-2.5 px-3 text-center text-amber-400 font-bold">
                                {{ $cp->stock_quantity }} / {{ $cp->min_stock_alert }} {{ $cp->unit }}
                            </td>
                            <td class="py-2.5 px-3 text-center text-rose-400 font-bold">
                                +{{ $cp->stock_deficit }} {{ $cp->unit }}
                            </td>
                            <td class="py-2.5 px-3 text-right text-emerald-400 font-bold">
                                Rp {{ number_format($cp->estimated_reorder_budget, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400 font-sans">Semua stok suku cadang dalam kondisi mencukupi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Jadwal Terencana Periode Ini -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm space-y-3">
        <h3 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-slate-800">
            <i class="fa-solid fa-calendar-check text-cyan-400"></i>
            <span>Jadwal Perencanaan Pemeliharaan (Preventive Maintenance Periode: {{ $period }})</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950 text-slate-400 font-semibold border-b border-slate-800">
                        <th class="py-2.5 px-3">Komponen</th>
                        <th class="py-2.5 px-3">Aktivitas Pemeliharaan & SOP</th>
                        <th class="py-2.5 px-3">Prioritas</th>
                        <th class="py-2.5 px-3 font-mono">Tanggal</th>
                        <th class="py-2.5 px-3">Teknisi</th>
                        <th class="py-2.5 px-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-sans">
                    @forelse($schedules as $s)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-2.5 px-3 font-mono">
                                <span class="font-bold text-cyan-400">{{ $s->component->component_code }}</span>
                                <div class="text-[11px] text-slate-400 font-sans">{{ $s->component->rack_location }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-semibold text-slate-200">{{ $s->title }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $s->sop_reference }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono
                                    {{ $s->priority === 'critical' ? 'bg-rose-950 text-rose-400' : 'bg-slate-800 text-slate-300' }}">
                                    {{ $s->priority }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-mono text-slate-200">{{ $s->scheduled_date->format('d M Y') }}</td>
                            <td class="py-2.5 px-3 text-slate-300">{{ $s->assigned_technician }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono
                                    {{ $s->status === 'completed' ? 'bg-emerald-950 text-emerald-400' : 'bg-slate-800 text-slate-300' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-400">Tidak ada jadwal tercatat pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
