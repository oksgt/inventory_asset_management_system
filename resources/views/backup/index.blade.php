@extends('layouts.app')

@section('title', 'Backup & Disaster Recovery Management')

@section('content')
<div class="space-y-6">

    <!-- Header & Trigger Backup Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-shield-virus text-emerald-400"></i>
                <span>Backup & Pemulihan Bencana (Disaster Recovery Center)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Pengelolaan siklus backup (Full, Incremental, Differential), enkripsi AES-256-CBC, replikasi offsite, dan uji restorasi berkala.</p>
        </div>

        <button onclick="document.getElementById('modal-backup').classList.remove('hidden')"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>Buat Arsip Cadangan Baru</span>
        </button>
    </div>

    <!-- DR Policy & Business Metrics Banner -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
        <!-- RPO -->
        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">RPO (Recovery Point Objective)</span>
            <div class="text-2xl font-bold font-mono text-cyan-400 mt-1">1 Jam</div>
            <p class="text-[11px] text-slate-400 mt-1">Toleransi kehilangan data maksimal</p>
        </div>

        <!-- RTO -->
        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">RTO (Recovery Time Objective)</span>
            <div class="text-2xl font-bold font-mono text-emerald-400 mt-1">30 Menit</div>
            <p class="text-[11px] text-slate-400 mt-1">Target pemulihan operasional layanan</p>
        </div>

        <!-- Enkripsi Standar -->
        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">Standar Enkripsi Data</span>
            <div class="text-xl font-bold font-mono text-purple-300 mt-1">AES-256-CBC</div>
            <p class="text-[11px] text-slate-400 mt-1">PBKDF2 HMAC-SHA256 (100k iterasi)</p>
        </div>

        <!-- Total Storage & Verification -->
        <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800">
            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[10px] block">Status Media Terverifikasi</span>
            <div class="text-2xl font-bold font-mono text-white mt-1">{{ $verifiedCount }} / {{ $totalBackups }}</div>
            <p class="text-[11px] text-emerald-400 mt-1 font-medium"><i class="fa-solid fa-check-double mr-1"></i>Integritas Teruji</p>
        </div>
    </div>

    <!-- Spesifikasi Lokasi Aman & Perlindungan Fisik (Sesuai KUK) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-sm space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-rose-400"></i>
                <h3 class="text-sm font-bold text-white">Spesifikasi Fasilitas Backup & Jarak Aman (Disaster Recovery Site)</h3>
            </div>
            <span class="text-[11px] font-mono text-slate-400">Standar: SNI ISO/IEC 27001 & TIA-942</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Lokasi Utama -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-200">Situs Utama (Primary Site):</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-950 text-cyan-400 border border-cyan-800/40">Tier 3</span>
                </div>
                <div class="font-mono text-cyan-300 text-[11px]">{{ $specs['primary_site']['name'] }}</div>
                <div class="text-slate-400 leading-relaxed">
                    <strong>Profil Bencana:</strong> {{ $specs['primary_site']['threat_profile'] }}
                </div>
            </div>

            <!-- Lokasi Cadangan (Offsite Safe Location) -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-emerald-300">Situs Cadangan (Secondary DRC Offsite):</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-800/40">
                        Jarak: {{ $specs['secondary_site']['distance_km'] }} KM
                    </span>
                </div>
                <div class="font-mono text-emerald-300 text-[11px]">{{ $specs['secondary_site']['name'] }}</div>
                <div class="text-slate-400 leading-relaxed">
                    <strong>Justifikasi Zona Aman:</strong> {{ $specs['secondary_site']['safe_zone_justification'] }}
                </div>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-300 grid grid-cols-1 md:grid-cols-3 gap-3 text-[11px]">
            <div>
                <strong class="text-white block mb-0.5"><i class="fa-solid fa-fingerprint mr-1 text-cyan-400"></i> Keamanan Fisik:</strong>
                {{ $specs['physical_protection']['access_control'] }}
            </div>
            <div>
                <strong class="text-white block mb-0.5"><i class="fa-solid fa-fire-extinguisher mr-1 text-rose-400"></i> Pemadam Lingkungan:</strong>
                {{ $specs['physical_protection']['fire_suppression'] }}
            </div>
            <div>
                <strong class="text-white block mb-0.5"><i class="fa-solid fa-hard-drive mr-1 text-purple-400"></i> Kapasitas Pool SAN:</strong>
                {{ $specs['storage_capacity_calculation']['required_monthly_pool_gb'] }} GB dibutuhkan (Pool 500 GB, utilisasi {{ $specs['storage_capacity_calculation']['utilization_percent'] }}%)
            </div>
        </div>
    </div>

    <!-- Catatan Administrasi Backup (Tabel Arsip Sesuai KUK) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-database text-cyan-400"></i>
                <span>Catatan Administrasi & Daftar Arsip Backup (Backup Registry)</span>
            </h3>
            <span class="text-xs text-slate-400 font-mono">Tabel: backup_logs</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/70 text-slate-400 border-b border-slate-800 uppercase tracking-wider font-semibold font-mono">
                        <th class="py-3 px-4">Nama Arsip Backup</th>
                        <th class="py-3 px-4">Tipe & Lokasi</th>
                        <th class="py-3 px-4">Ukuran & Retensi</th>
                        <th class="py-3 px-4">Integritas Checksum (SHA-256)</th>
                        <th class="py-3 px-4">Status Pengujian</th>
                        <th class="py-3 px-4 text-right">Aksi & Pengujian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($backups as $b)
                        <tr class="hover:bg-slate-800/30 transition">
                            <!-- Nama Berkas -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-slate-200">{{ $b->backup_name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">Dibuat: {{ $b->created_at->format('d M Y H:i') }}</div>
                                @if($b->is_encrypted)
                                    <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-mono font-bold text-purple-400 bg-purple-950 px-1.5 py-0.5 rounded border border-purple-800/50">
                                        <i class="fa-solid fa-lock text-[9px]"></i> {{ $b->encryption_cipher }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tipe & Lokasi -->
                            <td class="py-3.5 px-4 font-mono">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $b->backup_type === 'full' ? 'bg-cyan-950 text-cyan-400 border border-cyan-800/60' : 'bg-slate-800 text-slate-300' }}">
                                    {{ $b->backup_type }}
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1 font-sans">
                                    {{ str_replace('_', ' ', $b->storage_location) }}
                                </div>
                            </td>

                            <!-- Ukuran & Retensi -->
                            <td class="py-3.5 px-4 font-mono">
                                <div class="font-bold text-slate-200">{{ $b->formatted_size }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Retensi: {{ $b->retention_days }} Hari</div>
                            </td>

                            <!-- Checksum SHA-256 -->
                            <td class="py-3.5 px-4 font-mono text-[11px] max-w-xs">
                                <span class="text-slate-300 truncate block font-mono" title="{{ $b->checksum_sha256 }}">
                                    {{ substr($b->checksum_sha256, 0, 16) }}...{{ substr($b->checksum_sha256, -8) }}
                                </span>
                                <span class="text-[10px] text-emerald-400/90 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Tamper-Proof Digest
                                </span>
                            </td>

                            <!-- Status Pengujian -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $b->status === 'verified' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800/60' :
                                      ($b->status === 'failed' ? 'bg-rose-950 text-rose-400 border border-rose-800/60' : 'bg-slate-800 text-slate-300') }}">
                                    {{ $b->status }}
                                </span>
                                @if($b->tested_at)
                                    <div class="text-[10px] font-mono text-slate-400 mt-1">Uji: {{ $b->tested_at->diffForHumans() }}</div>
                                @endif
                            </td>

                            <!-- Aksi & Uji Restorasi -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol Uji Restorasi (KUK 8.1 - 8.3) -->
                                    <form method="POST" action="{{ route('backup.test-restore', $b) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded bg-purple-950 hover:bg-purple-900 text-purple-300 border border-purple-800/60 text-xs font-semibold transition" title="Jalankan Uji Restorasi & Validasi Integritas">
                                            <i class="fa-solid fa-vial-circle-check mr-1"></i> Uji Restorasi
                                        </button>
                                    </form>

                                    <!-- Download -->
                                    <a href="{{ route('backup.download', $b) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Unduh Berkas">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada catatan backup dalam registry.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backups->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $backups->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Pembuatan Backup Baru -->
