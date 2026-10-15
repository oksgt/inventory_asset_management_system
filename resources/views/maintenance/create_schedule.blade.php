@extends('layouts.app')

@section('title', 'Rencanakan Jadwal Pemeliharaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('maintenance.index') }}" class="hover:text-slate-200">Pemeliharaan</a>
            <span>/</span>
            <span class="text-slate-200 font-semibold">Jadwalkan Pemeliharaan Preventif (PM)</span>
        </div>
        <a href="{{ route('maintenance.index') }}" class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pemeliharaan
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl">
        <div class="mb-6 pb-4 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus text-cyan-400"></i>
                <span>Perencanaan Pemeliharaan Komponen Pusat Data</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Standar KUK: Mendokumentasikan komponen yang membutuhkan pemeliharaan & mengidentifikasi SOP yang berlaku.</p>
        </div>

        <form method="POST" action="{{ route('maintenance.schedules.store') }}" class="space-y-4 text-xs">
            @csrf

            <!-- Komponen Target -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Pilih Komponen Pusat Data *</label>
                <select name="component_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500 font-mono">
                    <option value="">-- Pilih Komponen --</option>
                    @foreach($components as $comp)
                        <option value="{{ $comp->id }}" {{ (old('component_id') == $comp->id || request('component_id') == $comp->id) ? 'selected' : '' }}>
                            [{{ $comp->component_code }}] {{ $comp->name }} ({{ $comp->rack_location }})
                        </option>
                    @endforeach
                </select>
                @error('component_id') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Judul Jadwal -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Judul Rencana Pemeliharaan *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pembersihan Thermal & Filter Udara Rutin Triwulan"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                @error('title') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tipe Pemeliharaan -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Tipe Pemeliharaan *</label>
                    <select name="maintenance_type" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="preventive" {{ old('maintenance_type') === 'preventive' ? 'selected' : '' }}>Preventive (Pencegahan Rutin)</option>
                        <option value="corrective" {{ old('maintenance_type') === 'corrective' ? 'selected' : '' }}>Corrective (Perbaikan Anomali)</option>
                        <option value="emergency" {{ old('maintenance_type') === 'emergency' ? 'selected' : '' }}>Emergency (Darurat Insiden)</option>
                    </select>
                    @error('maintenance_type') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Prioritas -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Prioritas Tindakan *</label>
                    <select name="priority" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low (Rendah)</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium (Standar)</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High (Tinggi)</option>
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical (Kritis)</option>
                    </select>
                    @error('priority') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Rencana -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Tanggal Pelaksanaan *</label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('scheduled_date') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Referensi SOP -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Dokumen Standar Operasional Prosedur (SOP) Terkait *</label>
                <input type="text" name="sop_reference" value="{{ old('sop_reference', 'SOP-PM-01: Prosedur Pemeliharaan Rutin Server & Rackmount Unit') }}" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                <span class="text-[10px] text-slate-400 block mt-1">Sesuai KUK: Mengidentifikasi SOP dan/atau kontrak pemeliharaan komponen sesuai standar organisasi.</span>
                @error('sop_reference') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Teknisi Penanggung Jawab -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Teknisi / Tim Pelaksana *</label>
                <input type="text" name="assigned_technician" value="{{ old('assigned_technician', 'Tim Operasional Infrastruktur DC') }}" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                @error('assigned_technician') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Catatan Instruksi Kerja -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Instruksi Kerja Khusus / Checklist</label>
                <textarea name="notes" rows="2" placeholder="Gunakan wrist-strap ESD, periksa kabel daya redundan line A & B, dsb."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">{{ old('notes') }}</textarea>
                @error('notes') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('maintenance.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Simpan Jadwal Pemeliharaan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
