@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-5">
    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500">Joriy oy daromadi</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($stats['monthly_income'], 0, ',', ' ') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">UZS · {{ $stats['monthly_sales'] }} ta savdo</p>
            </div>
            <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <i class="fas fa-chart-line text-sm"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500">Jami daromad</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($stats['total_income'], 0, ',', ' ') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">UZS · {{ $stats['sales'] }} ta savdo</p>
            </div>
            <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">
                <i class="fas fa-coins text-sm"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500">Choraklik o'sish</p>
                <p class="mt-1 text-2xl font-bold {{ $stats['quarterly_growth'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $stats['quarterly_growth'] >= 0 ? '+' : '' }}{{ $stats['quarterly_growth'] }}%
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Oldingi chorakka nisbatan</p>
            </div>
            <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <i class="fas fa-arrow-trend-up text-sm"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500">O'rtacha savdo</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($stats['average_sale'], 0, ',', ' ') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">UZS</p>
            </div>
            <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <i class="fas fa-receipt text-sm"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-5">
    <a href="{{ route('properties.index') }}" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm hover:shadow transition flex items-center gap-3">
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
            <i class="fas fa-building"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Uylar</p>
            <p class="text-lg font-bold text-slate-900">{{ $stats['properties'] }}</p>
        </div>
    </a>
    <a href="{{ route('clients.index') }}" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm hover:shadow transition flex items-center gap-3">
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Mijozlar</p>
            <p class="text-lg font-bold text-slate-900">{{ $stats['clients'] }}</p>
        </div>
    </a>
    <a href="{{ route('investors.index') }}" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm hover:shadow transition flex items-center gap-3">
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
            <i class="fas fa-user-tie"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Investorlar</p>
            <p class="text-lg font-bold text-slate-900">{{ $stats['investors'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $stats['active_investors'] }} faol</span></p>
        </div>
    </a>
    <a href="{{ route('sales.index') }}" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm hover:shadow transition flex items-center gap-3">
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700">
            <i class="fas fa-handshake"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500">Savdolar</p>
            <p class="text-lg font-bold text-slate-900">{{ $stats['sales'] }}</p>
        </div>
    </a>
</div>

<div class="grid gap-4 lg:grid-cols-3 mb-5">
    <div class="lg:col-span-2 rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Savdo trendi</h3>
                <p class="text-xs text-slate-500">So'nggi 6 oy</p>
            </div>
        </div>
        <div style="position: relative; height: 260px;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-slate-900">Uylar holati</h3>
            <p class="text-xs text-slate-500">Jami: {{ $stats['properties'] }}</p>
        </div>
        <div class="flex items-center justify-center mb-4">
            <canvas id="statusChart" width="160" height="160"></canvas>
        </div>
        <div class="space-y-1.5 text-sm">
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-slate-600"><span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Bo'sh</span>
                <span class="font-semibold text-slate-900">{{ $stats['free'] }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-slate-600"><span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500"></span> Sotilgan</span>
                <span class="font-semibold text-slate-900">{{ $stats['sold'] }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-slate-600"><span class="inline-block w-2.5 h-2.5 rounded-full bg-orange-500"></span> Ijarada</span>
                <span class="font-semibold text-slate-900">{{ $stats['rent'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-3 mb-5">
    <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-900">So'nggi savdolar</h3>
            <a href="{{ route('sales.index') }}" class="text-xs text-cyan-600 hover:text-cyan-700">Barchasi <i class="fas fa-arrow-right text-[10px] ml-0.5"></i></a>
        </div>
        @if($stats['recent_sales']->isEmpty())
            <p class="text-sm text-slate-500 px-5 py-8 text-center">Hali savdo yo'q.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($stats['recent_sales'] as $sale)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ $sale->type === 'rent' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }}">
                            <i class="fas {{ $sale->type === 'rent' ? 'fa-key' : 'fa-handshake' }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $sale->property->title ?? 'Noma\'lum uy' }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $sale->client->name ?? 'Noma\'lum mijoz' }} · {{ $sale->sale_date?->format('d.m.Y') ?? '---' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($sale->price, 0, ',', ' ') }}</p>
                            <p class="text-[11px] text-slate-400">UZS</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-900">Top investorlar</h3>
            <a href="{{ route('investors.index') }}" class="text-xs text-cyan-600 hover:text-cyan-700">Barchasi</a>
        </div>
        @if($stats['top_investors']->isEmpty())
            <p class="text-sm text-slate-500 px-5 py-8 text-center">Investor yo'q.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($stats['top_investors'] as $i => $inv)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 text-xs font-bold">{{ $i + 1 }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $inv->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $inv->phone ?? '—' }}</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 bg-slate-100 rounded-full px-2.5 py-1">{{ $inv->properties_count }} uy</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
    <h3 class="text-base font-semibold text-slate-900 mb-3">Tezkor amallar</h3>
    <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-5">
        <a href="{{ route('properties.index') }}" class="flex items-center gap-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-3 text-sm font-medium transition">
            <i class="fas fa-plus text-xs"></i> Yangi uy
        </a>
        <a href="{{ route('clients.index') }}" class="flex items-center gap-2 rounded-xl bg-violet-50 hover:bg-violet-100 text-violet-700 px-4 py-3 text-sm font-medium transition">
            <i class="fas fa-plus text-xs"></i> Yangi mijoz
        </a>
        <a href="{{ route('sales.index') }}" class="flex items-center gap-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 px-4 py-3 text-sm font-medium transition">
            <i class="fas fa-plus text-xs"></i> Yangi savdo
        </a>
        <a href="{{ route('investors.index') }}" class="flex items-center gap-2 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-cyan-700 px-4 py-3 text-sm font-medium transition">
            <i class="fas fa-plus text-xs"></i> Yangi investor
        </a>
        <a href="{{ route('mortgage.calculator') }}" class="flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-3 text-sm font-medium transition">
            <i class="fas fa-calculator text-xs"></i> Kalkulyator
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const init = () => {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js yuklanmadi');
                return;
            }
            const months = @json($stats['chart_months']);
            const salesData = (@json($stats['chart_sales'])).map(Number);
            const incomeData = (@json($stats['chart_income'])).map(Number);

            const fmtUZS = v => {
                if (v >= 1e9) return (v / 1e9).toFixed(1).replace(/\.0$/, '') + ' mlrd';
                if (v >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + ' mln';
                if (v >= 1e3) return (v / 1e3).toFixed(0) + 'k';
                return v.toLocaleString('ru-RU').replace(/,/g, ' ');
            };

            const salesCanvas = document.getElementById('salesChart');
            const hasSalesData = incomeData.some(v => v > 0);
            if (salesCanvas && !hasSalesData) {
                salesCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-slate-400"><div class="text-center"><i class="fas fa-chart-column text-3xl text-slate-300 mb-2 block"></i>Hozircha savdo ma\'lumotlari yo\'q</div></div>';
            } else if (salesCanvas) {
                new Chart(salesCanvas, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Daromad',
                            data: incomeData,
                            backgroundColor: 'rgba(8, 145, 178, 0.85)',
                            hoverBackgroundColor: 'rgb(8, 145, 178)',
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 48,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (c) => ' ' + Math.round(c.parsed.y).toLocaleString('ru-RU').replace(/,/g, ' ') + ' UZS',
                                    afterLabel: (c) => ' Savdolar: ' + salesData[c.dataIndex],
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 10 }, callback: v => fmtUZS(v) },
                                grid: { color: 'rgba(148, 163, 184, 0.15)' }
                            },
                        }
                    }
                });
            }

            const statusCanvas = document.getElementById('statusChart');
            const statusData = [{{ $stats['free'] }}, {{ $stats['sold'] }}, {{ $stats['rent'] }}];
            const hasStatusData = statusData.some(v => v > 0);
            if (statusCanvas && !hasStatusData) {
                statusCanvas.parentElement.innerHTML = '<div class="text-sm text-slate-400 text-center py-8"><i class="fas fa-house text-3xl text-slate-300 mb-2 block"></i>Uy yo\'q</div>';
            } else if (statusCanvas) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Bo\'sh', 'Sotilgan', 'Ijarada'],
                        datasets: [{
                            data: statusData,
                            backgroundColor: ['rgb(16, 185, 129)', 'rgb(59, 130, 246)', 'rgb(249, 115, 22)'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: false,
                        plugins: { legend: { display: false } },
                        cutout: '70%',
                    }
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endsection
