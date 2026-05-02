@extends('layouts.main')

@section('title', 'Lidlar — Pipeline')

@section('actions')
    <a href="{{ route('leads.index', ['view' => 'list']) }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-3 py-2.5 text-sm font-medium transition" title="Ro'yxat ko'rinishi">
        <i class="fas fa-list text-xs"></i>
    </a>
    <a href="{{ route('leads.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-plus text-xs"></i> Yangi lid
    </a>
@endsection

@section('content')
@php
    $colors = [
        'slate' => 'border-slate-300 bg-slate-50',
        'blue' => 'border-blue-300 bg-blue-50',
        'cyan' => 'border-cyan-300 bg-cyan-50',
        'amber' => 'border-amber-300 bg-amber-50',
        'emerald' => 'border-emerald-300 bg-emerald-50',
        'red' => 'border-red-300 bg-red-50',
    ];
    $headerColors = [
        'slate' => 'text-slate-700', 'blue' => 'text-blue-700', 'cyan' => 'text-cyan-700',
        'amber' => 'text-amber-700', 'emerald' => 'text-emerald-700', 'red' => 'text-red-700',
    ];
@endphp

<form method="GET" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm mb-4">
    <input type="hidden" name="view" value="pipeline">
    <div class="grid gap-3 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-search text-sm"></i></span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Ism, telefon, email..." class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-slate-500 focus:outline-none">
            </div>
        </div>
        <div class="sm:col-span-3">
            <select name="operator_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Barcha operatorlar</option>
                @foreach($operators as $op)
                    <option value="{{ $op->id }}" @selected(request('operator_id') == $op->id)>{{ $op->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-3">
            <select name="source" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Barcha manbalar</option>
                @foreach(\App\Models\Lead::SOURCES as $key => $label)
                    <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

<div class="overflow-x-auto pb-4">
    <div class="grid gap-3" style="grid-template-columns: repeat({{ count(\App\Models\Lead::STATUSES) }}, minmax(280px, 1fr)); min-width: max-content;">
        @foreach(\App\Models\Lead::STATUSES as $statusKey => $info)
            @php $items = $leads[$statusKey] ?? collect(); @endphp
            <div class="rounded-2xl border-2 {{ $colors[$info['color']] ?? 'border-slate-300 bg-slate-50' }} p-3" data-status="{{ $statusKey }}">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h3 class="text-sm font-semibold {{ $headerColors[$info['color']] ?? 'text-slate-700' }}">{{ $info['label'] }}</h3>
                    <span class="text-xs font-bold {{ $headerColors[$info['color']] ?? 'text-slate-700' }}">{{ $items->count() }}</span>
                </div>
                <div class="space-y-2 min-h-[100px]" data-droppable>
                    @forelse($items as $lead)
                        <a href="{{ route('leads.show', $lead) }}" draggable="true" data-lead-id="{{ $lead->id }}" class="block rounded-xl bg-white p-3 border border-slate-200 hover:border-slate-400 hover:shadow-sm cursor-grab transition">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $lead->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $lead->phone }}</p>
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                @if($lead->budget)
                                    <span class="text-[10px] bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">{{ number_format($lead->budget / 1000000, 1) }}M</span>
                                @endif
                                @if($lead->rooms_wanted)
                                    <span class="text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded">{{ $lead->rooms_wanted }} xona</span>
                                @endif
                                @if($lead->payment_method)
                                    <span class="text-[10px] bg-cyan-50 text-cyan-700 px-1.5 py-0.5 rounded">{{ \App\Models\Lead::PAYMENT_METHODS[$lead->payment_method] ?? '' }}</span>
                                @endif
                                @if($lead->urgency === 'immediate')
                                    <span class="text-[10px] bg-red-50 text-red-700 px-1.5 py-0.5 rounded font-semibold">🔥 Hoziroq</span>
                                @endif
                            </div>
                            @if($lead->preferred_district)
                                <p class="text-[11px] text-slate-400 mt-1 truncate"><i class="fas fa-location-dot mr-1"></i>{{ $lead->preferred_district }}</p>
                            @endif
                            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                                <span class="truncate">{{ $lead->operator->name ?? '—' }}</span>
                                @if($lead->next_follow_up)
                                    <span class="{{ $lead->next_follow_up->isPast() ? 'text-red-500 font-semibold' : '' }}"><i class="far fa-calendar mr-0.5"></i>{{ $lead->next_follow_up->format('d.m') }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Bo'sh</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    let dragged = null;
    document.querySelectorAll('[data-lead-id]').forEach(el => {
        el.addEventListener('dragstart', e => { dragged = el; el.style.opacity = '0.4'; });
        el.addEventListener('dragend', e => { el.style.opacity = '1'; });
    });
    document.querySelectorAll('[data-droppable]').forEach(zone => {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('bg-white/40'); });
        zone.addEventListener('dragleave', e => { zone.classList.remove('bg-white/40'); });
        zone.addEventListener('drop', async e => {
            e.preventDefault();
            zone.classList.remove('bg-white/40');
            if (!dragged) return;
            const newStatus = zone.closest('[data-status]').dataset.status;
            const leadId = dragged.dataset.leadId;
            zone.appendChild(dragged);
            try {
                await fetch(`/leads/${leadId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                location.reload();
            } catch (err) {
                console.error(err);
                location.reload();
            }
        });
    });
</script>
@endsection
