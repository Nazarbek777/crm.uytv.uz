@extends('layouts.main')

@section('title', 'Operatorlar')

@section('actions')
    <a href="{{ route('operators.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-plus text-xs"></i> Yangi operator
    </a>
@endsection

@section('content')
@php
    $roleBadge = [
        'admin' => ['label' => 'Admin', 'class' => 'bg-red-50 text-red-700 border-red-200'],
        'manager' => ['label' => 'Menejer', 'class' => 'bg-violet-50 text-violet-700 border-violet-200'],
        'operator' => ['label' => 'Operator', 'class' => 'bg-cyan-50 text-cyan-700 border-cyan-200'],
    ];
@endphp

<form method="GET" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm mb-4">
    <div class="grid gap-3 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-search text-sm"></i></span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Ism, email, telefon..." class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-slate-500 focus:outline-none">
            </div>
        </div>
        <div class="sm:col-span-3">
            <select name="role" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Barcha lavozimlar</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="manager" @selected(request('role') === 'manager')>Menejer</option>
                <option value="operator" @selected(request('role') === 'operator')>Operator</option>
            </select>
        </div>
        <div class="sm:col-span-3">
            <select name="active" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Barcha holatlar</option>
                <option value="1" @selected(request('active') === '1')>Faol</option>
                <option value="0" @selected(request('active') === '0')>Faolsiz</option>
            </select>
        </div>
    </div>
</form>

<div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
    @if($operators->isEmpty())
        <div class="px-5 py-16 text-center">
            <i class="fas fa-user-tie text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-sm text-slate-500">Operatorlar topilmadi</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Operator</th>
                        <th class="px-4 py-3 text-left font-medium">Aloqa</th>
                        <th class="px-4 py-3 text-center font-medium">Lavozim</th>
                        <th class="px-4 py-3 text-center font-medium">Lidlar</th>
                        <th class="px-4 py-3 text-center font-medium">Savdolar</th>
                        <th class="px-4 py-3 text-center font-medium">Holat</th>
                        <th class="px-4 py-3 text-right font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($operators as $op)
                        @php $b = $roleBadge[$op->role] ?? ['label' => $op->role, 'class' => 'bg-slate-100 text-slate-700']; @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 text-sm font-semibold">{{ mb_strtoupper(mb_substr($op->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $op->name }}</p>
                                        <p class="text-[11px] text-slate-400">#{{ $op->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <p>{{ $op->email }}</p>
                                <p class="text-[11px] text-slate-400">{{ $op->phone ?: '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $b['class'] }}">{{ $b['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-slate-700">{{ $op->leads_count }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-slate-700">{{ $op->sales_count }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($op->active)
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Faol</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-400"><span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Faolsiz</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('operators.show', $op) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition" title="Ko'rish"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('operators.edit', $op) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition" title="Tahrirlash"><i class="fas fa-pen text-xs"></i></a>
                                @if($op->id !== auth()->id())
                                    <form action="{{ route('operators.destroy', $op) }}" method="POST" class="inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition"><i class="fas fa-trash text-xs"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($operators->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $operators->links() }}</div>
        @endif
    @endif
</div>
@endsection
