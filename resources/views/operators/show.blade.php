@extends('layouts.main')

@section('title', $operator->name)

@section('actions')
    <a href="{{ route('operators.edit', $operator) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-pen text-xs"></i> Tahrirlash
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <div class="mb-1">
        <a href="{{ route('operators.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Operatorlar
        </a>
    </div>

    <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-700 text-2xl font-semibold">{{ mb_strtoupper(mb_substr($operator->name, 0, 1)) }}</div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-slate-900">{{ $operator->name }}</h2>
                <p class="text-sm text-slate-500">{{ $operator->email }}</p>
                <p class="text-sm text-slate-500">{{ $operator->phone ?: '—' }}</p>
                <div class="mt-2 flex gap-2">
                    <span class="inline-flex rounded-full border border-cyan-200 bg-cyan-50 text-cyan-700 px-2.5 py-0.5 text-xs font-medium">{{ ucfirst($operator->role) }}</span>
                    @if($operator->active)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-0.5 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Faol</span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-500 px-2.5 py-0.5 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Faolsiz</span>
                    @endif
                </div>
                @if($operator->notes)
                    <p class="mt-3 text-sm text-slate-600 bg-slate-50 rounded-xl p-3">{{ $operator->notes }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500">Lidlar</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $operator->leads_count }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500">Savdolar</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $operator->sales_count }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <p class="text-xs text-slate-500">Jami daromad</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($totalRevenue, 0, '.', ' ') }} <span class="text-sm font-normal text-slate-400">UZS</span></p>
        </div>
    </div>

    @if($leadsByStatus->isNotEmpty())
    <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 mb-3">Lidlar holati bo'yicha</h3>
        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach(\App\Models\Lead::STATUSES as $key => $info)
                <div class="rounded-xl bg-slate-50 p-3 text-center border border-slate-100">
                    <p class="text-[11px] text-slate-500">{{ $info['label'] }}</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">{{ $leadsByStatus[$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 pt-4 pb-3 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">So'nggi savdolar</h3>
            </div>
            @if($recentSales->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-slate-500">Savdo yo'q</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentSales as $sale)
                        <div class="flex items-center gap-3 px-5 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $sale->property->title ?? '—' }}</p>
                                <p class="text-xs text-slate-500">{{ $sale->client->name ?? '—' }} · {{ $sale->sale_date?->format('d.m.Y') }}</p>
                            </div>
                            <p class="text-sm font-semibold">{{ number_format($sale->price, 0, '.', ' ') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 pt-4 pb-3 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-900">So'nggi lidlar</h3>
            </div>
            @if($recentLeads->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-slate-500">Lid yo'q</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentLeads as $lead)
                        <a href="{{ route('leads.show', $lead) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 transition">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $lead->name }}</p>
                                <p class="text-xs text-slate-500">{{ $lead->phone }} · {{ $lead->statusLabel() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
