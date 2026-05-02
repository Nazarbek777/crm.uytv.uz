<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SaleController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'investors' => \App\Models\Investor::count(),
            'properties' => \App\Models\Property::count(),
            'free' => \App\Models\Property::where('status', 'free')->count(),
            'sold' => \App\Models\Property::where('status', 'sold')->count(),
            'rent' => \App\Models\Property::where('status', 'rent')->count(),
            'clients' => \App\Models\Client::count(),
            'sales' => \App\Models\Sale::count(),
            'total_income' => \App\Models\Sale::sum('price'),
            'average_sale' => \App\Models\Sale::count() ? round(\App\Models\Sale::avg('price')) : 0,
            'active_investors' => \App\Models\Investor::whereHas('properties')->count(),
            'recent_sales' => \App\Models\Sale::with(['client', 'property'])->latest()->limit(5)->get(),
        ];

        return view('dashboard', compact('stats'));
    })->name('dashboard');

    Route::resource('investors', InvestorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('properties', PropertyController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('sales', SaleController::class)->only(['index', 'store', 'destroy']);
});
