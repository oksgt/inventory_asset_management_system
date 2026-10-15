<?php

namespace App\Services;

use App\Models\DcComponent;
use SplPriorityQueue;

class DataStructureBenchmarkService
{
    /**
     * Membandingkan Akses Hash Map / Associative Array O(1) vs Linear Search O(n)
     *
     * @return array<string, mixed>
     */
    public function benchmarkHashMapVsLinearSearch(int $iterations = 5000): array
    {
        $components = DcComponent::all();
        if ($components->isEmpty()) {
            return ['error' => 'Data komponen kosong'];
        }

        // Siapkan Linear Array dan Hash Map
        $linearList = [];
        $hashMap = [];
        $targetCode = $components->last()->component_code;

        foreach ($components as $comp) {
            $linearList[] = [
                'code' => $comp->component_code,
                'name' => $comp->name,
                'status' => $comp->status,
            ];
            $hashMap[$comp->component_code] = [
                'name' => $comp->name,
                'status' => $comp->status,
            ];
        }

        // 1. Uji Linear Search (O(n))
        $startLinear = hrtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $found = null;
            foreach ($linearList as $item) {
                if ($item['code'] === $targetCode) {
                    $found = $item;
                    break;
                }
            }
        }
        $linearTimeMs = (hrtime(true) - $startLinear) / 1e6;

