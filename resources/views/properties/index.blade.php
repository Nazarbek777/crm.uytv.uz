@extends('layouts.main')

@section('title', 'Uylar')

@section('actions')
    <a href="{{ route('properties.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-plus text-xs"></i> Yangi uy
    </a>
@endsection

@section('content')
@php
    $statusBadge = [
        'free' => ['label' => 'Bo\'sh', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'sold' => ['label' => 'Sotilgan', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'rent' => ['label' => 'Ijarada', 'class' => 'bg-orange-50 text-orange-700 border-orange-200'],
    ];
@endphp

<form method="GET" class="rounded-2xl bg-white p-4 border border-slate-200 shadow-sm mb-4">
    <div class="grid gap-3 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-search text-sm"></i></span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, manzil bo'yicha qidirish..." class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-slate-500 focus:outline-none">
            </div>
        </div>
        <div class="sm:col-span-3">
            <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                <option value="">Barcha holatlar</option>
                <option value="free" @selected(request('status') === 'free')>Bo'sh</option>
                <option value="sold" @selected(request('status') === 'sold')>Sotilgan</option>
                <option value="rent" @selected(request('status') === 'rent')>Ijarada</option>
            </select>
        </div>
        <div class="sm:col-span-3">
            <select name="investor_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                <option value="">Barcha investorlar</option>
                @foreach($investors as $inv)
                    <option value="{{ $inv->id }}" @selected(request('investor_id') == $inv->id)>{{ $inv->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @if(request()->hasAny(['search', 'status', 'investor_id']))
        <div class="mt-3 flex items-center justify-between text-xs">
            <span class="text-slate-500">{{ $properties->total() }} ta natija</span>
            <a href="{{ route('properties.index') }}" class="text-slate-500 hover:text-slate-900">Filtrlarni tozalash <i class="fas fa-times ml-0.5"></i></a>
        </div>
    @endif
</form>

<div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
    @if($properties->isEmpty())
        <div class="px-5 py-16 text-center">
            <i class="fas fa-building text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-sm text-slate-500">Uylar topilmadi</p>
            <a href="{{ route('properties.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 text-sm font-medium transition">
                <i class="fas fa-plus text-xs"></i> Birinchi uyni qo'shish
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Uy</th>
                        <th class="px-4 py-3 text-left font-medium">Manzil</th>
                        <th class="px-4 py-3 text-left font-medium">Investor</th>
                        <th class="px-4 py-3 text-left font-medium">Xususiyatlari</th>
                        <th class="px-4 py-3 text-right font-medium">Narx</th>
                        <th class="px-4 py-3 text-center font-medium">Holat</th>
                        <th class="px-4 py-3 text-right font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($properties as $property)
                        @php $badge = $statusBadge[$property->status] ?? ['label' => $property->status, 'class' => 'bg-slate-100 text-slate-700']; @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $property->title }}</p>
                                <p class="text-[11px] text-slate-400">#{{ $property->id }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $property->address }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $property->investor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                @if($property->rooms){{ $property->rooms }} xona @endif
                                @if($property->area) · {{ $property->area }} m² @endif
                                @if($property->floor && $property->total_floors) · {{ $property->floor }}/{{ $property->total_floors }} qavat @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="font-semibold text-slate-900">{{ number_format($property->price, 0, '.', ' ') }}</p>
                                <p class="text-[11px] text-slate-400">UZS</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('properties.edit', $property) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition" title="Tahrirlash">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('properties.destroy', $property) }}" method="POST" class="inline" onsubmit="return confirm('Bu uyni o\'chirishni tasdiqlaysizmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition" title="O'chirish">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($properties->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $properties->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
