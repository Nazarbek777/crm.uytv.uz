@extends('layouts.main')

@section('title', 'Eslatmalar')

@section('actions')
    <a href="{{ route('reminders.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-plus text-xs"></i> Yangi eslatma
    </a>
@endsection

@section('content')
<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-4">
    <a href="{{ route('reminders.index', ['filter' => 'all']) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'all' ? 'border-slate-900 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700"><i class="fas fa-bell text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Hammasi</p><p class="text-lg font-bold">{{ $counts['today'] + $counts['overdue'] + $counts['upcoming'] }}</p></div>
    </a>
    <a href="{{ route('reminders.index', ['filter' => 'today']) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'today' ? 'border-cyan-600 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700"><i class="fas fa-calendar-day text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Bugun</p><p class="text-lg font-bold">{{ $counts['today'] }}</p></div>
    </a>
    <a href="{{ route('reminders.index', ['filter' => 'overdue']) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'overdue' ? 'border-red-500 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700"><i class="fas fa-circle-exclamation text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Kechikkan</p><p class="text-lg font-bold text-red-600">{{ $counts['overdue'] }}</p></div>
    </a>
    <a href="{{ route('reminders.index', ['filter' => 'upcoming']) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'upcoming' ? 'border-emerald-500 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fas fa-clock text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Kelajakda</p><p class="text-lg font-bold">{{ $counts['upcoming'] }}</p></div>
    </a>
</div>

<div class="flex items-center gap-2 mb-3 text-sm">
    <a href="{{ route('reminders.index', ['filter' => 'completed']) }}" class="rounded-full px-3 py-1 {{ $filter === 'completed' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Bajarilgan</a>
</div>

<div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
    @if($reminders->isEmpty())
        <div class="px-5 py-16 text-center">
            <i class="fas fa-bell-slash text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-sm text-slate-500">Eslatmalar yo'q</p>
        </div>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($reminders as $r)
                @php
                    $isOverdue = !$r->completed && $r->remind_at->isPast();
                    $isToday = !$r->completed && $r->remind_at->isToday() && !$isOverdue;
                @endphp
                <div class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50/60 transition">
                    <form action="{{ route($r->completed ? 'reminders.uncomplete' : 'reminders.complete', $r) }}" method="POST" class="pt-0.5">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center justify-center w-5 h-5 rounded-full border-2 {{ $r->completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-slate-500' }}">
                            @if($r->completed)<i class="fas fa-check text-[10px]"></i>@endif
                        </button>
                    </form>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-medium {{ $r->completed ? 'text-slate-400 line-through' : 'text-slate-900' }}">{{ $r->title }}</p>
                            @if($isOverdue)
                                <span class="inline-flex rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[10px] font-semibold">Kechikkan</span>
                            @elseif($isToday)
                                <span class="inline-flex rounded-full bg-cyan-100 text-cyan-700 px-2 py-0.5 text-[10px] font-semibold">Bugun</span>
                            @endif
                        </div>
                        @if($r->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $r->description }}</p>
                        @endif
                        <div class="mt-1 flex items-center gap-3 text-[11px] text-slate-400 flex-wrap">
                            <span><i class="far fa-clock"></i> {{ $r->remind_at->format('d.m.Y H:i') }}</span>
                            <span><i class="far fa-user"></i> {{ $r->user->name }}</span>
                            @if($r->lead)<a href="{{ route('leads.show', $r->lead) }}" class="hover:text-cyan-600"><i class="fas fa-user-plus"></i> {{ $r->lead->name }}</a>@endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('reminders.edit', $r) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100"><i class="fas fa-pen text-xs"></i></a>
                        <form action="{{ route('reminders.destroy', $r) }}" method="POST" onsubmit="return confirm('O\'chirilsinmi?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        @if($reminders->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $reminders->links() }}</div>
        @endif
    @endif
</div>
@endsection
