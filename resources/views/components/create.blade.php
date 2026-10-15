@extends('layouts.app')

@section('title', 'Daftarkan Komponen Pusat Data')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('components.index') }}" class="hover:text-slate-200">Komponen Pusat Data</a>
            <span>/</span>
            <span class="text-slate-200 font-semibold">Pendaftaran Komponen Baru</span>
        </div>
        <a href="{{ route('components.index') }}" class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl">
        <div class="mb-6 pb-4 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-server text-cyan-400"></i>
                <span>Pendaftaran Aset Infrastruktur Pusat Data</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Lengkapi informasi teknis komponen, lokasi rak pusat data, dan kontrak pemeliharaan (SLA Vendor).</p>
        </div>

        <form method="POST" action="{{ route('components.store') }}" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kategori Komponen -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Klasifikasi Kategori Pusat Data *</label>
                    <select name="category_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Kode Komponen -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Kode Komponen Unik *</label>
                    <input type="text" name="component_code" value="{{ old('component_code') }}" required placeholder="Contoh: SRV-R03-U10, NET-SW-02"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500 uppercase">
                    @error('component_code') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Komponen -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Nama Perangkat / Hostname *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Dell PowerEdge R750xs Core Database"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('name') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Model Hardware -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Model / Tipe Hardware *</label>
                    <input type="text" name="model" value="{{ old('model') }}" required placeholder="Contoh: PowerEdge R750xs Dual Xeon Gold"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('model') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Serial Number -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Nomor Seri Pabrik (Serial Number) *</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" required placeholder="Contoh: SN-DEL-984321-X"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500 uppercase">
                    @error('serial_number') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- IP Address -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Alamat IP Manajemen (OOB / iDRAC / IPMI)</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address') }}" placeholder="Contoh: 10.240.10.25"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('ip_address') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Lokasi Rack -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Lokasi Rak (Rack Identifier) *</label>
                    <input type="text" name="rack_location" value="{{ old('rack_location') }}" required placeholder="Contoh: Rack A-01, Power Room Row-P"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('rack_location') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Rack Unit -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Unit Elevasi Rak (Rack Unit) *</label>
                    <input type="text" name="rack_unit" value="{{ old('rack_unit') }}" required placeholder="Contoh: U12-U13, Floor Standing"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('rack_unit') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Status Operasional Awal -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Status Operasional *</label>
                    <select name="status" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        <option value="operational" {{ old('status') === 'operational' ? 'selected' : '' }}>Operational (Beroperasi Normal)</option>
                        <option value="warning" {{ old('status') === 'warning' ? 'selected' : '' }}>Warning (Ada Anomali Non-Kritis)</option>
                        <option value="maintenance_required" {{ old('status') === 'maintenance_required' ? 'selected' : '' }}>Maintenance Required (Segera Perlu Servis)</option>
                    </select>
                    @error('status') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Kondisi Kesehatan Awal (%) -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Kondisi Kesehatan Fisik (0 - 100%) *</label>
                    <input type="number" name="condition_percent" value="{{ old('condition_percent', 100) }}" min="0" max="100" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('condition_percent') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Kontrak SLA Vendor -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Kontrak Pemeliharaan & SLA Vendor</label>
                <input type="text" name="vendor_sla" value="{{ old('vendor_sla') }}" placeholder="Contoh: Dell ProSupport Plus 24x7 4-Hour Onsite Response, Kontrak s.d Des 2028"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                @error('vendor_sla') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Catatan Teknis -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Catatan Konfigurasi & Topologi</label>
                <textarea name="notes" rows="3" placeholder="Informasi topologi jaringan, redundansi power supply, jalur kabel fiber optik, dsb."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">{{ old('notes') }}</textarea>
                @error('notes') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('components.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Komponen</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
