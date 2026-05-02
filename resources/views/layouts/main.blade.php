<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM Uy')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar.open { transform: translateX(0) !important; }
        .overlay.show { display: block !important; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
    <button id="menuToggle" class="md:hidden fixed top-4 left-4 z-50 bg-slate-950 text-white p-3 rounded-2xl shadow-2xl ring-2 ring-slate-900/10">
        <i class="fas fa-bars"></i>
    </button>

    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-full max-w-[240px] md:w-64 bg-slate-950/95 backdrop-blur-xl border-r border-white/10 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto">
        <div class="flex min-h-full flex-col px-4 py-6">
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-gradient-to-br from-cyan-400 to-blue-600 text-white shadow-xl">
                        <i class="fas fa-building text-base"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-white">CRM Uy</h1>
                        <p class="text-slate-400 text-xs">Uy sotish boshqaruvi</p>
                    </div>
                </div>
                <div class="rounded-3xl bg-slate-900/90 p-4 border border-white/10 shadow-2xl">
                    <p class="text-slate-400 text-[10px] uppercase tracking-[0.35em] mb-3">Tezkor</p>
                    <div class="space-y-2">
                        <div class="rounded-3xl bg-slate-950 p-3">
                            <p class="text-[10px] uppercase tracking-[0.25em] text-slate-500">Bugun</p>
                            <p class="text-lg font-semibold text-white">12 ta</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950 p-3">
                            <p class="text-[10px] uppercase tracking-[0.25em] text-slate-500">Ipoteka</p>
                            <p class="text-lg font-semibold text-white">12 oy</p>
                        </div>
                    </div>
                </div>
            </div>

            <nav class="space-y-2 mb-8">
                <div class="text-[10px] uppercase tracking-[0.35em] text-slate-500 mb-3">Bo‘limlar</div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-3xl px-3 py-3 text-sm text-slate-200 hover:bg-slate-800 hover:text-white transition {{ request()->is('dashboard') ? 'bg-slate-800 text-white shadow-2xl border-l-4 border-cyan-400' : '' }}">
                    <i class="fas fa-chart-line w-4"></i>
                    Dashboard
                </a>
                <a href="{{ route('investors.index') }}" class="flex items-center gap-3 rounded-3xl px-3 py-3 text-sm text-slate-200 hover:bg-slate-800 hover:text-white transition {{ request()->is('investors*') ? 'bg-slate-800 text-white shadow-2xl border-l-4 border-cyan-400' : '' }}">
                    <i class="fas fa-user-tie w-4"></i>
                    Investorlar
                </a>
                <a href="{{ route('properties.index') }}" class="flex items-center gap-3 rounded-3xl px-3 py-3 text-sm text-slate-200 hover:bg-slate-800 hover:text-white transition {{ request()->is('properties*') ? 'bg-slate-800 text-white shadow-2xl border-l-4 border-cyan-400' : '' }}">
                    <i class="fas fa-building w-4"></i>
                    Uylar
                </a>
                <a href="{{ route('clients.index') }}" class="flex items-center gap-3 rounded-3xl px-3 py-3 text-sm text-slate-200 hover:bg-slate-800 hover:text-white transition {{ request()->is('clients*') ? 'bg-slate-800 text-white shadow-2xl border-l-4 border-cyan-400' : '' }}">
                    <i class="fas fa-users w-4"></i>
                    Mijozlar
                </a>
                <a href="{{ route('sales.index') }}" class="flex items-center gap-3 rounded-3xl px-3 py-3 text-sm text-slate-200 hover:bg-slate-800 hover:text-white transition {{ request()->is('sales*') ? 'bg-slate-800 text-white shadow-2xl border-l-4 border-cyan-400' : '' }}">
                    <i class="fas fa-handshake w-4"></i>
                    Savdolar
                </a>
            </nav>

            <div class="mt-auto rounded-3xl bg-slate-900/90 p-5 border border-white/10 shadow-2xl text-slate-300">
                <p class="text-xs uppercase tracking-[0.25em] mb-2 text-slate-500">Ish jarayoni</p>
                <p class="text-sm leading-6">CRM interfeysi hozirgi savdo va mijoz monitoringini chiroyli tarzda taqdim etadi. Har bir bo‘limni tezda kuzating.</p>
            </div>
        </div>
    </aside>

    <main class="md:ml-72 flex-1 p-4 md:p-8">
        <div class="sticky top-0 z-20 mb-6 rounded-[2rem] bg-white/95 backdrop-blur border border-slate-200 shadow-sm px-5 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm text-slate-500">Xush kelibsiz, @auth{{ auth()->user()->name }}@endauth</p>
                    <h2 class="text-3xl font-bold text-slate-900">@yield('title', 'Dashboard')</h2>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="search" placeholder="Qidiruv..." class="w-full rounded-3xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm text-slate-700 outline-none transition focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100" />
                    </div>
                    <button class="inline-flex items-center gap-2 rounded-3xl bg-slate-950 px-5 py-3 text-white shadow-lg hover:bg-slate-800 transition">
                        <i class="fas fa-bell"></i>
                        Bildirishnomalar
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-3xl border border-emerald-200 bg-white px-6 py-4 text-slate-800 shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    </script>
</body>
</html>
