@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="grid gap-6 xl:grid-cols-4 mb-8">
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-slate-500">Investorlar</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $stats['investors'] }}</p>
            </div>
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-cyan-100 text-cyan-700">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
        <p class="text-sm text-slate-500">Ularning aksariyati faol va loyihalarni qo‘llab-quvvatlaydi.</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-slate-500">Jami uylar</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $stats['properties'] }}</p>
            </div>
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-emerald-100 text-emerald-700">
                <i class="fas fa-building"></i>
            </div>
        </div>
        <p class="text-sm text-slate-500">Bo‘sh, sotilgan va ijaraga olingan uylarni boshqarish.</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-slate-500">Mijozlar</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $stats['clients'] }}</p>
            </div>
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-violet-100 text-violet-700">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <p class="text-sm text-slate-500">Sizning aktiv mijozlar bazangiz.</p>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200 hover:shadow-xl transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-slate-500">Savdolar</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $stats['sales'] }}</p>
            </div>
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-orange-100 text-orange-700">
                <i class="fas fa-handshake"></i>
            </div>
        </div>
        <p class="text-sm text-slate-500">Joriy savdo holati va daromadlar.</p>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3 mb-8">
    <div class="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-2xl border border-slate-700">
        <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Umumiy daromad</p>
        <p class="mt-4 text-4xl font-semibold">{{ number_format($stats['total_income'], 0, ',', ' ') }} UZS</p>
        <p class="mt-3 text-slate-300">So‘nggi savdolarning jami qiymati.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">O‘rtacha savdo qiymati</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ number_format($stats['average_sale'], 0, ',', ' ') }} UZS</p>
        <p class="mt-3 text-sm text-slate-500">Yangi savdolarni baholash uchun tezkor ko‘rsatkich.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">Oylik savdolar</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $stats['monthly_sales'] }}</p>
        <p class="mt-3 text-sm text-slate-500">Joriy oyda amalga oshirilgan savdolar.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">Faol investorlar</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $stats['active_investors'] }}</p>
        <p class="mt-3 text-sm text-slate-500">Propertylari bo‘lgan investorlar soni.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">Choraklik o‘sish</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $stats['quarterly_growth'] }}%</p>
        <p class="mt-3 text-sm text-slate-500">Oldingi chorakka nisbatan o‘sish.</p>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3 mb-8">
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">Minimal narx</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ number_format($stats['min_price'], 0, ',', ' ') }} UZS</p>
        <p class="mt-3 text-sm text-slate-500">Bu narx bo‘yicha boshlash mumkin.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">O‘rtacha uy narxi</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ number_format($stats['avg_price'], 0, ',', ' ') }} UZS</p>
        <p class="mt-3 text-sm text-slate-500">Bozor uchun real boshlang‘ich qiymat.</p>
    </div>
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <p class="text-sm text-slate-500">Maximum narx</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ number_format($stats['max_price'], 0, ',', ' ') }} UZS</p>
        <p class="mt-3 text-sm text-slate-500">Premium segmentdagi uylar.</p>
    </div>
</div>

<div class="rounded-3xl bg-gradient-to-br from-slate-950 to-slate-900 p-6 text-white shadow-2xl border border-slate-800 mb-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h3 class="text-xl font-semibold">Ipoteka va boshlang‘ich to‘lov</h3>
            <p class="text-sm text-slate-400">Real vaqt narxlari va to‘lov qulayliklarini tezda ko‘ring.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-3xl bg-slate-900/60 px-4 py-3 text-sm text-slate-300">
            <i class="fas fa-percent text-emerald-400"></i>
            <span>{{ $stats['mortgage_rate'] }}% yillik ipoteka</span>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-800">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Boshlang‘ich to‘lov</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($stats['down_payment'], 0, ',', ' ') }} UZS</p>
            <p class="mt-2 text-sm text-slate-400">O‘rtacha uy uchun 30% boshlang‘ich.</p>
        </div>
        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-800">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Ipoteka summasi</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ number_format(round($stats['avg_price'] * 0.7), 0, ',', ' ') }} UZS</p>
            <p class="mt-2 text-sm text-slate-400">70% kredit orqali moliyalashtirish.</p>
        </div>
        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-800">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Oylik to‘lov</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($stats['monthly_mortgage'], 0, ',', ' ') }} UZS</p>
            <p class="mt-2 text-sm text-slate-400">20 yil uchun tahminiy oylik.</p>
        </div>
        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-800">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Boshlang‘ich segment</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $stats['min_price'] ? 'Boshlang‘ich' : '---' }}</p>
            <p class="mt-2 text-sm text-slate-400">Tezda narx bandini aniqlang.</p>
        </div>
    </div>
</div>

<div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200 mb-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-4">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Tezkor amallar</h3>
            <p class="text-sm text-slate-500">Alohida bo‘limlarga tez kirish.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('mortgage.calculator') }}" class="inline-flex items-center gap-2 rounded-2xl bg-purple-600 px-4 py-3 text-white shadow-lg hover:bg-purple-700 transition">
                <i class="fas fa-calculator"></i> Ipoteka Kalkulyatori
            </a>
            <a href="{{ route('investors.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-cyan-600 px-4 py-3 text-white shadow-lg hover:bg-cyan-700 transition">
                <i class="fas fa-user-tie"></i> Yangi Investor
            </a>
            <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-white shadow-lg hover:bg-emerald-700 transition">
                <i class="fas fa-building"></i> Yangi Uy
            </a>
            <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-4 py-3 text-white shadow-lg hover:bg-violet-700 transition">
                <i class="fas fa-users"></i> Yangi Mijoz
            </a>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-orange-500 px-4 py-3 text-white shadow-lg hover:bg-orange-600 transition">
                <i class="fas fa-handshake"></i> Yangi Savdo
            </a>
        </div>
    </div>
