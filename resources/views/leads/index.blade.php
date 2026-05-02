@extends('layouts.main')

@section('title', 'Lidlar — Ro\'yxat')

@section('actions')
    <a href="{{ route('leads.index', ['view' => 'pipeline']) }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-3 py-2.5 text-sm font-medium transition" title="Pipeline ko'rinishi">
        <i class="fas fa-table-columns text-xs"></i>
    </a>
    <a href="{{ route('leads.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-plus text-xs"></i> Yangi lid
    </a>
@endsection

@section('content')
@php
    $colorClasses = [
        'slate' => 'bg-slate-50 text-slate-700 border-slate-200',
        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
        'cyan' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'red' => 'bg-red-50 text-red-700 border-red-200',
    ];
@endphp

<form method="GET" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm mb-4">
    <input type="hidden" name="view" value="list">
    <div class="grid gap-3 sm:grid-cols-12">
        <div class="sm:col-span-4">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Qidirish..." class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
        </div>
        <div class="sm:col-span-3">
            <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Barcha statuslar</option>
                @foreach(\App\Models\Lead::STATUSES as $key => $info)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $info['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-3">
            <select name="operator_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Barcha operatorlar</option>
                @foreach($operators as $op)
                    <option value="{{ $op->id }}" @selected(request('operator_id') == $op->id)>{{ $op->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <select name="source" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Manba</option>
                @foreach(\App\Models\Lead::SOURCES as $key => $label)
                    <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

<div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
    @if($leads->isEmpty())
        <div class="px-5 py-16 text-center">
            <i class="fas fa-user-plus text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-sm text-slate-500">Lid topilmadi</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Lid</th>
                        <th class="px-4 py-3 text-left font-medium">Telefon</th>
                        <th class="px-4 py-3 text-left font-medium">Manba</th>
                        <th class="px-4 py-3 text-left font-medium">Operator</th>
                        <th class="px-4 py-3 text-left font-medium">Uy</th>
                        <th class="px-4 py-3 text-center font-medium">Aloqa sanasi</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($leads as $lead)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-4 py-3"><a href="{{ route('leads.show', $lead) }}" class="font-medium text-slate-900 hover:text-cyan-700">{{ $lead->name }}</a></td>
                            <td class="px-4 py-3 text-slate-600">{{ $lead->phone }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $lead->sourceLabel() }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $lead->operator->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $lead->property->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-xs {{ $lead->next_follow_up?->isPast() ? 'text-red-500 font-semibold' : 'text-slate-500' }}">
                                {{ $lead->next_follow_up?->format('d.m.Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $colorClasses[$lead->statusColor()] ?? 'bg-slate-100 text-slate-700' }}">{{ $lead->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('leads.show', $lead) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('leads.edit', $lead) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"><i class="fas fa-pen text-xs"></i></a>
                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('O\'chirilsinmi?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    @endif
</div>
@endsection
