@extends('layouts.app')

@section('title', 'Inventaris Komponen Pusat Data')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Komponen Pusat Data (Data Center Assets)</h2>
            <p class="text-xs text-slate-400 mt-0.5">Klasifikasi Server Compute, Core Switch, All-Flash SAN, Modular UPS, dan Precision Air Conditioning.</p>
        </div>
        <a href="{{ route('components.create') }}" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs shadow-lg shadow-cyan-600/20 transition flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>Daftarkan Komponen Baru</span>
        </a>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('components.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <!-- Search -->
            <div>
                <label class="block text-slate-400 font-medium mb-1">Cari Aset / SN / Rack</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: SRV-R01, DEL-SN, Rack A..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-9 pr-3 py-2 text-slate-200 placeholder-slate-400 focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-slate-400 font-medium mb-1">Klasifikasi Kategori</label>
                <select name="category_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 focus:outline-none focus:border-cyan-500">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Operasional -->
            <div>
                <label class="block text-slate-400 font-medium mb-1">Status Operasional</label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 focus:outline-none focus:border-cyan-500">
                    <option value="">-- Semua Status --</option>
                    <option value="operational" {{ request('status') === 'operational' ? 'selected' : '' }}>Operational (Normal)</option>
                    <option value="warning" {{ request('status') === 'warning' ? 'selected' : '' }}>Warning (Perlu Perhatian)</option>
                    <option value="maintenance_required" {{ request('status') === 'maintenance_required' ? 'selected' : '' }}>Maintenance Required (Kritis)</option>
                    <option value="under_maintenance" {{ request('status') === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium border border-slate-700 transition">
                    Filter Data
                </button>
                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('components.index') }}" class="px-3 py-2 rounded-lg bg-slate-800/50 hover:bg-slate-800 text-slate-400 hover:text-slate-200 border border-slate-800 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Components Table -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/70 text-slate-400 border-b border-slate-800 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Kode & Nama Komponen</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Lokasi Rack</th>
                        <th class="py-3 px-4">Serial Number</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Kesehatan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($components as $comp)
                        <tr class="hover:bg-slate-800/30 transition">
                            <!-- Kode & Nama -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-cyan-400 text-sm">
                                    <a href="{{ route('components.show', $comp) }}" class="hover:underline">{{ $comp->component_code }}</a>
                                </div>
                                <div class="font-semibold text-slate-200 mt-0.5">{{ $comp->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $comp->model }}</div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 font-medium">
                                    <i class="fa-solid fa-{{ $comp->category->icon ?? 'server' }} text-slate-400 text-[10px]"></i>
                                    <span>{{ $comp->category->name }}</span>
                                </span>
                            </td>

                            <!-- Lokasi Rack -->
                            <td class="py-3.5 px-4 font-mono">
                                <div class="text-slate-200 font-semibold">{{ $comp->rack_location }}</div>
                                <div class="text-[11px] text-slate-400">{{ $comp->rack_unit }}</div>
                                @if($comp->ip_address)
                                    <div class="text-[10px] text-cyan-400 mt-0.5">{{ $comp->ip_address }}</div>
                                @endif
                            </td>

                            <!-- Serial Number -->
                            <td class="py-3.5 px-4 font-mono text-slate-300">
                                <div>{{ $comp->serial_number }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-[160px]">{{ $comp->vendor_sla }}</div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $comp->status === 'operational' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800/60' :
                                      ($comp->status === 'warning' ? 'bg-amber-950 text-amber-400 border border-amber-800/60' :
                                      ($comp->status === 'maintenance_required' ? 'bg-rose-950 text-rose-400 border border-rose-800/60' : 'bg-slate-800 text-slate-300')) }}">
                                    {{ str_replace('_', ' ', $comp->status) }}
                                </span>
                            </td>

                            <!-- Kondisi & Kesehatan -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="font-bold text-sm font-mono {{ $comp->condition_percent >= 80 ? 'text-emerald-400' : ($comp->condition_percent >= 60 ? 'text-amber-400' : 'text-rose-400') }}">
                                    {{ $comp->condition_percent }}%
                                </div>
                                <div class="w-16 bg-slate-800 h-1.5 rounded-full mx-auto mt-1 overflow-hidden">
                                    <div class="h-full rounded-full {{ $comp->condition_percent >= 80 ? 'bg-emerald-400' : ($comp->condition_percent >= 60 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width: {{ $comp->condition_percent }}%"></div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('components.show', $comp) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Lihat Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('components.edit', $comp) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Ubah Aset">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="{{ route('maintenance.logs.create', ['component_id' => $comp->id]) }}" class="p-1.5 rounded-lg bg-cyan-950 hover:bg-cyan-900 text-cyan-300 border border-cyan-800/50 transition" title="Servis Komponen">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada komponen yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($components->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $components->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
