<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');    Route::get('/mortgage-calculator', [DashboardController::class, 'mortgageCalculator'])->name('mortgage.calculator');    Route::post('/calculate-mortgage', [DashboardController::class, 'calculateMortgage'])->name('calculate.mortgage');

    Route::resource('investors', InvestorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('properties', PropertyController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('clients', ClientController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('sales', SaleController::class)->only(['index', 'store', 'destroy']);

    Route::resource('operators', OperatorController::class)->parameters(['operators' => 'operator']);

    Route::resource('leads', LeadController::class);
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    Route::resource('reminders', ReminderController::class)->except(['show']);
    Route::patch('/reminders/{reminder}/complete', [ReminderController::class, 'complete'])->name('reminders.complete');
    Route::patch('/reminders/{reminder}/uncomplete', [ReminderController::class, 'uncomplete'])->name('reminders.uncomplete');

    Route::resource('tasks', TaskController::class)->except(['show', 'create', 'edit']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'setStatus'])->name('tasks.status');
    Route::patch('/tasks/{task}/priority', [TaskController::class, 'setPriority'])->name('tasks.priority');
});