        // 2. Uji Hash Map Key Lookup (O(1))
        $startHash = hrtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $found = $hashMap[$targetCode] ?? null;
        }
        $hashTimeMs = (hrtime(true) - $startHash) / 1e6;

        $speedup = $linearTimeMs > 0 ? round($linearTimeMs / max(0.0001, $hashTimeMs), 2) : 1;

        return [
            'iterations' => $iterations,
            'target_key' => $targetCode,
            'linear_search' => [
                'complexity' => 'O(n) Linear Time',
                'time_ms' => round($linearTimeMs, 4),
                'notes' => 'Iterasi berulang elemen array satu demi satu hingga kunci ditemukan.',
            ],
            'hash_map' => [
                'complexity' => 'O(1) Constant Time',
                'time_ms' => round($hashTimeMs, 4),
                'notes' => 'Akses langsung berdasarkan hash bucket memori PHP zval bucket.',
            ],
            'speedup_factor' => "{$speedup}x lebih cepat",
            'conclusion' => 'Struktur data Hash Map terbukti sangat efisien untuk lookup komponen serial number/kode berulang.',
        ];
    }

    /**
     * Membandingkan Binary Search O(log n) vs Linear Search O(n) pada dataset terurut
     *
     * @return array<string, mixed>
     */
    public function benchmarkBinaryVsLinear(int $size = 20000): array
    {
        // Bangkitkan serial number terurut
        $dataset = [];
        for ($i = 1; $i <= $size; $i++) {
            $dataset[] = sprintf('SN-%07d', $i);
        }

        $target = sprintf('SN-%07d', (int) ($size * 0.95)); // elemen di dekat akhir
        $iterations = 2000;

        // 1. Linear Search
        $startLinear = hrtime(true);
        for ($k = 0; $k < $iterations; $k++) {
            $idx = -1;
            for ($i = 0; $i < $size; $i++) {
                if ($dataset[$i] === $target) {
                    $idx = $i;
                    break;
                }
            }
        }
        $linearMs = (hrtime(true) - $startLinear) / 1e6;

        // 2. Binary Search (O(log n))
        $startBinary = hrtime(true);
        for ($k = 0; $k < $iterations; $k++) {
            $low = 0;
            $high = $size - 1;
            $idx = -1;
            while ($low <= $high) {
                $mid = (int) (($low + $high) / 2);
                $cmp = strcmp($dataset[$mid], $target);
                if ($cmp === 0) {
                    $idx = $mid;
                    break;
                } elseif ($cmp < 0) {
                    $low = $mid + 1;
                } else {
                    $high = $mid - 1;
                }
            }
        }
        $binaryMs = (hrtime(true) - $startBinary) / 1e6;

        $speedup = $linearMs > 0 ? round($linearMs / max(0.0001, $binaryMs), 2) : 1;

        return [
            'dataset_size' => $size,
            'target_element' => $target,
            'iterations' => $iterations,
            'linear_search' => [
                'complexity' => 'O(n)',
                'time_ms' => round($linearMs, 4),
            ],
            'binary_search' => [
                'complexity' => 'O(log n)',
                'time_ms' => round($binaryMs, 4),
            ],
            'speedup_factor' => "{$speedup}x lebih cepat",
        ];
    }

    /**
     * Demonstrasi Priority Queue (SplPriorityQueue) untuk Antrean Pemeliharaan Darurat Pusat Data
     *
     * @return array<string, mixed>
     */
    public function demonstratePriorityQueue(): array
    {
        $queue = new class extends SplPriorityQueue
        {
            public function compare(mixed $priority1, mixed $priority2): int
            {
                return $priority1 <=> $priority2;
            }
        };

        // Masukkan task pemeliharaan acak (urgensi: Critical = 100, High = 75, Medium = 50, Low = 25)
        $tasksInserted = [
            ['title' => 'Pembersihan Debu Rutin Server A1', 'priority_name' => 'Low', 'priority_weight' => 25],
            ['title' => 'OVERHEAT Kritis: PAC Cold Aisle Mencapai 34°C', 'priority_name' => 'Critical', 'priority_weight' => 100],
            ['title' => 'Penggantian Patch Cord OM4 Tertekuk', 'priority_name' => 'Medium', 'priority_weight' => 50],
            ['title' => 'ALARM UPS: Baterai String 2 Hilang Redundansi', 'priority_name' => 'High', 'priority_weight' => 85],
        ];

        foreach ($tasksInserted as $task) {
            $queue->insert($task, $task['priority_weight']);
        }

        // Dequeue berdasarkan prioritas tertinggi
        $dequeuedOrder = [];
        $step = 1;
        while ($queue->valid()) {
            $current = $queue->extract();
            $dequeuedOrder[] = [
                'dispatch_order' => $step++,
                'title' => $current['title'],
                'priority' => $current['priority_name'],
                'weight' => $current['priority_weight'],
            ];
        }

        return [
            'structure_type' => 'Max-Heap Priority Queue (SplPriorityQueue)',
            'input_order' => $tasksInserted,
            'dispatched_execution_order' => $dequeuedOrder,
            'explanation' => 'Meskipun tiket insiden masuk belakangan, tiket dengan bobot prioritas tertinggi (Critical / High) diekstrak pertama kali secara otomatis (O(log n) heap insertion/extraction).',
        ];
    }

    /**
     * Analisis Struktur Data & Konsep Data untuk Dokumen Evaluasi SKKNI
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDataStructureComparisonMatrix(): array
    {
        return [
            [
                'structure' => 'Relational Tuple (MySQL Tables)',
                'use_case' => 'Penyimpanan data aset, komponen, riwayat pemeliharaan, dan suku cadang',
                'pros' => 'ACID compliant, integritas relasi foreign key terjamin, dukungan indexing B-Tree, query join efisien',
                'cons' => 'Skema rigid membutuhkan migrasi, latensi I/O disk jika tidak di-cache',
                'complexity_access' => 'O(log n) via B-Tree Index, O(1) via Primary Key',
            ],
            [
                'structure' => 'Associative Array / Hash Map (PHP Key-Value)',
                'use_case' => 'Lookup cepat komponen berdasarkan kode/serial number di memori aplikasi',
                'pros' => 'Waktu akses konstan O(1), sangat cepat untuk pencarian dalam runtime',
                'cons' => 'Memori volatil, tidak terurut secara alami, konsumsi RAM seiring ukuran data',
                'complexity_access' => 'O(1) Average case',
            ],
            [
                'structure' => 'Binary Heap / Priority Queue (SplPriorityQueue)',
                'use_case' => 'Antrean dispatcher tiket maintenance darurat vs preventif',
                'pros' => 'Elemen prioritas darurat selalu berada di root heap, efisien untuk penjadwalan',
                'cons' => 'Pencarian elemen sembarang lambat O(n), tidak dapat diindeks acak',
                'complexity_access' => 'O(1) Peek max, O(log n) Insert & Extract',
            ],
            [
                'structure' => 'Laravel Eloquent Collection (Object Array Wrapper)',
                'use_case' => 'Transformasi data, filtering kondisi status, grouping per kategori di Controller/View',
                'pros' => 'Metode rantai deklaratif (filter, map, groupBy, reduce), mudah dibaca dan diuji',
                'cons' => 'Overhead alokasi objek memory jika memuat puluhan ribu baris tanpa pagination',
                'complexity_access' => 'O(n) untuk filter linear, O(1) untuk akses index',
            ],
            [
                'structure' => 'In-Memory Cache (Redis/File Memoization)',
                'use_case' => 'Penyimpanan metrik dashboard (kesehatan DC, total aset, stok kritis)',
                'pros' => 'Mengeliminasi beban query database berulang, respons sub-milidetik',
                'cons' => 'Memerlukan mekanisme invalidasi cache saat data diupdate',
                'complexity_access' => 'O(1) Key-Value lookup',
            ],
        ];
    }

    /**
     * Analisis Kebutuhan Framework/Library vs Kustomisasi Sendiri (KUK 1.1 - 1.2)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFrameworkAnalysisMatrix(): array
    {
        return [
            [
                'component' => 'Laravel Framework Core & Eloquent ORM',
                'scope' => 'Arsitektur MVC, database abstraction, routing, CSRF protection, middleware, database migration & seeder',
                'why_used' => 'Menyediakan pondasi enterprise-grade siap pakai dengan standar keamanan tinggi (CSRF, SQL Injection prevention via PDO prepared statements, bcrypt password hashing).',
                'vs_custom' => 'Mengembangkan router, ORM, dan query builder sendiri membutuhkan ratusan jam kerja rawan bug keamanan dan sulit dipelihara.',
                'limitations' => 'Overhead footprint memori lebih besar dibanding microframework ringan atau raw PHP, memerlukan konfigurasi PHP 8.2+.',
            ],
            [
                'component' => 'TailwindCSS Modern Styling Utility',
                'scope' => 'Tampilan antarmuka dashboard, dark/light theme, komponen responsif dan indikator visual status kesehatan komponen',
                'why_used' => 'Memungkinkan pembuatan UI enterprise modern, rapi, dan responsif dengan konsistensi token warna dan fleksibilitas tinggi tanpa CSS bloat.',
                'vs_custom' => 'Menulis Vanilla CSS dari nol untuk ratusan class responsif dan state button memakan waktu lama dan rentan inkonsistensi styling.',
                'limitations' => 'Daftar utility class di file HTML dapat menjadi panjang (dapat diatasi dengan komponen Blade).',
            ],
            [
                'component' => 'OpenSSL Cryptographic Extension (AES-256-CBC)',
                'scope' => 'Enkripsi dan dekripsi berkas backup informasi dan digest integritas SHA-256',
                'why_used' => 'Standar industri militer/keuangan bersertifikasi NIST untuk kerahasiaan data at-rest.',
                'vs_custom' => 'Jangan pernah membuat algoritma kriptografi sendiri karena rentan terhadap serangan kriptanalisis matematika.',
                'limitations' => 'Manajemen kunci rahasia (encryption key) harus dijaga dengan aman di file .env atau HSM.',
            ],
            [
                'component' => 'Mermaid.js Diagram Visualizer',
                'scope' => 'Rendering diagram ERD, Use Case, Sequence, Component, dan Class Diagram interaktif langsung di web browser',
                'why_used' => 'Memungkinkan representasi diagram sistem secara dinamis dan hidup (live rendering) dari teks deklaratif markdown.',
                'vs_custom' => 'Merancang engine grafis SVG parser sendiri sangat kompleks dan memerlukan ribuan baris kode matematika grafis.',
                'limitations' => 'Memerlukan kompilasi JS sisi klien (client-side render).',
            ],
        ];
    }
}
