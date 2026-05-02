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
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-blue-600 hover:to-purple-600 hover:text-white rounded-lg transition-all duration-300 transform hover:scale-105; }
        .sidebar-link.active { @apply bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg; }
        .sidebar { @apply fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 to-gray-800 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out; }
        .sidebar.open { @apply translate-x-0; }
        .overlay { @apply fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden; }
        .overlay.show { @apply block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .animate-modal-in { animation: modalIn 0.3s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Mobile Menu Button -->
    <button id="menuToggle" class="md:hidden fixed top-4 left-4 z-50 bg-gradient-to-r from-blue-600 to-purple-600 text-white p-2 rounded-lg shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay -->
    <div id="overlay" class="overlay"></div>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">CRM Uy</h1>
                <p class="text-gray-400 text-sm mt-1">Uy sotish boshqaruvi</p>
            </div>
            <nav class="px-3 py-4">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
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
        <main class="flex-1 md:ml-64 p-4 md:p-8 min-h-screen">
            @if(session('success'))
                <div class="bg-gradient-to-r from-green-400 to-green-500 border border-green-300 text-white px-6 py-4 rounded-xl shadow-lg mb-6 animate-pulse">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>

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