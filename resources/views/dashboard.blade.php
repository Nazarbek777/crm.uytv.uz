@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
    <p class="text-gray-600">Umumiy statistika</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border-l-4 border-blue-500 hover:border-blue-600 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Investorlar</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['investors'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center shadow-inner">
                <i class="fas fa-user-tie text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border-l-4 border-green-500 hover:border-green-600 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Jami Uylar</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['properties'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center shadow-inner">
                <i class="fas fa-building text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border-l-4 border-purple-500 hover:border-purple-600 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Mijozlar</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['clients'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center shadow-inner">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border-l-4 border-yellow-500 hover:border-yellow-600 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Savdolar</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['sales'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center shadow-inner">
                <i class="fas fa-handshake text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Property Status -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center shadow-inner">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Bo'sh uylar</p>
                <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['free'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center shadow-inner">
                <i class="fas fa-money-bill-wave text-blue-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Sotilgan</p>
                <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['sold'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 transform hover:-translate-y-1 transition-transform">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center shadow-inner">
                <i class="fas fa-key text-orange-600 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Ijara</p>
                <p class="text-3xl font-bold text-orange-700 mt-1">{{ $stats['rent'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tezkor amallar</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('investors.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Yangi Investor
        </a>
        <a href="{{ route('properties.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-plus mr-2"></i>Yangi Uy
        </a>
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i>Yangi Mijoz
        </a>
        <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
            <i class="fas fa-plus mr-2"></i>Yangi Savdo
        </a>
    </div>
</div>
@endsection