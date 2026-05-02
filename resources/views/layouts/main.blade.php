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
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border-radius: 0.75rem; font-size: 0.875rem; color: rgb(203 213 225); transition: all 150ms; }
        .nav-link:hover { background: rgb(30 41 59); color: white; }
        .nav-link.active { background: rgb(30 41 59); color: white; box-shadow: inset 3px 0 0 rgb(34 211 238); }
        .nav-link i { width: 1.125rem; text-align: center; font-size: 0.875rem; opacity: 0.85; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
    <button id="menuToggle" class="md:hidden fixed top-4 left-4 z-50 bg-slate-950 text-white p-2.5 rounded-xl shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-60 bg-slate-950 border-r border-slate-800 transform -translate-x-full md:translate-x-0 transition-transform duration-300 overflow-y-auto">
        <div class="flex min-h-full flex-col px-3 py-5">
            <div class="px-2 mb-6 flex items-center gap-3">
                <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 text-white shadow-lg">
                    <i class="fas fa-building text-sm"></i>
                </div>
                <div>
                    <h1 class="text-sm font-semibold text-white leading-tight">CRM Uy</h1>
                    <p class="text-slate-500 text-[11px]">Uy sotish boshqaruvi</p>
                </div>
            </div>

            <nav class="space-y-1 flex-1">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('properties.index') }}" class="nav-link {{ request()->is('properties*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Uylar
                </a>
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->is('clients*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Mijozlar
                </a>
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->is('sales*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i> Savdolar
                </a>
                <a href="{{ route('investors.index') }}" class="nav-link {{ request()->is('investors*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> Investorlar
                </a>
                <a href="{{ route('mortgage.calculator') }}" class="nav-link {{ request()->is('mortgage-calculator') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i> Kalkulyator
                </a>
            </nav>

            @auth
            <div class="mt-4 pt-4 border-t border-slate-800">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-slate-300 text-sm font-semibold">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Chiqish" class="text-slate-400 hover:text-red-400 transition p-2">
                            <i class="fas fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </aside>

    <main class="md:ml-60 p-4 md:p-6">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div class="md:pl-0 pl-12">
                <h2 class="text-2xl font-bold text-slate-900">@yield('title', 'Dashboard')</h2>
                @auth<p class="text-xs text-slate-500 mt-0.5">Salom, {{ auth()->user()->name }}</p>@endauth
            </div>
            <div class="flex items-center gap-2">
                @yield('actions')
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
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
