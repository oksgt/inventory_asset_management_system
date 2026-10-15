@extends('layouts.app')

@section('title', 'Catat Realisasi Pemeliharaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('maintenance.index') }}" class="hover:text-slate-200">Pemeliharaan</a>
            <span>/</span>
            <span class="text-slate-200 font-semibold">Pencatatan Realisasi Pemeliharaan</span>
        </div>
        <a href="{{ route('maintenance.index') }}" class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pemeliharaan
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl">
        <div class="mb-6 pb-4 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-blue-400"></i>
                <span>Pencatatan Realisasi Pemeliharaan Komponen Pusat Data</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Pilih mesin eksekusi database di bawah ini untuk menguji kepatuhan SKKNI (MySQL Stored Procedure vs Managed Transaction Commit & Rollback).
            </p>
        </div>

        <form method="POST" action="{{ route('maintenance.logs.store') }}" class="space-y-5 text-xs">
            @csrf

            @if($selectedSchedule)
                <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
                <div class="p-4 rounded-xl bg-cyan-950/40 border border-cyan-800/60 text-xs space-y-1">
                    <span class="text-[10px] uppercase font-bold text-cyan-400 tracking-wider">Menyelesaikan Jadwal Pemeliharaan Terencana:</span>
                    <div class="font-bold text-slate-200 text-sm">{{ $selectedSchedule->title }}</div>
                    <div class="font-mono text-slate-400 text-[11px]">{{ $selectedSchedule->sop_reference }} • Komponen: [{{ $selectedSchedule->component->component_code }}] {{ $selectedSchedule->component->name }}</div>
                </div>
            @endif

            <!-- PILIHAN MODE EKSEKUSI DATABASE (SKKNI MANDATORY REQUIREMENT) -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                <label class="block font-bold text-white uppercase tracking-wider text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-database text-purple-400"></i>
                    <span>Pilih Mesin Eksekusi Database (Metode Transaksi) *</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- 1. Stored Procedure -->
                    <label class="relative flex flex-col p-3 rounded-xl border border-slate-800 bg-slate-900/80 cursor-pointer hover:border-purple-500 transition group">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-purple-300 text-xs">Stored Procedure</span>
                            <input type="radio" name="execution_mode" value="procedure" checked class="text-purple-600 focus:ring-purple-500">
                        </div>
                        <span class="font-mono text-[10px] text-purple-400 block mb-1">CALL sp_record_maintenance_activity()</span>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Eksekusi penuh di dalam MySQL routine (KUK 5.1 & 5.2).</p>
                    </label>

                    <!-- 2. Transaksi Commit -->
                    <label class="relative flex flex-col p-3 rounded-xl border border-slate-800 bg-slate-900/80 cursor-pointer hover:border-emerald-500 transition group">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-emerald-300 text-xs">Transaksi (COMMIT)</span>
                            <input type="radio" name="execution_mode" value="transaction" class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <span class="font-mono text-[10px] text-emerald-400 block mb-1">DB::commit()</span>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Operasi multi-tabel atomik tersimpan konsisten (KUK 8.1).</p>
                    </label>

                    <!-- 3. Transaksi Rollback Demo -->
                    <label class="relative flex flex-col p-3 rounded-xl border border-slate-800 bg-slate-900/80 cursor-pointer hover:border-rose-500 transition group">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-rose-300 text-xs">Simulasi (ROLLBACK)</span>
                            <input type="radio" name="execution_mode" value="rollback_demo" class="text-rose-600 focus:ring-rose-500">
                        </div>
                        <span class="font-mono text-[10px] text-rose-400 block mb-1">DB::rollBack()</span>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Mensimulasikan error integritas dan membatalkan seluruh perubahan (KUK 8.2).</p>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Komponen -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Komponen Pusat Data *</label>
                    <select name="component_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500 font-mono">
                        <option value="">-- Pilih Komponen --</option>
                        @foreach($components as $comp)
                            <option value="{{ $comp->id }}" {{ (old('component_id') == $comp->id || (isset($selectedSchedule) && $selectedSchedule->component_id == $comp->id) || request('component_id') == $comp->id) ? 'selected' : '' }}>
                                [{{ $comp->component_code }}] {{ $comp->name }} ({{ $comp->rack_location }})
                            </option>
                        @endforeach
                    </select>
                    @error('component_id') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Judul Pemeliharaan -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Judul Aktivitas Pemeliharaan *</label>
                    <input type="text" name="title" value="{{ old('title', $selectedSchedule->title ?? '') }}" required placeholder="Contoh: Penggantian Modul Kipas Pendingin & Thermal Re-paste"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('title') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Teknisi Pelaksana -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Nama Teknisi Pelaksana *</label>
                    <input type="text" name="technician_name" value="{{ old('technician_name', $selectedSchedule->assigned_technician ?? 'Budi Santoso (Sr. Infra Lead)') }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('technician_name') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tipe Aksi -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Kategori Tindakan *</label>
                    <select name="action_type" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="preventive" {{ old('action_type') === 'preventive' ? 'selected' : '' }}>Preventive (Pembersihan / Inspeksi Berkala)</option>
                        <option value="part_replacement" {{ old('action_type') === 'part_replacement' ? 'selected' : '' }}>Part Replacement (Penggantian Suku Cadang)</option>
                        <option value="corrective" {{ old('action_type') === 'corrective' ? 'selected' : '' }}>Corrective (Perbaikan Anomali Kerusakan)</option>
                        <option value="emergency" {{ old('action_type') === 'emergency' ? 'selected' : '' }}>Emergency (Penanganan Insiden Downtime)</option>
                    </select>
                    @error('action_type') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Tindakan yang Dilakukan -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Rincian Tindakan yang Dilakukan *</label>
                <textarea name="actions_taken" rows="3" required placeholder="Jelaskan langkah teknis: Prosedur isolasi ESD, pemutusan arus AC line B, pelepasan modul lama, pemasangan modul baru, uji cold restart..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">{{ old('actions_taken') }}</textarea>
                @error('actions_taken') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Temuan & Kondisi Hasil -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Temuan / Catatan Inspeksi Fisik</label>
                    <input type="text" name="findings" value="{{ old('findings') }}" placeholder="Contoh: RPM kipas normal 6200, suhu stabil 42°C, tidak ada error SMART"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('findings') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Skor Kesehatan Setelah Servis (1 - 100%) *</label>
                    <input type="number" name="health_after" value="{{ old('health_after', 98) }}" min="1" max="100" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('health_after') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Penggunaan Suku Cadang (Opsi Pengurangan Stok) -->
            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-3">
                <span class="block font-semibold text-slate-300 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-microchip text-cyan-400"></i>
                    <span>Penggunaan Suku Cadang dari Inventaris (Opsional)</span>
                </span>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-slate-400 mb-1">Pilih Suku Cadang</label>
                        <select name="spare_part_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-cyan-500 font-mono text-xs">
                            <option value="">-- Tidak Ada Suku Cadang yang Dipakai --</option>
                            @foreach($spareParts as $part)
                                <option value="{{ $part->id }}" {{ old('spare_part_id') == $part->id ? 'selected' : '' }}>
                                    [{{ $part->part_code }}] {{ $part->name }} (Stok: {{ $part->stock_quantity }} {{ $part->unit }} | Rp {{ number_format($part->unit_cost, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1">Jumlah Unit Dipakai</label>
                        <input type="number" name="quantity_used" value="{{ old('quantity_used', 1) }}" min="1"
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-slate-200 font-mono focus:outline-none focus:border-cyan-500 text-xs">
                    </div>
                </div>
                <p class="text-[10px] text-slate-400">
                    * Jika suku cadang dipilih, sistem (atau Stored Procedure) akan secara atomik memotong stok gudang dan mengkalkulasi total biaya pemeliharaan.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('maintenance.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Eksekusi & Catat Realisasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
