@extends('layouts.app')

@section('title', 'Inventaris Suku Cadang Pusat Data')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                <span>Inventaris Suku Cadang (Spare Parts Management)</span>
                @if($showCriticalOnly)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-amber-950 text-amber-400 border border-amber-800/60">
                        Filter: MySQL View (view_critical_spareparts)
                    </span>
                @endif
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Pengelolaan buffer stok suku cadang kritis, SOP penggantian antistatis (ESD), dan trigger otomatis batas minimum.</p>
        </div>
        <a href="{{ route('spareparts.create') }}" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-lg shadow-cyan-600/20 transition flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Suku Cadang</span>
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-slate-400 uppercase tracking-wider font-semibold text-[11px]">Total Item Terdaftar</span>
                <div class="text-2xl font-bold text-white mt-1">{{ $totalItems }} SKU</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-cyan-950 text-cyan-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-microchip"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 p-4 rounded-xl border {{ $criticalCount > 0 ? 'border-amber-800/60 bg-amber-950/20' : 'border-slate-800' }} flex items-center justify-between">
            <div>
                <span class="text-slate-400 uppercase tracking-wider font-semibold text-[11px]">Stok Kritis / Kurang</span>
                <div class="text-2xl font-bold {{ $criticalCount > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1">
                    {{ $criticalCount }} SKU
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-950 text-amber-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-slate-400 uppercase tracking-wider font-semibold text-[11px]">Valuasi Total Gudang DC</span>
                <div class="text-xl font-bold font-mono text-emerald-400 mt-1">
                    Rp {{ number_format($totalValuation, 0, ',', '.') }}
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-950 text-emerald-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Toolbar & Filter -->
    <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <a href="{{ route('spareparts.index') }}" class="px-3 py-1.5 rounded-lg font-semibold transition {{ !$showCriticalOnly ? 'bg-cyan-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                Semua Suku Cadang ({{ $totalItems }})
            </a>
            <a href="{{ route('spareparts.index', ['critical_only' => 1]) }}" class="px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5 {{ $showCriticalOnly ? 'bg-amber-600 text-white' : 'bg-amber-950/60 text-amber-400 border border-amber-800/40 hover:bg-amber-900' }}">
                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                <span>Stok Kritis (MySQL View: {{ $criticalCount }})</span>
            </a>
        </div>

        <form method="GET" action="{{ route('spareparts.index') }}" class="flex items-center gap-2">
            @if($showCriticalOnly)
                <input type="hidden" name="critical_only" value="1">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, part number..."
                class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5 text-slate-200 placeholder-slate-400 focus:outline-none focus:border-cyan-500">
            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium border border-slate-700 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Spare Parts Table -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/70 text-slate-400 border-b border-slate-800 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Kode & Deskripsi Suku Cadang</th>
                        <th class="py-3 px-4">Kategori & Lokasi Bin</th>
                        <th class="py-3 px-4 text-center">Stok / Batas Min</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4">SOP Penggantian</th>
                        <th class="py-3 px-4 text-right">Penyesuaian Stok (Trigger)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($spareParts as $part)
                        <tr class="hover:bg-slate-800/30 transition {{ $part->isLowStock() ? 'bg-amber-950/10' : '' }}">
                            <!-- Kode & Deskripsi -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-cyan-400">{{ $part->part_code }}</div>
                                <div class="font-semibold text-slate-200 mt-0.5">{{ $part->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">P/N: {{ $part->part_number }}</div>
                            </td>

                            <!-- Kategori & Bin -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-medium inline-block mb-1">
                                    {{ $part->category->name }}
                                </span>
                                <div class="font-mono text-slate-400 text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-box text-slate-400 text-[10px]"></i>
                                    {{ $part->location_bin ?? 'Bin Utama' }}
                                </div>
                            </td>

                            <!-- Status Stok -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="font-mono font-bold text-sm {{ $part->stock_quantity == 0 ? 'text-rose-400' : ($part->isLowStock() ? 'text-amber-400' : 'text-emerald-400') }}">
                                    {{ $part->stock_quantity }} {{ $part->unit }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                    Min Alert: {{ $part->min_stock_alert }}
                                </div>
                                @if($part->isLowStock())
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $part->stock_quantity == 0 ? 'bg-rose-950 text-rose-400 border border-rose-800/60' : 'bg-amber-950 text-amber-400 border border-amber-800/60' }}">
                                        {{ $part->stock_quantity == 0 ? 'Habis' : 'Stok Kritis' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Harga Satuan -->
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-200">
                                Rp {{ number_format($part->unit_cost, 0, ',', '.') }}
                            </td>

                            <!-- SOP Penggantian -->
                            <td class="py-3.5 px-4 max-w-[220px]">
                                <div class="text-[11px] text-slate-300 font-medium truncate" title="{{ $part->sop_replacement_ref }}">
                                    {{ $part->sop_replacement_ref ?? 'SOP Standar ESD DC' }}
                                </div>
                                <div class="text-[10px] text-slate-400 truncate mt-0.5">
                                    {{ $part->compatibility_notes }}
                                </div>
                            </td>

                            <!-- Quick Stock Adjustment Form (Triggers MySQL Trigger) -->
                            <td class="py-3.5 px-4 text-right">
                                <form method="POST" action="{{ route('spareparts.adjust-stock', $part) }}" class="flex items-center justify-end gap-1.5">
                                    @csrf
                                    <input type="hidden" name="reason" value="Penyesuaian stok langsung dari tabel inventaris">
                                    
                                    <!-- Tombol Kurangi (-1) -->
                                    <button type="submit" name="quantity_change" value="-1" 
                                        class="px-2 py-1 rounded bg-rose-950/80 hover:bg-rose-900 text-rose-300 border border-rose-800/60 font-mono font-bold text-xs transition"
                                        title="Kurangi 1 unit (Uji MySQL Trigger jika stok <= minimum)">
                                        -1
                                    </button>

                                    <!-- Tombol Tambah (+5) -->
                                    <button type="submit" name="quantity_change" value="5" 
                                        class="px-2 py-1 rounded bg-emerald-950/80 hover:bg-emerald-900 text-emerald-300 border border-emerald-800/60 font-mono font-bold text-xs transition"
                                        title="Restock +5 unit">
                                        +5
                                    </button>

                                    <a href="{{ route('spareparts.edit', $part) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Ubah Part">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada data suku cadang yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($spareParts->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $spareParts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
