<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DC-Ops') - Sistem Pemeliharaan Pusat Data & Disaster Recovery</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        dc: {
                            dark: '#090d16',
                            card: '#0f172a',
                            border: '#1e293b',
                            accent: '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Mermaid.js for Diagrams -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            mermaid.initialize({
                startOnLoad: true,
                theme: 'dark',
                themeVariables: {
                    darkMode: true,
                    background: '#0f172a',
                    primaryColor: '#2563eb',
                    primaryTextColor: '#f8fafc',
                    primaryBorderColor: '#38bdf8',
                    lineColor: '#94a3b8',
                    secondaryColor: '#1e293b',
                    tertiaryColor: '#0f172a'
                }
            });
        });
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #06090e;
        }
        .code-font {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #090d16;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col md:flex-row antialiased overflow-x-hidden text-slate-200">

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-72 bg-[#090d16] border-r border-slate-800/80 flex flex-col shrink-0 z-30">
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600 via-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 text-white font-black text-xl">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-white tracking-tight flex items-center gap-1.5">
                        DC-OPS <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-950 text-cyan-400 border border-cyan-800/50 font-mono font-semibold">Tier 3</span>
                    </h1>
                    <p class="text-[11px] text-slate-400">Pusat Data & Pemeliharaan</p>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Operasional Utama</div>
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60' }}">
                <i class="fa-solid fa-chart-line w-5 text-center {{ request()->routeIs('dashboard') ? 'text-cyan-400' : 'text-slate-500' }}"></i>
                <span>Ringkasan Dashboard</span>
            </a>

            <a href="{{ route('components.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('components.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60' }}">
                <i class="fa-solid fa-server w-5 text-center {{ request()->routeIs('components.*') ? 'text-cyan-400' : 'text-slate-500' }}"></i>
                <span>Komponen Pusat Data</span>
            </a>

            <a href="{{ route('spareparts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('spareparts.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60' }}">
                <i class="fa-solid fa-microchip w-5 text-center {{ request()->routeIs('spareparts.*') ? 'text-cyan-400' : 'text-slate-500' }}"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span>Suku Cadang (Inventory)</span>
                </div>
            </a>

            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('maintenance.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60' }}">
                <i class="fa-solid fa-screwdriver-wrench w-5 text-center {{ request()->routeIs('maintenance.*') ? 'text-cyan-400' : 'text-slate-500' }}"></i>
                <span>Pemeliharaan & Log</span>
            </a>

            <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Keberlanjutan & Pelaporan</div>

            <a href="{{ route('backup.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('backup.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60' }}">
                <i class="fa-solid fa-shield-virus w-5 text-center {{ request()->routeIs('backup.*') ? 'text-emerald-400' : 'text-slate-500' }}"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span>Backup & Pemulihan (DR)</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-950 text-emerald-300 border border-emerald-800/40">AES-256</span>
                </div>
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('reports.*') ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900/60' }}">
                <i class="fa-solid fa-file-invoice w-5 text-center {{ request()->routeIs('reports.*') ? 'text-cyan-400' : 'text-slate-500' }}"></i>
                <span>Laporan Pemeliharaan</span>
            </a>

            <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-purple-400 flex items-center gap-1.5">
                <i class="fa-solid fa-certificate text-purple-400"></i>
                <span>Standar Sertifikasi SKKNI</span>
            </div>

            <a href="{{ route('skkni.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all {{ request()->routeIs('skkni.*') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40 shadow-lg shadow-purple-900/20' : 'text-purple-300/80 hover:text-purple-200 hover:bg-purple-950/40 border border-purple-900/40' }}">
                <i class="fa-solid fa-vial-circle-check w-5 text-center text-purple-400"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span>Pusat Kepatuhan SKKNI</span>
                    <span class="text-[9px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-purple-900 text-purple-200 font-bold animate-pulse">Lab</span>
                </div>
            </a>
        </nav>

        <!-- System & Database Indicator Footer -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-slate-300 font-medium">MySQL 8.4 Connected</span>
                </span>
                <span class="font-mono text-[10px] text-slate-400">root@127.0.0.1</span>
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between">
                <span>Database: <strong class="text-slate-300 font-mono">dc_maintenance</strong></span>
                <span class="text-[10px] bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded">PHP 8.3</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navbar -->
        <header class="h-16 bg-[#090d16]/90 backdrop-blur border-b border-slate-800/80 px-6 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <span class="text-xs text-slate-400 font-mono flex items-center gap-2">
                    <i class="fa-solid fa-server text-cyan-400"></i>
                    <span>Primary DC Cyber 1</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-emerald-400"><i class="fa-solid fa-circle text-[8px] mr-1"></i>SLA 99.982%</span>
                </span>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('skkni.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-950/80 text-purple-300 border border-purple-800/60 hover:bg-purple-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-flask-vial"></i>
                    <span>Buka Lab SKKNI</span>
                </a>
                <a href="{{ route('maintenance.schedules.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-600 hover:bg-cyan-500 text-white shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i>
                    <span>Rencanakan PM</span>
                </a>
                <a href="{{ route('maintenance.logs.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>Catat Pemeliharaan</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-xl bg-emerald-950/70 border border-emerald-700/60 text-emerald-200 flex items-start gap-3 shadow-lg shadow-emerald-950/30">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg mt-0.5"></i>
                    <div class="flex-1 text-sm leading-relaxed">
                        <strong class="font-semibold text-emerald-300">Berhasil:</strong> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 rounded-xl bg-rose-950/70 border border-rose-700/60 text-rose-200 flex items-start gap-3 shadow-lg shadow-rose-950/30">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg mt-0.5"></i>
                    <div class="flex-1 text-sm leading-relaxed">
                        <strong class="font-semibold text-rose-300">Peringatan/Kesalahan:</strong> {{ session('error') }}
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 mb-4 rounded-xl bg-amber-950/70 border border-amber-700/60 text-amber-200 flex items-start gap-3 shadow-lg shadow-amber-950/30">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 text-lg mt-0.5"></i>
                    <div class="flex-1 text-sm leading-relaxed">
                        <strong class="font-semibold text-amber-300">Informasi Pengujian:</strong> {{ session('warning') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Page Body Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 px-6 py-4 bg-[#090d16] text-xs text-slate-400 flex flex-col md:flex-row items-center justify-between gap-3">
            <div>
                © {{ date('Y') }} <strong>DC-OPS Enterprise</strong> - Sistem Manajemen Pemeliharaan Pusat Data & Disaster Recovery.
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Standar: SKKNI Software Development & IT Infrastructure</span>
                <span>•</span>
                <span>Enkripsi: AES-256-CBC</span>
                <span>•</span>
                <span>Database: MySQL 8.4</span>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
