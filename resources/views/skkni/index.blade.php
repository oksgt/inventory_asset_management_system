@extends('layouts.app')

@section('title', 'Pusat Kepatuhan SKKNI & Laboratorium Pengujian')

@section('content')
<div class="space-y-6">

    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-purple-950/80 via-slate-900 to-slate-900 p-6 rounded-2xl border border-purple-800/50 shadow-2xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-900 text-purple-200 border border-purple-700">
                        <i class="fa-solid fa-certificate mr-1 text-purple-300"></i> Standar Uji Kompetensi Nasional (SKKNI)
                    </span>
                    <span class="text-xs text-slate-400">Software Development & IT Infrastructure</span>
                </div>
                <h1 class="text-2xl font-black text-white tracking-tight">Pusat Kepatuhan & Laboratorium Teknis SKKNI</h1>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Modul pengujian langsung (Live Interactive Lab) dan dokumentasi komprehensif untuk membuktikan pemenuhan seluruh Elemen dan Kriteria Unjuk Kerja (KUK) secara riil pada aplikasi.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('reports.print') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak Laporan Pemeliharaan
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap items-center gap-1.5 border-b border-slate-800 text-xs font-semibold pb-1">
        <a href="{{ route('skkni.index', ['tab' => 'overview']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'overview' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Ringkasan KUK</span>
        </a>

        <a href="{{ route('skkni.index', ['tab' => 'library']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'library' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-cubes"></i>
            <span>Library & POC</span>
        </a>

        <a href="{{ route('skkni.index', ['tab' => 'datastructures']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'datastructures' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-network-wired"></i>
            <span>Struktur Data & Benchmark</span>
        </a>

        <a href="{{ route('skkni.index', ['tab' => 'diagrams']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'diagrams' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-diagram-project"></i>
            <span>Diagram & Pemodelan (ERD/UML)</span>
        </a>

        <a href="{{ route('skkni.index', ['tab' => 'sql']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'sql' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-database"></i>
            <span>Lab SQL (SP/Fn/Trigger/Commit/Rollback)</span>
        </a>

        <a href="{{ route('skkni.index', ['tab' => 'backup']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'backup' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Backup & Disaster Recovery</span>
        </a>

        <a href="{{ route('skkni.index', ['tab' => 'sop']) }}"
            class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 {{ $activeTab === 'sop' ? 'bg-purple-600 text-white shadow' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
            <i class="fa-solid fa-book"></i>
            <span>SOP Pemeliharaan & Suku Cadang</span>
        </a>
    </div>

    <!-- TAB 1: OVERVIEW & DAFTAR KUK -->
    @if($activeTab === 'overview')
        <div class="space-y-6 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Unit 1 -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800/40">Unit 1</span>
                    <h3 class="font-bold text-white text-sm">Menganalisis & Menggunakan Library/Komponen/Framework</h3>
                    <ul class="space-y-1 text-slate-300 list-disc list-inside text-[11px]">
                        <li>Identifikasi kebutuhan: Laravel, Eloquent ORM, OpenSSL AES-256, Mermaid.js, TailwindCSS.</li>
                        <li>Keuntungan vs membuat sendiri (keamanan, efisiensi waktu, standarisasi industri).</li>
                        <li>Proof of Concept (POC) & demonstrasi manfaat interaktif.</li>
                        <li>Rencana integrasi & identifikasi limitasi teknis.</li>
                    </ul>
                    <a href="{{ route('skkni.index', ['tab' => 'library']) }}" class="inline-block text-cyan-400 hover:underline font-semibold mt-1">Buka Analisis Library &rarr;</a>
                </div>

                <!-- Unit 2 -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800/40">Unit 2</span>
                    <h3 class="font-bold text-white text-sm">Konsep Data, Struktur Data & Algoritma Efisien</h3>
                    <ul class="space-y-1 text-slate-300 list-disc list-inside text-[11px]">
                        <li>Relational Tuples vs Associative Array/Hash Map vs Heap Priority Queue.</li>
                        <li>Akses efisien: Hash Table O(1) vs Linear O(n) (Uji benchmark live).</li>
                        <li>Binary Search O(log n) vs Linear Search O(n) pada serial number aset.</li>
                        <li>SplPriorityQueue untuk antrean tiket pemeliharaan darurat vs preventif.</li>
                    </ul>
                    <a href="{{ route('skkni.index', ['tab' => 'datastructures']) }}" class="inline-block text-cyan-400 hover:underline font-semibold mt-1">Jalankan Live Benchmark &rarr;</a>
                </div>

                <!-- Unit 3 -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800/40">Unit 3</span>
                    <h3 class="font-bold text-white text-sm">Entitas, Pemodelan Diagram & Metodologi Sistem</h3>
                    <ul class="space-y-1 text-slate-300 list-disc list-inside text-[11px]">
                        <li>Interactive ERD (Entity Relationship Diagram) 8 entitas relasional.</li>
                        <li>Use Case Diagram, Activity Diagram (Prosedur Pemeliharaan), Sequence Diagram.</li>
                        <li>Component Diagram & Class Diagram arsitektur MVC.</li>
                        <li>Metodologi pengembangan: Agile Scrum vs Waterfall vs RAD.</li>
                    </ul>
                    <a href="{{ route('skkni.index', ['tab' => 'diagrams']) }}" class="inline-block text-cyan-400 hover:underline font-semibold mt-1">Lihat Diagram Interaktif &rarr;</a>
                </div>

                <!-- Unit 4 -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800/40">Unit 4</span>
                    <h3 class="font-bold text-white text-sm">Fitur Aplikasi SQL & Database Terkelola</h3>
                    <ul class="space-y-1 text-slate-300 list-disc list-inside text-[11px]">
                        <li>DDL, DML, Indexing B-Tree (5 indeks komposit), dan 3 Database Views.</li>
                        <li>Stored Procedure: <code>sp_record_maintenance_activity</code> (dengan IN/OUT params).</li>
                        <li>Stored Functions: <code>fn_calculate_component_health</code> & cost aggregator.</li>
                        <li>Database Triggers: <code>trg_after_sparepart_stock_update</code> & log trigger.</li>
                        <li>ACID Transactions: Perintah COMMIT dan ROLLBACK interaktif.</li>
                    </ul>
                    <a href="{{ route('skkni.index', ['tab' => 'sql']) }}" class="inline-block text-cyan-400 hover:underline font-semibold mt-1">Buka Laboratorium SQL &rarr;</a>
                </div>

                <!-- Unit 5 -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800/40">Unit 5</span>
                    <h3 class="font-bold text-white text-sm">Tingkat Backup Informasi & Disaster Recovery (DRC)</h3>
                    <ul class="space-y-1 text-slate-300 list-disc list-inside text-[11px]">
                        <li>RPO (1 Jam), RTO (30 Menit), skema Full, Differential, dan Incremental.</li>
                        <li>Situs aman Offsite Cikarang Tier-3 (Jarak 52 KM di luar radius bencana utama).</li>
                        <li>Perlindungan fisik: Biometrik iris, man-trap interlock, gas pemadam Novec 1230.</li>
                        <li>Enkripsi berkas AES-256-CBC, hash SHA-256, dan simulator uji restorasi berkala.</li>
                    </ul>
                    <a href="{{ route('skkni.index', ['tab' => 'backup']) }}" class="inline-block text-cyan-400 hover:underline font-semibold mt-1">Buka Kebijakan Backup &rarr;</a>
                </div>

                <!-- Unit 6 -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800/40">Unit 6</span>
                    <h3 class="font-bold text-white text-sm">Klasifikasi Komponen Pusat Data & Suku Cadang</h3>
                    <ul class="space-y-1 text-slate-300 list-disc list-inside text-[11px]">
                        <li>Klasifikasi Server, Jaringan Core, Storage All-Flash, UPS Modular, PAC Cooling.</li>
                        <li>SOP Penggantian Suku Cadang Terisolasi ESD & Hot-Swap Storage.</li>
                        <li>Perencanaan suku cadang kritis dengan visualisasi batas minimum alert.</li>
                        <li>Laporan Pemeliharaan resmi terstandarisasi cetak PDF & arsip digital.</li>
                    </ul>
                    <a href="{{ route('skkni.index', ['tab' => 'sop']) }}" class="inline-block text-cyan-400 hover:underline font-semibold mt-1">Lihat Dokumen SOP &rarr;</a>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: LIBRARY & POC -->
    @if($activeTab === 'library')
        <div class="space-y-6 text-xs">
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-cubes text-cyan-400"></i>
                        <span>Analisis Library, Komponen, dan Framework (KUK 1.1 - 3.2)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Evaluasi komprehensif pemilihan framework, keuntungan penggunaan, rencana integrasi, dan batasan teknis.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-slate-800 text-xs">
                        <thead>
                            <tr class="bg-slate-950 text-slate-300 border-b border-slate-800 font-semibold uppercase text-[11px]">
                                <th class="p-3">Komponen / Library</th>
                                <th class="p-3">Ruang Lingkup Kebutuhan (1.1)</th>
                                <th class="p-3">Keuntungan vs Buat Sendiri (1.2)</th>
                                <th class="p-3">Limitasi Teknis (3.2)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($frameworkMatrix as $f)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="p-3 font-bold text-cyan-400">{{ $f['component'] }}</td>
                                    <td class="p-3 text-slate-300">{{ $f['scope'] }}</td>
                                    <td class="p-3 text-slate-300 leading-relaxed">{{ $f['why_used'] }}</td>
                                    <td class="p-3 text-amber-300/90">{{ $f['limitations'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Proof of Concept (POC) Demonstrator -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-flask text-purple-400"></i>
                            <span>Proof of Concept (POC) Library: Dynamic Health Score Engine</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Mendemonstrasikan manfaat integrasi Eloquent ORM + MySQL Stored Function secara real-time.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-purple-950 text-purple-300 border border-purple-800 text-[11px] font-mono font-bold">POC Active</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                    <span class="text-slate-300 font-semibold block">Pilih Komponen untuk Menghitung Skor via Stored Function:</span>
                    <div class="flex flex-wrap items-center gap-3">
                        <select id="poc-component-select" class="bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 font-mono text-xs focus:outline-none focus:border-cyan-500">
                            @foreach($components as $c)
                                <option value="{{ $c->id }}">[{{ $c->component_code }}] {{ $c->name }}</option>
                            @endforeach
                        </select>
                        <button onclick="runPocHealthCheck()" class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-semibold transition text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-play"></i>
                            <span>Eksekusi Stored Function fn_health()</span>
                        </button>
                    </div>

                    <div id="poc-result-box" class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 font-mono text-xs hidden">
                        <div class="text-cyan-400 font-bold mb-1">HASIL EKSEKUSI POC:</div>
                        <div id="poc-output" class="text-slate-300"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: DATA STRUCTURES & BENCHMARK -->
    @if($activeTab === 'datastructures')
        <div class="space-y-6 text-xs">
            <!-- Data Structure Comparison Matrix -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-network-wired text-cyan-400"></i>
                        <span>Identifikasi Konsep Data & Alternatif Struktur Data (KUK 1.1 & 1.2)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Analisis kelebihan, kekurangan, dan kompleksitas waktu akses struktur data.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border border-slate-800 text-xs">
                        <thead>
                            <tr class="bg-slate-950 text-slate-300 border-b border-slate-800 font-semibold uppercase text-[11px]">
                                <th class="p-3">Struktur Data</th>
                                <th class="p-3">Konteks Penerapan di DC</th>
                                <th class="p-3">Kelebihan</th>
                                <th class="p-3">Kekurangan</th>
                                <th class="p-3 font-mono">Kompleksitas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($dataStructureMatrix as $ds)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="p-3 font-bold text-cyan-400">{{ $ds['structure'] }}</td>
                                    <td class="p-3 text-slate-300">{{ $ds['use_case'] }}</td>
                                    <td class="p-3 text-emerald-300/90 leading-relaxed">{{ $ds['pros'] }}</td>
                                    <td class="p-3 text-amber-300/90 leading-relaxed">{{ $ds['cons'] }}</td>
                                    <td class="p-3 font-mono font-bold text-purple-300">{{ $ds['complexity_access'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Live Interactive Benchmark Runner -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-stopwatch text-purple-400"></i>
                            <span>Laboratorium Uji Benchmark Algoritma & Efisiensi Data (KUK 2.1 & 2.2)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Eksekusi benchmark langsung di server untuk menguji efisiensi algoritma secara real-time.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Benchmark 1 -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-cyan-400 block mb-1">Uji 1: Hash Map vs Linear</span>
                            <h4 class="font-bold text-white text-sm">O(1) Hash Map vs O(n) Scan</h4>
                            <p class="text-slate-400 text-[11px] mt-1 leading-relaxed">
                                Membandingkan waktu pencarian komponen menggunakan key associative array vs iterasi loop array linear (5.000 iterasi).
                            </p>
                        </div>
                        <button onclick="runBenchmark('hash_vs_linear')" class="w-full py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-semibold transition text-xs">
                            Jalankan Uji Hash Map
                        </button>
                    </div>

                    <!-- Benchmark 2 -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-purple-400 block mb-1">Uji 2: Binary Search</span>
                            <h4 class="font-bold text-white text-sm">O(log n) Binary vs O(n) Linear</h4>
                            <p class="text-slate-400 text-[11px] mt-1 leading-relaxed">
                                Membandingkan efisiensi pencarian serial number pada 20.000 record aset terurut (2.000 iterasi).
                            </p>
                        </div>
                        <button onclick="runBenchmark('binary_vs_linear')" class="w-full py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-semibold transition text-xs">
                            Jalankan Uji Binary Search
                        </button>
                    </div>

                    <!-- Benchmark 3 -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-emerald-400 block mb-1">Uji 3: Priority Queue</span>
                            <h4 class="font-bold text-white text-sm">SplPriorityQueue Max-Heap</h4>
                            <p class="text-slate-400 text-[11px] mt-1 leading-relaxed">
                                Demonstrasi dispatcher antrean tiket darurat (Overheat PAC & UPS Failure) diekstrak mendahului tiket rutin.
                            </p>
                        </div>
                        <button onclick="runBenchmark('priority_queue')" class="w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition text-xs">
                            Uji Priority Queue
                        </button>
                    </div>
                </div>

                <!-- Result Terminal -->
                <div id="benchmark-terminal" class="p-4 rounded-xl bg-slate-950 border border-slate-800 font-mono text-xs hidden space-y-2">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800 text-slate-400 text-[11px]">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-200 font-bold">TERMINAL HASIL BENCHMARK SERVER</span>
                        </span>
                        <span id="benchmark-timestamp"></span>
                    </div>
                    <pre id="benchmark-output" class="text-emerald-400 overflow-x-auto whitespace-pre-wrap leading-relaxed"></pre>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 4: DIAGRAMS & MODELING -->
    @if($activeTab === 'diagrams')
        <div class="space-y-6 text-xs">
            <!-- Diagram 1: ERD -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-diagram-project text-cyan-400"></i>
                            <span>1. Entity Relationship Diagram (ERD) - Sistem Pusat Data</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Memenuhi KUK: Mengidentifikasikan entitas beserta hubungannya secara efisien.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-cyan-950 text-cyan-300 border border-cyan-800 font-mono font-bold text-[11px]">Live Mermaid</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 overflow-x-auto flex justify-center">
                    <pre class="mermaid">
erDiagram
    dc_categories ||--o{ dc_components : "mengelompokkan (1:N)"
    dc_categories ||--o{ spare_parts : "memiliki suku cadang (1:N)"
    dc_components ||--o{ maintenance_schedules : "direncanakan (1:N)"
    dc_components ||--o{ maintenance_logs : "riwayat realisasi (1:N)"
    maintenance_schedules ||--o| maintenance_logs : "diselesaikan oleh (1:1)"
    maintenance_logs ||--o{ spare_part_usages : "menggunakan part (1:N)"
    spare_parts ||--o{ spare_part_usages : "dipotong stok (1:N)"
    backup_logs {
        bigint id PK
        string backup_name
        enum backup_type
        string checksum_sha256
        boolean is_encrypted
        enum status
    }
    audit_logs {
        bigint id PK
        string event_type
        string message
        json payload_json
    }
                    </pre>
                </div>
            </div>

            <!-- Diagram 2: Use Case Diagram -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-users-gear text-purple-400"></i>
                        <span>2. Use Case Diagram - Aktor & Fungsionalitas Sistem</span>
                    </h3>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 overflow-x-auto flex justify-center">
                    <pre class="mermaid">
flowchart LR
    Teknisi([Teknisi DC])
    Admin([Lead Operations])
    Auditor([Auditor SKKNI])

    subgraph DC_Manage [Sistem DC-Ops Enterprise]
        UC1(Kelola Aset & Kondisi Rak)
        UC2(Penjadwalan Pemeliharaan PM)
        UC3(Eksekusi Servis & Stored Procedure)
        UC4(Manajemen Suku Cadang & Trigger Alert)
        UC5(Pencadangan Data & Enkripsi AES-256)
        UC6(Uji Restorasi & Validasi SHA-256)
        UC7(Cetak Laporan Resmi Pemeliharaan)
    end

    Teknisi --> UC1
    Teknisi --> UC3
    Teknisi --> UC4
    Admin --> UC2
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Auditor --> UC6
    Auditor --> UC7
                    </pre>
                </div>
            </div>

            <!-- Diagram 3: Activity Diagram (SOP Pemeliharaan) -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2 pb-3 border-b border-slate-800">
                    <i class="fa-solid fa-arrow-progress text-emerald-400"></i>
                    <span>3. Activity Diagram - Alur Pemeliharaan & Penggantian Suku Cadang Terisolasi ESD</span>
                </h3>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 overflow-x-auto flex justify-center">
                    <pre class="mermaid">
flowchart TD
    Start([Mulai Pemeliharaan]) --> CheckSOP[Periksa Dokumen SOP & Gunakan Wrist-Strap ESD]
    CheckSOP --> Inspect[Inspeksi Anomali & Sensor Fisik Komponen]
    Inspect --> NeedPart{Perlu Penggantian Suku Cadang?}
    
    NeedPart -- Ya --> CheckStock{Stok Part Mencukupi?}
    CheckStock -- Tidak --> AlertProcure[Peringatan Stok Habis / Buat Pengadaan] --> Abort([Tunda Servis])
    CheckStock -- Ya --> DeductStock[Panggil Stored Procedure / Transaksi]
    DeductStock --> ReplacePart[Lakukan Penggantian Modul Fisik]
    
    NeedPart -- Tidak --> Cleaning[Pembersihan Debu & Pembaruan Firmware]
    
    ReplacePart --> VerifyTest[Uji Cold-Restart & Telemetri Suhu]
    Cleaning --> VerifyTest
    
    VerifyTest --> HealthOK{Skor Kesehatan >= 80%?}
    HealthOK -- Ya --> CommitDB[COMMIT Transaksi & Update Status Operational]
    HealthOK -- Tidak --> RollbackDB[ROLLBACK Transaksi & Catat Temuan Kerusakan]
    
    CommitDB --> GenerateLog[Terbitkan Log & Cetak Laporan Resmi] --> Selesai([Selesai])
    RollbackDB --> Selesai
                    </pre>
                </div>
            </div>

            <!-- Metodologi Pengembangan Aplikasi -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-3">
                <h3 class="text-base font-bold text-white pb-3 border-b border-slate-800">
                    4. Metodologi Pengembangan Perangkat Lunak (KUK 1.1 - 3.2)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-slate-950 border border-cyan-800/60 space-y-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-950 text-cyan-300">Metodologi Terpilih</span>
                        <h4 class="font-bold text-white text-sm">Agile Development (Scrum Framework)</h4>
                        <p class="text-slate-300 leading-relaxed text-[11px]">
                            Dipilih karena fleksibilitas tinggi terhadap perubahan kebutuhan inventaris dan integrasi bertahap (Sprint 1: Schema & Aset; Sprint 2: Pemeliharaan & SP; Sprint 3: DR Backup & Enkripsi).
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">Alternatif 1</span>
                        <h4 class="font-bold text-slate-300 text-sm">Waterfall (Linear Sequential)</h4>
                        <p class="text-slate-400 leading-relaxed text-[11px]">
                            Kaku terhadap perubahan spesifikasi di tengah jalan; risiko kegagalan integrasi tinggi jika ditemukan limitasi hardware di tahap akhir.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">Alternatif 2</span>
                        <h4 class="font-bold text-slate-300 text-sm">RAD (Rapid Application Development)</h4>
                        <p class="text-slate-400 leading-relaxed text-[11px]">
                            Bagus untuk prototyping cepat, tetapi sering mengorbankan dokumentasi arsitektur formal dan ketatnya standarisasi ACID transaksi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 5: SQL LAB (STORED PROCEDURE, FUNCTION, TRIGGER, COMMIT/ROLLBACK) -->
    @if($activeTab === 'sql')
        <div class="space-y-6 text-xs">
            <!-- SQL Lab Hero -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-flask-vial text-purple-400"></i>
                            <span>Laboratorium Fitur SQL: Stored Procedure, Function, Trigger, Commit & Rollback</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Memenuhi KUK: Mempersiapkan SQL, eksekusi DML, SP, Function, Trigger, dan Transaksi Commit/Rollback.</p>
                    </div>
                    <span class="font-mono text-xs text-emerald-400"><i class="fa-solid fa-server mr-1"></i>MySQL 8.4 Connected</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left: Stored Procedure Tester -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-gears text-purple-400"></i>
                                <span>1. Uji Stored Procedure (CALL sp_record_maintenance_activity)</span>
                            </h4>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Prosedur SQL menerima parameter IN teknisi, tindakan, dan suku cadang, lalu mengembalikan OUT parameter <code>p_new_log_id</code>, <code>p_status_code</code>, dan <code>p_message</code>.
                        </p>

                        <div class="space-y-2.5">
                            <div>
                                <label class="block text-slate-400 mb-1">Komponen Target</label>
                                <select id="sp-comp-id" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-slate-200 font-mono text-xs">
                                    @foreach($components as $c)
                                        <option value="{{ $c->id }}">[{{ $c->component_code }}] {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-slate-400 mb-1">Gunakan Suku Cadang (Potong Stok Otomatis)</label>
                                <select id="sp-part-id" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-slate-200 font-mono text-xs">
                                    <option value="">-- Tanpa Suku Cadang --</option>
                                    @foreach($spareParts as $p)
                                        <option value="{{ $p->id }}">[{{ $p->part_code }}] {{ $p->name }} (Sisa: {{ $p->stock_quantity }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <button onclick="executeSpLiveTest()" class="w-full py-2.5 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-semibold transition text-xs flex items-center justify-center gap-2">
                                <i class="fa-solid fa-play"></i> Eksekusi CALL Stored Procedure
                            </button>

                            <div id="sp-output-box" class="p-3 rounded-lg bg-slate-900 border border-slate-800 font-mono text-[11px] hidden">
                                <div class="text-cyan-400 font-bold mb-1">OUT PARAMETERS HASIL SP:</div>
                                <pre id="sp-output-text" class="text-slate-300"></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Commit vs Rollback Simulator -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                        <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-scale-balanced text-emerald-400"></i>
                            <span>2. Simulasi Transaksi: Perintah COMMIT vs ROLLBACK (KUK 8.1 & 8.2)</span>
                        </h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Uji coba atomisitas transaksi MySQL InnoDB. Mode COMMIT memastikan integritas data multi-tabel, sedangkan Mode ROLLBACK membuktikan tidak ada data rusak/orphan record yang tertinggal saat terjadi kegagalan.
                        </p>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <button onclick="runTransactionDemo('commit')" class="p-3 rounded-xl bg-emerald-950/80 hover:bg-emerald-900 text-emerald-200 border border-emerald-800/80 font-semibold transition text-center space-y-1">
                                <i class="fa-solid fa-check-circle text-lg text-emerald-400 block"></i>
                                <span class="block text-xs font-bold">Uji Operasi COMMIT</span>
                                <span class="block text-[10px] text-slate-400 font-mono font-normal">Data Valid Tersimpan</span>
                            </button>

                            <button onclick="runTransactionDemo('rollback')" class="p-3 rounded-xl bg-rose-950/80 hover:bg-rose-900 text-rose-200 border border-rose-800/80 font-semibold transition text-center space-y-1">
                                <i class="fa-solid fa-rotate-left text-lg text-rose-400 block"></i>
                                <span class="block text-xs font-bold">Uji Operasi ROLLBACK</span>
                                <span class="block text-[10px] text-slate-400 font-mono font-normal">Batalkan Seluruh Data</span>
                            </button>
                        </div>

                        <div id="tx-output-box" class="p-3 rounded-lg bg-slate-900 border border-slate-800 font-mono text-[11px] hidden">
                            <div class="text-cyan-400 font-bold mb-1">HASIL TRANSAKSI DATABASE:</div>
                            <pre id="tx-output-text" class="text-slate-300 whitespace-pre-wrap leading-relaxed"></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Views & Indexes Explorer -->
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 pb-3 border-b border-slate-800">
                    <i class="fa-solid fa-table text-cyan-400"></i>
                    <span>Database Views & Indeks B-Tree yang Telah Dibangkitkan (KUK 3.2 & 3.3)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Views List -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="text-xs font-bold text-slate-300 block">Daftar MySQL Views Aktif:</span>
                        <ul class="space-y-2 text-[11px] font-mono">
                            <li class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">
                                <strong class="text-cyan-400 block">view_component_maintenance_status</strong>
                                Menggabungkan tabel dc_components & dc_categories dengan kalkulasi status kritis DATEDIFF().
                            </li>
                            <li class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">
                                <strong class="text-amber-400 block">view_critical_spareparts</strong>
                                Memfilter suku cadang dengan stok <= min_stock_alert beserta estimasi kebutuhan reorder budget.
                            </li>
                            <li class="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">
                                <strong class="text-emerald-400 block">view_monthly_maintenance_summary</strong>
                                Agregasi bulanan total aktivitas, biaya, dan rata-rata skor kesehatan tercapai.
                            </li>
                        </ul>
                    </div>

                    <!-- Indexes List -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="text-xs font-bold text-slate-300 block">Indeks B-Tree Tambahan (Optimalisasi Query):</span>
                        <ul class="space-y-1.5 text-[11px] font-mono">
                            @foreach($indexes as $idx)
                                <li class="p-2 rounded bg-slate-900 border border-slate-800 flex items-center justify-between text-slate-300">
                                    <span class="text-cyan-400 font-bold">{{ $idx->Key_name }}</span>
                                    <span class="text-slate-400">Kolom: {{ $idx->Column_name }} ({{ $idx->Index_type }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 6: BACKUP & DRC POLICY -->
    @if($activeTab === 'backup')
        <div class="space-y-6 text-xs">
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-shield-virus text-emerald-400"></i>
                            <span>Dokumentasi Standar Backup Informasi & Pemulihan Bencana (KUK 1.1 - 8.3)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Kepatuhan standar RPO, RTO, pemilihan lokasi aman offsite, enkripsi AES-256, dan retensi data.</p>
                    </div>
                    <a href="{{ route('backup.index') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition">
                        Buka Konsol Backup &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tingkat Backup & Frekuensi -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <h4 class="font-bold text-white text-sm">1. Tingkat Backup & Frekuensi (KUK 1.1 - 1.3)</h4>
                        <ul class="space-y-1.5 text-slate-300 list-disc list-inside text-[11px] leading-relaxed">
                            <li><strong>Full Backup:</strong> Setiap hari Minggu pukul 01:00 WIB (Arsip lengkap skema dan data DML).</li>
                            <li><strong>Differential Backup:</strong> Setiap hari Rabu pukul 02:00 WIB (Perubahan sejak full backup terakhir).</li>
                            <li><strong>Incremental Backup:</strong> Setiap hari (Senin, Selasa, Kamis, Jumat, Sabtu) pukul 01:00 WIB (Perubahan 24 jam terakhir).</li>
                            <li><strong>RPO 1 Jam:</strong> Toleransi kehilangan transaksi maksimal 1 jam operasional.</li>
                            <li><strong>RTO 30 Menit:</strong> Waktu maksimal pemulihan sistem dari snapshot replikasi aktif.</li>
                        </ul>
                    </div>

                    <!-- Enkripsi & Retensi -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <h4 class="font-bold text-white text-sm">2. Standar Enkripsi & Periode Retensi (KUK 5.1 - 6.2)</h4>
                        <ul class="space-y-1.5 text-slate-300 list-disc list-inside text-[11px] leading-relaxed">
                            <li><strong>Algoritma Enkripsi:</strong> AES-256-CBC dengan random 16-byte IV per berkas.</li>
                            <li><strong>Key Derivation:</strong> PBKDF2 dengan HMAC-SHA256 (100.000 iterasi).</li>
                            <li><strong>Integritas Data:</strong> Checksum SHA-256 digest untuk menjamin data tidak mengalami bit-rot.</li>
                            <li><strong>Retensi Harian:</strong> 7 hari pada disk lokal SAN Tier-1.</li>
                            <li><strong>Retensi Mingguan:</strong> 4 minggu pada secondary storage offsite Cikarang.</li>
                            <li><strong>Retensi Bulanan:</strong> 12 bulan pada immutable cold storage.</li>
                        </ul>
                    </div>
                </div>

                <!-- Lokasi Fasilitas Aman -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                    <h4 class="font-bold text-white text-sm">3. Pemilihan Lokasi Backup di Jarak yang Aman (KUK 3.1 - 4.2)</h4>
                    <p class="text-slate-300 leading-relaxed text-[11px]">
                        Lokasi utama di <strong>Gedung Cyber 1 Jakarta</strong> memiliki profil risiko bencana gempa patahan Jawa dan banjir dataran rendah. 
                        Situs cadangan (DRC) ditempatkan di <strong>Kawasan Industri Cikarang, Jawa Barat</strong> dengan jarak <strong>52.4 KM</strong>. 
                        Jarak ini memenuhi standar Perka BSSN dan SNI ISO/IEC 27001 (jarak aman minimum > 40 KM dari zona bencana utama). 
                        Fasilitas DRC dilengkapi proteksi fisik mantrap pintu biometrik, pagar kawat mikrofonik, serta sistem pemadam gas Novec 1230.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 7: SOP PEMELIHARAAN & SUKU CADANG -->
    @if($activeTab === 'sop')
        <div class="space-y-6 text-xs">
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-book text-cyan-400"></i>
                            <span>Standar Operasional Prosedur (SOP) Pemeliharaan Komponen & Suku Cadang (KUK 1.2 & 2.2)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Dokumentasi SOP pemeliharaan berkala, pencegahan ESD, dan tata cara penggantian suku cadang.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- SOP 1 -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-950 text-cyan-300 font-mono">SOP-PM-01</span>
                        <h4 class="font-bold text-white text-sm">SOP Pemeliharaan Rutin Server & Rackmount Unit 90 Hari</h4>
                        <ol class="space-y-1 text-slate-300 list-decimal list-inside text-[11px] leading-relaxed">
                            <li>Koordinasikan maintenance window dengan penanggung jawab aplikasi.</li>
                            <li>Verifikasi status replikasi database kluster telah berpindah ke standby node.</li>
                            <li>Gunakan pakaian antistatis dan hubungkan wrist-strap ESD ke grounding rack.</li>
                            <li>Buka cover chassis, bersihkan sirip heatsink dan bilah kipas dengan vacuum ESD.</li>
                            <li>Periksa log SMART drive NVMe dan event log iDRAC/IPMI.</li>
                            <li>Lakukan update firmware berkala dan catat ke dalam log pemeliharaan DC-Ops.</li>
                        </ol>
                    </div>

                    <!-- SOP 2 -->
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-300 font-mono">SOP-SP-02</span>
                        <h4 class="font-bold text-white text-sm">SOP Penggantian Suku Cadang Storage Hot-Swap</h4>
                        <ol class="space-y-1 text-slate-300 list-decimal list-inside text-[11px] leading-relaxed">
                            <li>Identifikasi nomor bay disk drive yang mengalami amber alarm.</li>
                            <li>Verifikasi stok suku cadang di gudang DC (pastikan P/N dan kapasitas identik).</li>
                            <li>Beri perintah drive offline melalui antarmuka storage controller.</li>
                            <li>Tarik tuas ejector secara perlahan, tunggu motor spin-down 15 detik sebelum dicabut.</li>
                            <li>Pasang drive baru ke dalam caddy, dorong hingga mengunci sempurna.</li>
                            <li>Monitor proses automatic background rebuild RAID-DP hingga 100% konsisten.</li>
                            <li>Kurangi stok suku cadang pada sistem DC-Ops via Stored Procedure.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // AJAX CSRF Setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 1. Eksekusi Live Benchmark
    function runBenchmark(type) {
        const terminal = document.getElementById('benchmark-terminal');
        const output = document.getElementById('benchmark-output');
        const timestamp = document.getElementById('benchmark-timestamp');

        terminal.classList.remove('hidden');
        output.innerText = "Menjalankan kalkulasi benchmark di server MySQL & PHP runtime...";

        fetch("{{ route('skkni.benchmark') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ type: type })
        })
        .then(res => res.json())
        .then(data => {
            timestamp.innerText = new Date().toLocaleTimeString();
            output.innerText = JSON.stringify(data, null, 2);
        })
        .catch(err => {
            output.innerText = "Gagal mengeksekusi benchmark: " + err;
        });
    }

    // 2. Eksekusi Live Stored Procedure Tester
    function executeSpLiveTest() {
        const compId = document.getElementById('sp-comp-id').value;
        const partId = document.getElementById('sp-part-id').value;
        const box = document.getElementById('sp-output-box');
        const text = document.getElementById('sp-output-text');

        box.classList.remove('hidden');
        text.innerText = "Memanggil Stored Procedure sp_record_maintenance_activity via PDO...";

        fetch("{{ route('skkni.test-procedure') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                component_id: compId,
                title: "Uji Stored Procedure Lab SKKNI",
                technician_name: "Auditor Evaluator SKKNI",
                action_type: "preventive",
                actions_taken: "Pembersihan rutin sensor rack & kalibrasi parameter melalui SP",
                findings: "Sistem merespons dengan kode SUCCESS",
                health_after: 98,
                part_id: partId ? partId : null,
                part_qty: partId ? 1 : null
            })
        })
        .then(res => res.json())
        .then(data => {
            text.innerText = JSON.stringify(data, null, 2);
        })
        .catch(err => {
            text.innerText = "Error: " + err;
        });
    }

    // 3. Eksekusi Live Transaksi Commit vs Rollback
    function runTransactionDemo(mode) {
        const box = document.getElementById('tx-output-box');
        const text = document.getElementById('tx-output-text');

        box.classList.remove('hidden');
        text.innerText = "Mengeksekusi simulasi transaksi mode: " + mode.toUpperCase() + "...";

        fetch("{{ route('skkni.test-transaction') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ mode: mode })
        })
        .then(res => res.json())
        .then(data => {
            text.innerText = JSON.stringify(data, null, 2);
        })
        .catch(err => {
            text.innerText = "Error: " + err;
        });
    }

    // 4. Eksekusi POC Stored Function
    function runPocHealthCheck() {
        const compId = document.getElementById('poc-component-select').value;
        const box = document.getElementById('poc-result-box');
        const out = document.getElementById('poc-output');

        box.classList.remove('hidden');
        out.innerText = "Memanggil MySQL fn_calculate_component_health(" + compId + ")...";

        fetch("{{ route('skkni.test-function') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ component_id: compId })
        })
        .then(res => res.json())
        .then(data => {
            out.innerHTML = `Skor Kesehatan Terkalkulasi: <strong class="text-purple-400 font-bold">${data.calculated_health}%</strong> | Total Biaya Servis: <strong class="text-emerald-400 font-bold">Rp ${Number(data.total_maintenance_cost).toLocaleString('id-ID')}</strong>`;
        })
        .catch(err => {
            out.innerText = "Error: " + err;
        });
    }
</script>
@endpush