<div id="modal-backup" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 rounded-2xl border border-slate-800 max-w-lg w-full p-6 shadow-2xl space-y-5 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-emerald-400"></i>
                <span>Pencadangan Basis Data & Konfigurasi Sistem</span>
            </h3>
            <button onclick="document.getElementById('modal-backup').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('backup.create') }}" class="space-y-4">
            @csrf

            <!-- Tipe Backup -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Tipe Pencadangan *</label>
                <select name="backup_type" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500 font-sans">
                    <option value="full">Full Backup (Seluruh Skema DDL & Data DML)</option>
                    <option value="differential">Differential Backup (Perubahan Sejak Full Terakhir)</option>
                    <option value="incremental">Incremental Backup (Perubahan Sejak Backup Terakhir)</option>
                </select>
            </div>

            <!-- Lokasi Penyimpanan -->
            <div>
                <label class="block text-slate-300 font-semibold mb-1.5">Lokasi Target Penyimpanan *</label>
                <select name="storage_location" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-200 focus:outline-none focus:border-cyan-500 font-sans">
                    <option value="primary_local">Primary Local SAN Storage (Cyber 1 DC)</option>
                    <option value="secondary_offsite_cikarang">Secondary Offsite DRC Cikarang (Jarak 52 KM)</option>
                    <option value="immutable_cloud">Immutable Cloud Object Storage (WORM Archive)</option>
                </select>
            </div>

            <!-- Opsi Enkripsi AES-256 -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="encrypt" value="1" checked class="rounded text-purple-600 focus:ring-purple-500">
                    <span class="font-bold text-purple-300">Enkripsi Berkas Menggunakan AES-256-CBC (Direkomendasikan)</span>
                </label>
                <p class="text-[11px] text-slate-400 pl-6 leading-relaxed">
                    Kunci enkripsi diturunkan secara kriptografis menggunakan algoritma HMAC-SHA256 sesuai standar kerahasiaan SKKNI KUK 5.1 & 5.2.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-backup').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-play text-xs"></i>
                    <span>Eksekusi Backup Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
