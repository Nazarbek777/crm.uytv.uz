<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Property;
use App\Models\Sale;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $avgPrice = Property::count() ? Property::avg('price') : 0;
        $totalSales = Sale::count();
        $monthlyInterestRate = 0.12 / 12;
        $termMonths = 20 * 12;
        $loanAmount = $avgPrice * 0.7;

        $monthlyMortgage = $loanAmount && $termMonths
            ? round($loanAmount * ($monthlyInterestRate * pow(1 + $monthlyInterestRate, $termMonths)) / (pow(1 + $monthlyInterestRate, $termMonths) - 1))
            : 0;

        $stats = [
            'investors' => Investor::count(),
            'properties' => Property::count(),
            'free' => Property::where('status', 'free')->count(),
            'sold' => Property::where('status', 'sold')->count(),
            'rent' => Property::where('status', 'rent')->count(),
            'clients' => Client::count(),
            'sales' => $totalSales,
            'total_income' => Sale::sum('price'),
            'average_sale' => $totalSales ? round(Sale::avg('price')) : 0,
            'active_investors' => Investor::whereHas('properties')->count(),
            'min_price' => Property::count() ? Property::min('price') : 0,
            'max_price' => Property::count() ? Property::max('price') : 0,
            'avg_price' => round($avgPrice),
            'down_payment' => round($avgPrice * 0.3),
            'monthly_mortgage' => $monthlyMortgage,
            'mortgage_rate' => 12,
            'recent_sales' => Sale::with(['client', 'property'])->latest()->limit(5)->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
