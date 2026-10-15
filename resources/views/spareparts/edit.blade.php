@extends('layouts.app')

@section('title', 'Ubah Suku Cadang - ' . $sparepart->part_code)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('spareparts.index') }}" class="hover:text-slate-200">Suku Cadang</a>
            <span>/</span>
            <span class="font-mono text-cyan-400 font-semibold">{{ $sparepart->part_code }}</span>
            <span>/</span>
            <span class="text-slate-200">Ubah Data</span>
        </div>
        <a href="{{ route('spareparts.index') }}" class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Inventaris
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl">
        <div class="mb-6 pb-4 border-b border-slate-800">
            <h2 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-cyan-400"></i>
                <span>Ubah Data Suku Cadang</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Perbarui kuantitas stok fisik, ambang batas minimum alert, atau SOP penggantian.</p>
        </div>

        <form method="POST" action="{{ route('spareparts.update', $sparepart) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kategori -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Kategori Komponen *</label>
                    <select name="category_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $sparepart->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Kode Part -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Kode Suku Cadang *</label>
                    <input type="text" name="part_code" value="{{ old('part_code', $sparepart->part_code) }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500 uppercase">
                    @error('part_code') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Part -->
                <div class="md:col-span-2">
                    <label class="block text-slate-300 font-semibold mb-1.5">Nama & Deskripsi Suku Cadang *</label>
                    <input type="text" name="name" value="{{ old('name', $sparepart->name) }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('name') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Part Number -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Nomor Part Pabrikan (Part Number) *</label>
                    <input type="text" name="part_number" value="{{ old('part_number', $sparepart->part_number) }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('part_number') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Lokasi Bin -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Lokasi Rak/Bin Gudang</label>
                    <input type="text" name="location_bin" value="{{ old('location_bin', $sparepart->location_bin) }}"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('location_bin') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Stok Fisik -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Stok Fisik Saat Ini *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $sparepart->stock_quantity) }}" min="0" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('stock_quantity') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Batas Minimum Alert -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Batas Minimum Peringatan (Min Stock Alert) *</label>
                    <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', $sparepart->min_stock_alert) }}" min="1" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('min_stock_alert') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Satuan Unit *</label>
                    <input type="text" name="unit" value="{{ old('unit', $sparepart->unit) }}" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                    @error('unit') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Harga Satuan -->
                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Harga Per Satuan (Rp) *</label>
                    <input type="number" name="unit_cost" value="{{ old('unit_cost', (int)$sparepart->unit_cost) }}" min="0" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-cyan-500">
                    @error('unit_cost') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- SOP Penggantian -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Referensi SOP Penggantian Suku Cadang *</label>
                <input type="text" name="sop_replacement_ref" value="{{ old('sop_replacement_ref', $sparepart->sop_replacement_ref) }}" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">
                @error('sop_replacement_ref') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kompatibilitas -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Catatan Kompatibilitas Hardware</label>
                <textarea name="compatibility_notes" rows="2"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500">{{ old('compatibility_notes', $sparepart->compatibility_notes) }}</textarea>
                @error('compatibility_notes') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('spareparts.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
