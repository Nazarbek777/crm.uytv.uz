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
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition; }
        .sidebar-link.active { @apply bg-blue-600 text-white; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 min-h-screen fixed">
            <div class="p-6">
                <h1 class="text-xl font-bold text-white">CRM Uy</h1>
                <p class="text-gray-400 text-sm">Uy sotish boshqaruvi</p>
            </div>
            <nav class="px-3">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home w-5"></i> Dashboard
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
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
</body>
</html>