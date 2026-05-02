<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Property;
use App\Models\Sale;
use App\Models\Client;
use Illuminate\Http\Request;

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
            'monthly_sales' => Sale::whereMonth('created_at', now()->month)->count(),
            'quarterly_growth' => $this->calculateQuarterlyGrowth(),
        ];

        return view('dashboard', compact('stats'));
    }

    public function mortgageCalculator()
    {
        $avgPrice = Property::count() ? Property::avg('price') : 0;
        return view('mortgage-calculator', compact('avgPrice'));
    }

    public function calculateMortgage(Request $request)
    {
        $request->validate([
            'property_price' => 'required|numeric|min:0',
            'down_payment_percent' => 'required|numeric|min:0|max:100',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_years' => 'required|integer|min:1|max:50',
        ]);

        $propertyPrice = $request->property_price;
        $downPaymentPercent = $request->down_payment_percent;
        $downPayment = $propertyPrice * ($downPaymentPercent / 100);
        $loanAmount = $propertyPrice - $downPayment;

        if ($loanAmount <= 0) {
            return back()->withErrors(['loan_amount' => 'Kredit summasi musbat bo‘lishi kerak.']);
        }

        $annualRate = $request->interest_rate / 100;
        $monthlyRate = $annualRate / 12;
        $termMonths = $request->term_years * 12;

        $monthlyPayment = $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        $totalPayment = $monthlyPayment * $termMonths;
        $totalInterest = $totalPayment - $loanAmount;

        return back()->with([
            'mortgage_result' => [
                'property_price' => $propertyPrice,
                'down_payment' => round($downPayment),
                'loan_amount' => round($loanAmount),
                'monthly_payment' => round($monthlyPayment),
                'total_payment' => round($totalPayment),
                'total_interest' => round($totalInterest),
                'interest_rate' => $request->interest_rate,
                'term_years' => $request->term_years,
                'down_payment_percent' => $downPaymentPercent,
            ]
        ]);
    }

    private function calculateQuarterlyGrowth()
    {
        $currentQuarter = Sale::whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()])->sum('price');
        $previousQuarter = Sale::whereBetween('created_at', [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()])->sum('price');

        if ($previousQuarter == 0) return 0;

        return round((($currentQuarter - $previousQuarter) / $previousQuarter) * 100);
    }
}
