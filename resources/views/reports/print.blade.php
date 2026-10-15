<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perencanaan & Pemeliharaan Pusat Data - {{ $period }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            body {
                background: #ffffff;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="p-8 max-w-5xl mx-auto text-slate-800 text-xs">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print mb-6 p-4 bg-slate-900 text-white rounded-xl flex items-center justify-between shadow-lg">
        <div>
            <h1 class="text-sm font-bold">Pratinjau Dokumen Cetak / Laporan Resmi Organisasi</h1>
            <p class="text-xs text-slate-400">Gunakan tombol di samping untuk mencetak fisik atau menyimpan sebagai file PDF.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 font-semibold rounded-lg text-xs shadow transition">
                Cetak / Ekspor PDF
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Organization Header / Kop Surat Resmi -->
    <div class="border-b-2 border-slate-900 pb-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Divisi Operasional & Infrastruktur Teknologi Informasi</div>
                <h1 class="text-xl font-extrabold uppercase tracking-tight text-slate-950">PUSAT DATA TIER-3 NASIONAL</h1>
                <p class="text-xs text-slate-600">Gedung Cyber 1 Lt. 3, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710 • Telp: (021) 5296-4000</p>
            </div>
            <div class="text-right space-y-1 mono text-[10px]">
                <div class="font-bold text-slate-900">NOMOR DOKUMEN: DC-OPS/PM/{{ str_replace('-', '/', $period) }}/014</div>
                <div class="text-slate-500">TANGGAL TERBIT: {{ date('d F Y') }}</div>
                <span class="inline-block px-2 py-0.5 bg-slate-200 text-slate-800 font-bold rounded">KLASIFIKASI: INTERNAL RAHASIA</span>
            </div>
        </div>
    </div>

    <!-- Title & Executive Summary -->
    <div class="text-center mb-6">
        <h2 class="text-base font-extrabold uppercase text-slate-900">LAPORAN PERENCANAAN & REALISASI PEMELIHARAAN PUSAT DATA</h2>
        <p class="text-xs text-slate-600 mt-1">Periode Pelaporan: <strong class="text-slate-900">{{ $dateFormatted }}</strong> • Fasilitas: Gedung Cyber 1 (Primary) & Cikarang (DRC)</p>
    </div>

    <!-- Bagian I: Ringkasan Eksekutif -->
    <div class="mb-6 space-y-2">
        <h3 class="font-bold uppercase text-[11px] text-slate-900 border-b border-slate-300 pb-1">I. RINGKASAN EKSEKUTIF & TINGKAT KETERSEDIAAN (SLA)</h3>
        <p class="text-[11px] text-slate-700 leading-relaxed text-justify">
            Berdasarkan standar operasional pusat data ANSI/TIA-942 Tier-3 dan standar sertifikasi SKKNI Pemeliharaan Komponen Pusat Data, 
            dilaporkan bahwa seluruh aset komputasi, storage, jaringan, catu daya UPS, dan sistem pendingin presisi (PAC) beroperasi dengan 
            tingkat ketersediaan <strong>99.982%</strong>. Total aset terpantau sebanyak <strong>{{ $components->count() }} unit</strong>, 
            dengan realisasi pemeliharaan tercatat sebanyak <strong>{{ $logs->count() }} kegiatan</strong> dan serapan anggaran pemeliharaan 
            sebesar <strong>Rp {{ number_format($totalRealizedCost, 0, ',', '.') }}</strong>.
        </p>
    </div>

    <!-- Bagian II: Status Inventaris Komponen Pusat Data -->
    <div class="mb-6 space-y-2">
        <h3 class="font-bold uppercase text-[11px] text-slate-900 border-b border-slate-300 pb-1">II. MATRIKS KLASIFIKASI KOMPONEN PUSAT DATA</h3>
        <table class="w-full text-left border border-slate-300 text-[10px]">
            <thead class="bg-slate-100 font-bold text-slate-900">
                <tr class="border-b border-slate-300">
                    <th class="p-1.5 border-r border-slate-300">Kode Komponen</th>
                    <th class="p-1.5 border-r border-slate-300">Nama Perangkat / Host</th>
                    <th class="p-1.5 border-r border-slate-300">Kategori</th>
                    <th class="p-1.5 border-r border-slate-300">Lokasi Rak</th>
                    <th class="p-1.5 border-r border-slate-300 text-center">Status</th>
                    <th class="p-1.5 border-r border-slate-300 text-center">Kondisi</th>
                    <th class="p-1.5">Kontrak SLA Vendor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($components as $c)
                    <tr>
                        <td class="p-1.5 font-bold mono border-r border-slate-300">{{ $c->component_code }}</td>
                        <td class="p-1.5 border-r border-slate-300 font-medium">{{ $c->name }}</td>
                        <td class="p-1.5 border-r border-slate-300">{{ $c->category->name }}</td>
                        <td class="p-1.5 mono border-r border-slate-300">{{ $c->rack_location }} ({{ $c->rack_unit }})</td>
                        <td class="p-1.5 text-center font-bold uppercase border-r border-slate-300">{{ $c->status }}</td>
                        <td class="p-1.5 text-center mono font-bold border-r border-slate-300">{{ $c->condition_percent }}%</td>
                        <td class="p-1.5 text-[9px]">{{ $c->vendor_sla ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bagian III: Perencanaan Suku Cadang Kritis -->
    <div class="mb-6 space-y-2">
        <div class="flex items-center justify-between border-b border-slate-300 pb-1">
            <h3 class="font-bold uppercase text-[11px] text-slate-900">III. PERENCANAAN PENGADAAN SUKU CADANG KRITIS (BUFFER STOCK)</h3>
            <span class="mono font-bold text-slate-900">Total Kebutuhan: Rp {{ number_format($totalEstimatedBudget, 0, ',', '.') }}</span>
        </div>
        <table class="w-full text-left border border-slate-300 text-[10px]">
            <thead class="bg-slate-100 font-bold text-slate-900">
                <tr class="border-b border-slate-300">
                    <th class="p-1.5 border-r border-slate-300">Kode Suku Cadang</th>
                    <th class="p-1.5 border-r border-slate-300">Nama & Spesifikasi</th>
                    <th class="p-1.5 border-r border-slate-300 text-center">Stok / Min</th>
                    <th class="p-1.5 border-r border-slate-300 text-center">Defisit</th>
                    <th class="p-1.5 border-r border-slate-300 text-right">Harga Satuan</th>
                    <th class="p-1.5 text-right">Estimasi Anggaran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 mono">
                @forelse($criticalSpareParts as $sp)
                    <tr>
                        <td class="p-1.5 font-bold border-r border-slate-300">{{ $sp->part_code }}</td>
                        <td class="p-1.5 font-sans border-r border-slate-300">{{ $sp->part_name }} (P/N: {{ $sp->part_number }})</td>
                        <td class="p-1.5 text-center border-r border-slate-300">{{ $sp->stock_quantity }} / {{ $sp->min_stock_alert }}</td>
                        <td class="p-1.5 text-center font-bold text-rose-700 border-r border-slate-300">+{{ $sp->stock_deficit }} {{ $sp->unit }}</td>
                        <td class="p-1.5 text-right border-r border-slate-300">Rp {{ number_format($sp->unit_cost, 0, ',', '.') }}</td>
                        <td class="p-1.5 text-right font-bold text-slate-900">Rp {{ number_format($sp->estimated_reorder_budget, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-2 text-center text-slate-500 font-sans">Semua suku cadang berada di atas ambang batas minimum.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Bagian IV: Jadwal Pemeliharaan Preventif -->
    <div class="mb-6 space-y-2">
        <h3 class="font-bold uppercase text-[11px] text-slate-900 border-b border-slate-300 pb-1">IV. JADWAL PEMELIHARAAN PREVENTIF TERENCANA</h3>
        <table class="w-full text-left border border-slate-300 text-[10px]">
            <thead class="bg-slate-100 font-bold text-slate-900">
                <tr class="border-b border-slate-300">
                    <th class="p-1.5 border-r border-slate-300">Komponen</th>
                    <th class="p-1.5 border-r border-slate-300">Aktivitas Pemeliharaan</th>
                    <th class="p-1.5 border-r border-slate-300">Referensi SOP</th>
                    <th class="p-1.5 border-r border-slate-300 text-center">Tanggal</th>
                    <th class="p-1.5 border-r border-slate-300">Teknisi</th>
                    <th class="p-1.5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($schedules as $sc)
                    <tr>
                        <td class="p-1.5 mono font-bold border-r border-slate-300">{{ $sc->component->component_code }}</td>
                        <td class="p-1.5 font-medium border-r border-slate-300">{{ $sc->title }}</td>
                        <td class="p-1.5 text-[9px] border-r border-slate-300">{{ $sc->sop_reference }}</td>
                        <td class="p-1.5 mono text-center border-r border-slate-300">{{ $sc->scheduled_date->format('d/m/Y') }}</td>
                        <td class="p-1.5 border-r border-slate-300">{{ $sc->assigned_technician }}</td>
                        <td class="p-1.5 text-center uppercase font-bold mono text-[9px]">{{ $sc->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bagian V: Lembar Pengesahan & Tanda Tangan Resmi (KUK 3.2) -->
    <div class="mt-10 pt-4 border-t-2 border-slate-900 text-[11px]">
        <div class="text-center font-bold uppercase mb-8">LEMBAR PENGESAHAN DOKUMEN PEMELIHARAAN PUSAT DATA</div>
        <div class="grid grid-cols-3 gap-8 text-center">
            <div class="space-y-16">
                <div>Dipersiapkan Oleh,<br><span class="font-bold">Lead Infrastructure Engineer</span></div>
                <div>
                    <div class="font-bold underline">Budi Santoso, S.Kom., CCIE</div>
                    <div class="text-[10px] text-slate-500 font-mono">NIP: 19880415 201201 1 002</div>
                </div>
            </div>

            <div class="space-y-16">
                <div>Diperiksa Oleh,<br><span class="font-bold">Auditor Sertifikasi SKKNI</span></div>
                <div>
                    <div class="font-bold underline">Dr. Sigit Oka, M.T., CISSP</div>
                    <div class="text-[10px] text-slate-500 font-mono">Asesor Kompetensi IT BNSP</div>
                </div>
            </div>

            <div class="space-y-16">
                <div>Disetujui Oleh,<br><span class="font-bold">Head of Data Center Operations</span></div>
                <div>
                    <div class="font-bold underline">Ir. H. Rahmat Hidayat, M.Kom.</div>
                    <div class="text-[10px] text-slate-500 font-mono">NIP: 19790822 200501 1 001</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
