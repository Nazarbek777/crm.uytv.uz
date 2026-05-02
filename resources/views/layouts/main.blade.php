<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM Uy')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-2xl transition-all duration-300; }
        .sidebar-link.active { @apply bg-slate-800 text-white shadow-2xl; }
        .sidebar { @apply fixed inset-y-0 left-0 z-40 w-72 bg-slate-950/95 backdrop-blur-xl border-r border-white/10 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out; }
        .sidebar.open { @apply translate-x-0; }
        .overlay { @apply fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden; }
        .overlay.show { @apply block; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <button id="menuToggle" class="md:hidden fixed top-4 left-4 z-50 bg-slate-950 text-white p-3 rounded-2xl shadow-2xl">
        <i class="fas fa-bars"></i>
    </button>

    <div id="overlay" class="overlay"></div>

    <aside id="sidebar" class="sidebar px-6 py-8">
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-3xl bg-gradient-to-br from-cyan-400 to-blue-600 text-white shadow-xl">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-white">CRM Uy</h1>
                    <p class="text-slate-400 text-sm">Uy sotish boshqaruvi</p>
                </div>
            </div>
            <div class="rounded-3xl bg-slate-900/80 p-4 border border-white/10 shadow-xl">
                <p class="text-slate-400 text-xs uppercase tracking-[0.2em] mb-3">Tez kirish</p>
                <div class="grid gap-3">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-simple w-5"></i> Dashboard
                    </a>
                    <a href="{{ route('investors.index') }}" class="sidebar-link {{ request()->is('investors*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie w-5"></i> Investorlar
                    </a>
                    <a href="{{ route('properties.index') }}" class="sidebar-link {{ request()->is('properties*') ? 'active' : '' }}">
                        <i class="fas fa-building w-5"></i> Uylar
                    </a>
                    <a href="{{ route('clients.index') }}" class="sidebar-link {{ request()->is('clients*') ? 'active' : '' }}">
                        <i class="fas fa-users w-5"></i> Mijozlar
                    </a>
                    <a href="{{ route('sales.index') }}" class="sidebar-link {{ request()->is('sales*') ? 'active' : '' }}">
                        <i class="fas fa-handshake w-5"></i> Savdolar
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-auto text-slate-400 text-sm">
            <p class="mb-2">CRM Uy v1.0</p>
            <p>Sodda, tezkor va ma'lumotlar boshqaruvi uchun mos.</p>
        </div>
    </aside>

    <main class="md:ml-72 flex-1 p-4 md:p-8">
        <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-slate-500">Xush kelibsiz, @auth{{ auth()->user()->name }}@endauth</p>
                <h2 class="text-3xl font-bold text-slate-900">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="{{ route('investors.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-slate-700 shadow-sm border border-slate-200 hover:bg-slate-50 transition">
                    <i class="fas fa-user-plus text-cyan-500"></i>
                    <span>Investor</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-white shadow-sm hover:bg-slate-800 transition">
                        <i class="fas fa-right-from-bracket"></i>
                        <span>Chiqish</span>
                    </button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-white border border-emerald-200 text-slate-800 px-6 py-4 rounded-3xl shadow-sm mb-6">
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
