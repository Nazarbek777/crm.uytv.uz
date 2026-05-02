@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4 mb-8">
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
        <p class="text-sm text-slate-500">Faol investorlar</p>
        <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $stats['active_investors'] }}</p>
        <p class="mt-3 text-sm text-slate-500">Propertylari bo‘lgan investorlar soni.</p>
    </div>
</div>

<div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200 mb-8">
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
                <h3 class="text-lg font-semibold text-slate-900">So‘nggi savdolar</h3>
                <p class="text-sm text-slate-500">Eng so‘nggi 5 ta savdo yozuvi.</p>
            </div>
            <span class="rounded-2xl bg-slate-100 px-3 py-2 text-sm text-slate-600">Yangi</span>
        </div>
        @if($stats['recent_sales']->isEmpty())
            <p class="text-slate-500">Hali savdo yo‘q.</p>
        @else
            <div class="space-y-4">
                @foreach($stats['recent_sales'] as $sale)
                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-slate-900 font-semibold">{{ $sale->property->title ?? 'Nomaʼlum uy' }}</p>
                                <p class="text-sm text-slate-500">{{ $sale->client->name ?? 'Nomaʼlum mijoz' }}</p>
                            </div>
                            <p class="text-slate-900 font-semibold">{{ number_format($sale->price, 0, ',', ' ') }} UZS</p>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            <span><i class="fas fa-calendar-alt mr-2"></i>{{ $sale->sale_date?->format('d.m.Y') ?? '---' }}</span>
                            <span><i class="fas fa-tag mr-2"></i>{{ ucfirst($sale->type) ?? 'Savdo' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-slate-900">Faol uylar holati</h3>
            <p class="text-sm text-slate-500">Uylar bo‘yicha tezkor holat.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200 text-center">
                <p class="text-sm text-slate-500">Bo‘sh</p>
                <p class="mt-3 text-2xl font-semibold text-emerald-700">{{ $stats['free'] }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200 text-center">
                <p class="text-sm text-slate-500">Sotilgan</p>
                <p class="mt-3 text-2xl font-semibold text-blue-700">{{ $stats['sold'] }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200 text-center">
                <p class="text-sm text-slate-500">Ijara</p>
                <p class="mt-3 text-2xl font-semibold text-orange-700">{{ $stats['rent'] }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
