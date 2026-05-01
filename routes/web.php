<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SaleController;

Route::get('/', function () {
    return redirect('/dashboard');
})->name('home');

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
    ];
    return view('dashboard', compact('stats'));
})->name('dashboard');

Route::resource('investors', InvestorController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('properties', PropertyController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('sales', SaleController::class)->only(['index', 'store', 'destroy']);
