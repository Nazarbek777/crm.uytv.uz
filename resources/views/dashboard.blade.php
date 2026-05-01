@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
    <p class="text-gray-600">Umumiy statistika</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Investorlar</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['investors'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-tie text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Jami Uylar</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['properties'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-green-500 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Mijozlar</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['clients'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Savdolar</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['sales'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-handshake text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Property Status -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Bo'sh uylar</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['free'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-blue-500 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Sotilgan</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['sold'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-key text-orange-500 text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Ijara</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['rent'] }}</p>
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