</div>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-4">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Tezkor amallar</h3>
            <p class="text-sm text-slate-500">Alohida bo‘limlarga tez kirish.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('investors.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-cyan-600 px-4 py-3 text-white shadow-lg hover:bg-cyan-700 transition">
                <i class="fas fa-user-tie"></i> Yangi Investor
            </a>
            <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-white shadow-lg hover:bg-emerald-700 transition">
                <i class="fas fa-building"></i> Yangi Uy
            </a>
            <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-4 py-3 text-white shadow-lg hover:bg-violet-700 transition">
                <i class="fas fa-users"></i> Yangi Mijoz
            </a>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-orange-500 px-4 py-3 text-white shadow-lg hover:bg-orange-600 transition">
                <i class="fas fa-handshake"></i> Yangi Savdo
            </a>
        </div>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">Daromad va natijalar</h3>
                <p class="text-sm text-slate-500">So‘nggi savdo faoliyati.</p>
            </div>
            <span class="rounded-3xl bg-slate-100 px-3 py-2 text-sm text-slate-600">Yangi</span>
        </div>
        <div class="grid gap-4 sm:grid-cols-3 mb-6">
            <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Bo‘sh uylar</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-700">{{ $stats['free'] }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Sotilgan uylar</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['sold'] }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Ijara uylar</p>
                <p class="mt-3 text-3xl font-semibold text-orange-700">{{ $stats['rent'] }}</p>
            </div>
        </div>
        <div class="space-y-4">
            @php $totalProps = max($stats['properties'] ?? 1, 1); @endphp
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-slate-500">Bo‘sh hovuz</p>
                    <p class="text-sm font-semibold text-slate-900">{{ round($stats['free'] / $totalProps * 100) }}%</p>
                </div>
                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ round($stats['free'] / $totalProps * 100) }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-slate-500">Sotilgan</p>
                    <p class="text-sm font-semibold text-slate-900">{{ round($stats['sold'] / $totalProps * 100) }}%</p>
                </div>
                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-blue-500" style="width: {{ round($stats['sold'] / $totalProps * 100) }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-slate-500">Ijara</p>
                    <p class="text-sm font-semibold text-slate-900">{{ round($stats['rent'] / $totalProps * 100) }}%</p>
                </div>
                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-orange-500" style="width: {{ round($stats['rent'] / $totalProps * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">So‘nggi savdolar</h3>
                <p class="text-sm text-slate-500">Eng so‘nggi 5 ta savdo yozuvi.</p>
            </div>
            <span class="rounded-3xl bg-slate-100 px-3 py-2 text-sm text-slate-600">Tahlil</span>
        </div>
        @if($stats['recent_sales']->isEmpty())
            <p class="text-slate-500">Hali savdo yo‘q.</p>
        @else
            <div class="space-y-4">
                @foreach($stats['recent_sales'] as $sale)
                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-base font-semibold text-slate-900">{{ $sale->property->title ?? 'Nomaʼlum uy' }}</p>
                                <p class="text-sm text-slate-500 mt-1">{{ $sale->client->name ?? 'Nomaʼlum mijoz' }}</p>
                            </div>
                            <span class="rounded-2xl bg-slate-100 px-3 py-1 text-sm text-slate-600">{{ ucfirst($sale->type) }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            <span><i class="fas fa-calendar-alt mr-2"></i>{{ $sale->sale_date?->format('d.m.Y') ?? '---' }}</span>
                            <span><i class="fas fa-money-bill-wave mr-2"></i>{{ number_format($sale->price, 0, ',', ' ') }} UZS</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-2xl border border-slate-800">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h3 class="text-xl font-semibold">So‘nggi oylik trend</h3>
            <p class="mt-2 text-sm text-slate-300">Oylik ishlab chiqarish va savdo ko‘rsatkichlarini kuzatish.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-3xl bg-slate-900/60 px-4 py-3 text-sm text-slate-300">
            <i class="fas fa-arrow-up text-emerald-400"></i>
            <span>Kutilgan daromad +24%</span>
        </div>
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="space-y-3 rounded-3xl bg-slate-950/80 p-4">
            <p class="text-sm text-slate-400">Investorlar o‘sishi</p>
            <p class="text-3xl font-semibold text-white">{{ $stats['investors'] }}</p>
            <div class="h-2 rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-cyan-500" style="width: 72%"></div>
            </div>
        </div>
        <div class="space-y-3 rounded-3xl bg-slate-950/80 p-4">
            <p class="text-sm text-slate-400">Uy portfeli</p>
            <p class="text-3xl font-semibold text-white">{{ $stats['properties'] }}</p>
            <div class="h-2 rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-emerald-500" style="width: 62%"></div>
            </div>
        </div>
        <div class="space-y-3 rounded-3xl bg-slate-950/80 p-4">
            <p class="text-sm text-slate-400">Savdolar soni</p>
            <p class="text-3xl font-semibold text-white">{{ $stats['sales'] }}</p>
            <div class="h-2 rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-orange-500" style="width: 83%"></div>
            </div>
        </div>
    </div>
</div>
@endsection
