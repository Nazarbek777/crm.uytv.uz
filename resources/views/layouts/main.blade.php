<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM Uy')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar.open { transform: translateX(0) !important; }
        .overlay.show { display: block !important; }
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.875rem; border-radius: 0.625rem; font-size: 0.875rem; color: rgb(203 213 225); transition: all 150ms; }
        .nav-link:hover { background: rgb(30 41 59); color: white; }
        .nav-link.active { background: rgb(30 41 59); color: white; box-shadow: inset 3px 0 0 rgb(34 211 238); }
        .nav-link i { width: 1.125rem; text-align: center; font-size: 0.875rem; opacity: 0.85; }
        .nav-section { font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; color: rgb(100 116 139); padding: 0.75rem 0.875rem 0.375rem; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
    @php
        $pendingCount = 0;
        $todayReminders = collect();
        if (auth()->check()) {
            $pendingCount = \App\Models\Reminder::where('user_id', auth()->id())
                ->where('completed', false)
                ->where('remind_at', '<=', now()->endOfDay())
                ->count();
            $todayReminders = \App\Models\Reminder::with('lead')
                ->where('user_id', auth()->id())
                ->where('completed', false)
                ->where('remind_at', '<=', now()->endOfDay())
                ->orderBy('remind_at')
                ->limit(5)
                ->get();
        }
    @endphp

    <button id="menuToggle" class="md:hidden fixed top-4 left-4 z-50 bg-slate-950 text-white p-2.5 rounded-xl shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-60 bg-slate-950 border-r border-slate-800 transform -translate-x-full md:translate-x-0 transition-transform duration-300 overflow-y-auto">
        <div class="flex min-h-full flex-col px-3 py-4">
            <div class="px-2 mb-4 flex items-center gap-3">
                <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 text-white shadow-lg">
                    <i class="fas fa-building text-sm"></i>
                </div>
                <div>
                    <h1 class="text-sm font-semibold text-white leading-tight">CRM Uy</h1>
                    <p class="text-slate-500 text-[11px]">Uy sotish boshqaruvi</p>
                </div>
            </div>

            <nav class="space-y-0.5 flex-1">
                <div class="nav-section">Asosiy</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('leads.index') }}" class="nav-link {{ request()->is('leads*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> Lidlar
                </a>
                <a href="{{ route('reminders.index') }}" class="nav-link {{ request()->is('reminders*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Eslatmalar
                    @if($pendingCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-red-500 text-white text-[10px] font-bold px-1.5">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                    @endif
                </a>

                <div class="nav-section">Boshqaruv</div>
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

                <div class="nav-section">Tizim</div>
                <a href="{{ route('operators.index') }}" class="nav-link {{ request()->is('operators*') ? 'active' : '' }}">
                    <i class="fas fa-id-badge"></i> Operatorlar
                </a>
                <a href="{{ route('mortgage.calculator') }}" class="nav-link {{ request()->is('mortgage-calculator') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i> Kalkulyator
                </a>
            </nav>

            @auth
            <div class="mt-3 pt-3 border-t border-slate-800">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-slate-300 text-sm font-semibold">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ ucfirst(auth()->user()->role ?? 'operator') }}</p>
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
                @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition">
                        <i class="fas fa-bell text-slate-600"></i>
                        @if($pendingCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-red-500 text-white text-[10px] font-bold px-1">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                        @endif
                    </button>
                    <div x-show="open" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-2xl border border-slate-200 shadow-xl z-50 overflow-hidden" style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-900">Eslatmalar</p>
                            <a href="{{ route('reminders.index') }}" class="text-xs text-cyan-600 hover:text-cyan-700">Hammasi</a>
                        </div>
                        @if($todayReminders->isEmpty())
                            <p class="px-4 py-8 text-center text-sm text-slate-500">Yangi eslatma yo'q</p>
                        @else
                            <div class="max-h-80 overflow-y-auto">
                                @foreach($todayReminders as $r)
                                    @php $isOverdue = $r->remind_at->isPast(); @endphp
                                    <a href="{{ route('reminders.edit', $r) }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50">
                                        <div class="flex items-start gap-2">
                                            <span class="mt-1 inline-block w-2 h-2 rounded-full {{ $isOverdue ? 'bg-red-500' : 'bg-cyan-500' }}"></span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-900 truncate">{{ $r->title }}</p>
                                                <p class="text-[11px] {{ $isOverdue ? 'text-red-500 font-semibold' : 'text-slate-500' }}">{{ $r->remind_at->diffForHumans() }} · {{ $r->remind_at->format('H:i') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endauth
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

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

        @auth
        // Browser notifikatsiya — bugun va kechikkan eslatmalar uchun
        if ('Notification' in window && {{ $pendingCount }} > 0) {
            const notify = () => {
                if (Notification.permission === 'granted') {
                    new Notification('CRM Uy — Eslatmalar', {
                        body: '{{ $pendingCount }} ta yangi eslatma kutmoqda',
                        icon: '/favicon.ico',
                    });
                } else if (Notification.permission !== 'denied') {
                    Notification.requestPermission();
                }
            };
            // Faqat bir marta sahifa ochilganda
            if (!sessionStorage.getItem('reminderShown_' + new Date().toDateString())) {
                setTimeout(notify, 1500);
                sessionStorage.setItem('reminderShown_' + new Date().toDateString(), '1');
            }
        }
        @endauth
    </script>
</body>
</html>
