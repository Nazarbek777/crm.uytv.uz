<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Property;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Reminder;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isManager = $user->isManager();

        $salesQuery = Sale::query();
        $leadsQuery = Lead::query();
        if (!$isManager) {
            $salesQuery->where('operator_id', $user->id);
            $leadsQuery->where('operator_id', $user->id);
        }

        $totalSales = (clone $salesQuery)->count();
        $monthlyTrend = $this->monthlyTrend(6, $isManager ? null : $user->id);
        $thisMonthIncome = (clone $salesQuery)->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->sum('price');

        $stats = [
            'is_manager' => $isManager,
            'investors' => Investor::count(),
            'properties' => Property::count(),
            'free' => Property::where('status', 'free')->count(),
            'sold' => Property::where('status', 'sold')->count(),
            'rent' => Property::where('status', 'rent')->count(),
            'clients' => Client::count(),
            'sales' => $totalSales,
            'total_income' => (clone $salesQuery)->sum('price'),
            'monthly_income' => $thisMonthIncome,
            'average_sale' => $totalSales ? round((clone $salesQuery)->avg('price')) : 0,
            'active_investors' => Investor::whereHas('properties')->count(),
            'monthly_sales' => (clone $salesQuery)->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->count(),
            'quarterly_growth' => $this->calculateQuarterlyGrowth($isManager ? null : $user->id),
            'recent_sales' => (clone $salesQuery)->with(['client', 'property', 'operator'])->latest('sale_date')->limit(6)->get(),
            'top_investors' => $isManager ? Investor::withCount('properties')->orderByDesc('properties_count')->limit(5)->get() : collect(),
            'chart_months' => $monthlyTrend['labels'],
            'chart_sales' => $monthlyTrend['sales_count'],
            'chart_income' => $monthlyTrend['income'],
            'leads_total' => (clone $leadsQuery)->count(),
            'leads_new' => (clone $leadsQuery)->where('status', 'new')->count(),
            'leads_active' => (clone $leadsQuery)->whereIn('status', ['contacted', 'qualified', 'negotiating'])->count(),
            'leads_by_status' => (clone $leadsQuery)->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status'),
            'today_reminders' => Reminder::with('lead')
                ->where('user_id', $user->id)
                ->where('completed', false)
                ->where('remind_at', '<=', now()->endOfDay())
                ->orderBy('remind_at')
                ->limit(5)->get(),
            'today_tasks' => Task::with(['lead', 'assigner'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->where(function ($q) {
                    $q->whereNull('due_date')->orWhereDate('due_date', '<=', today());
                })
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
                ->orderBy('due_date')
                ->limit(5)->get(),
            'today_calls' => Lead::where('operator_id', $user->id)
                ->whereDate('next_follow_up', today())
                ->whereNotIn('status', ['won', 'lost'])
                ->count(),
        ];

        return view('dashboard', compact('stats'));
    }

    private function monthlyTrend(int $monthsBack, ?int $operatorId = null)
    {
        $labels = [];
        $salesCount = [];
        $income = [];
        $monthNames = ['Yan', 'Fev', 'Mar', 'Apr', 'May', 'Iyn', 'Iyl', 'Avg', 'Sen', 'Okt', 'Noy', 'Dek'];
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $monthNames[$date->month - 1];
            $rows = Sale::whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
            if ($operatorId) {
                $rows->where('operator_id', $operatorId);
            }
            $salesCount[] = (clone $rows)->count();
            $income[] = (float) (clone $rows)->sum('price');
        }
        return ['labels' => $labels, 'sales_count' => $salesCount, 'income' => $income];
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
            'currency' => 'required|in:UZS,USD',
        ]);

        $exchangeRate = 12600; // 1 USD = 12600 UZS
        $currency = $request->currency;

        // Convert to UZS for calculation
        $propertyPriceUZS = $currency === 'USD' ? $request->property_price * $exchangeRate : $request->property_price;

        $downPaymentPercent = $request->down_payment_percent;
        $downPaymentUZS = $propertyPriceUZS * ($downPaymentPercent / 100);
        $loanAmountUZS = $propertyPriceUZS - $downPaymentUZS;

        $maxLoanLimit = 380000000; // 380 million UZS max bank loan
        if ($loanAmountUZS > $maxLoanLimit) {
            return back()->withErrors(['loan_amount' => 'Bank maksimal kredit limiti 380 million UZS. Kredit summasini kamaytiring.']);
        }

        if ($loanAmountUZS <= 0) {
            return back()->withErrors(['loan_amount' => 'Kredit summasi musbat bo‘lishi kerak.']);
        }

        $annualRate = $request->interest_rate / 100;
        $monthlyRate = $annualRate / 12;
        $termMonths = $request->term_years * 12;

        $monthlyPaymentUZS = $loanAmountUZS * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        $totalPaymentUZS = $monthlyPaymentUZS * $termMonths;
        $totalInterestUZS = $totalPaymentUZS - $loanAmountUZS;

        // Convert back to display currency
        $conversionRate = $currency === 'USD' ? $exchangeRate : 1;

        return back()->with([
            'mortgage_result' => [
                'property_price' => round($propertyPriceUZS / $conversionRate),
                'down_payment' => round($downPaymentUZS / $conversionRate),
                'loan_amount' => round($loanAmountUZS / $conversionRate),
                'monthly_payment' => round($monthlyPaymentUZS / $conversionRate),
                'total_payment' => round($totalPaymentUZS / $conversionRate),
                'total_interest' => round($totalInterestUZS / $conversionRate),
                'interest_rate' => $request->interest_rate,
                'term_years' => $request->term_years,
                'down_payment_percent' => $downPaymentPercent,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
            ]
        ]);
    }

    private function calculateQuarterlyGrowth(?int $operatorId = null)
    {
        $currentQ = Sale::whereBetween('sale_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
        $previousQ = Sale::whereBetween('sale_date', [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()]);

        if ($operatorId) {
            $currentQ->where('operator_id', $operatorId);
            $previousQ->where('operator_id', $operatorId);
        }

        $current = (float) $currentQ->sum('price');
        $previous = (float) $previousQ->sum('price');

        if ($previous == 0) return 0;
        return round((($current - $previous) / $previous) * 100);
    }
}
