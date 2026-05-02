@extends('layouts.main')

@section('title', $lead->name)

@section('actions')
    <a href="{{ route('leads.edit', $lead) }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 px-3 py-2.5 text-sm font-medium transition">
        <i class="fas fa-pen text-xs"></i> Tahrirlash
    </a>
    @if($lead->status !== 'won')
        <button onclick="document.getElementById('convertModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 text-sm font-medium transition">
            <i class="fas fa-handshake text-xs"></i> Savdoga aylantirish
        </button>
    @endif
@endsection

@section('content')
@php
    $colorClasses = [
        'slate' => 'bg-slate-100 text-slate-700', 'blue' => 'bg-blue-100 text-blue-700',
        'cyan' => 'bg-cyan-100 text-cyan-700', 'amber' => 'bg-amber-100 text-amber-700',
        'emerald' => 'bg-emerald-100 text-emerald-700', 'red' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-5">
    <div class="mb-1">
        <a href="{{ route('leads.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Lidlar
        </a>
    </div>

    <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">{{ $lead->name }}</h2>
                <p class="text-sm text-slate-500">{{ $lead->phone }} · {{ $lead->email ?: '—' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $colorClasses[$lead->statusColor()] }}">{{ $lead->statusLabel() }}</span>
                <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs">{{ $lead->sourceLabel() }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('leads.status', $lead) }}" class="mt-4 flex items-center gap-2 flex-wrap">
            @csrf @method('PATCH')
            <span class="text-xs text-slate-500">Statusni o'zgartirish:</span>
            @foreach(\App\Models\Lead::STATUSES as $key => $info)
                <button type="submit" name="status" value="{{ $key }}" class="rounded-full px-3 py-1 text-xs font-medium {{ $lead->status === $key ? $colorClasses[$info['color']] : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">{{ $info['label'] }}</button>
            @endforeach
        </form>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
                <h3 class="text-base font-semibold text-slate-900 mb-3">Tafsilotlar</h3>
                <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500">Operator</dt>
                        <dd class="text-slate-900">{{ $lead->operator->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Qiziqgan uy</dt>
                        <dd class="text-slate-900">{{ $lead->property->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Byudjet</dt>
                        <dd class="text-slate-900">{{ $lead->budget ? number_format($lead->budget, 0, '.', ' ') . ' UZS' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Keyingi aloqa</dt>
                        <dd class="{{ $lead->next_follow_up?->isPast() ? 'text-red-600' : 'text-slate-900' }}">{{ $lead->next_follow_up?->format('d.m.Y') ?? '—' }}</dd>
                    </div>
                </dl>
                @if($lead->notes)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-500 mb-1">Izoh</p>
                        <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $lead->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-slate-900">Eslatmalar</h3>
                    <a href="{{ route('reminders.create', ['lead_id' => $lead->id]) }}" class="text-xs text-cyan-600 hover:text-cyan-700"><i class="fas fa-plus text-[10px]"></i> Qo'shish</a>
                </div>
                @if($lead->reminders->isEmpty())
                    <p class="text-sm text-slate-500 text-center py-4">Eslatma yo'q</p>
                @else
                    <div class="space-y-2">
                        @foreach($lead->reminders as $r)
                            <div class="rounded-xl bg-slate-50 p-3 text-sm">
                                <p class="font-medium text-slate-900">{{ $r->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $r->remind_at->format('d.m.Y H:i') }} · {{ $r->user->name }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($lead->status !== 'won')
<div id="convertModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Savdoga aylantirish</h3>
        <form action="{{ route('leads.convert', $lead) }}" method="POST" class="space-y-3" x-data="{ price: 0, priceInput: '' }">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Uy</label>
                <select name="property_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    @foreach(\App\Models\Property::orderBy('title')->get() as $p)
                        <option value="{{ $p->id }}" @selected($lead->property_id == $p->id)>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Narx (UZS)</label>
                <input type="text" inputmode="numeric" x-model="priceInput"
                    @input="
                        const raw = $event.target.value.replace(/\D/g, '');
                        price = raw ? parseInt(raw, 10) : 0;
                        priceInput = price ? price.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                        $event.target.value = priceInput;
                    "
                    required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <input type="hidden" name="price" :value="price">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Turi</label>
                    <select name="type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="sale">Sotuv</option>
                        <option value="rent">Ijara</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Sana</label>
                    <input type="date" name="sale_date" value="{{ now()->format('Y-m-d') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('convertModal').classList.add('hidden')" class="rounded-xl px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Bekor</button>
                <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-medium">Savdoga aylantirish</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endif
@endsection
