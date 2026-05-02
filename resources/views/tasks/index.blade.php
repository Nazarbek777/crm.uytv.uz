@extends('layouts.main')

@section('title', 'Tasklar')

@section('actions')
    <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 text-sm font-medium transition">
        <i class="fas fa-plus text-xs"></i> Yangi task
    </a>
@endsection

@section('content')
@php
    $priColors = [
        'red' => 'border-l-red-500', 'amber' => 'border-l-amber-500',
        'blue' => 'border-l-blue-400', 'slate' => 'border-l-slate-300',
    ];
    $priBadge = [
        'red' => 'bg-red-50 text-red-700', 'amber' => 'bg-amber-50 text-amber-700',
        'blue' => 'bg-blue-50 text-blue-700', 'slate' => 'bg-slate-100 text-slate-600',
    ];
    $statusBadge = [
        'slate' => 'bg-slate-100 text-slate-700', 'cyan' => 'bg-cyan-100 text-cyan-700',
        'emerald' => 'bg-emerald-100 text-emerald-700', 'red' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-4">
    <a href="{{ route('tasks.index', ['filter' => 'active'] + (request('user_id') ? ['user_id' => request('user_id')] : [])) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'active' ? 'border-slate-900 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700"><i class="fas fa-list-check text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Faol</p><p class="text-lg font-bold">{{ $counts['active'] }}</p></div>
    </a>
    <a href="{{ route('tasks.index', ['filter' => 'today'] + (request('user_id') ? ['user_id' => request('user_id')] : [])) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'today' ? 'border-cyan-500 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700"><i class="fas fa-calendar-day text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Bugun</p><p class="text-lg font-bold">{{ $counts['today'] }}</p></div>
    </a>
    <a href="{{ route('tasks.index', ['filter' => 'overdue'] + (request('user_id') ? ['user_id' => request('user_id')] : [])) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'overdue' ? 'border-red-500 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-700"><i class="fas fa-circle-exclamation text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Kechikkan</p><p class="text-lg font-bold text-red-600">{{ $counts['overdue'] }}</p></div>
    </a>
    <a href="{{ route('tasks.index', ['filter' => 'done'] + (request('user_id') ? ['user_id' => request('user_id')] : [])) }}" class="rounded-2xl bg-white p-4 border {{ $filter === 'done' ? 'border-emerald-500 shadow' : 'border-slate-200' }} flex items-center gap-3 hover:shadow transition">
        <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fas fa-check-double text-sm"></i></div>
        <div><p class="text-xs text-slate-500">Bajarilgan</p><p class="text-lg font-bold">{{ $counts['done'] }}</p></div>
    </a>
</div>

@if(auth()->user()->isManager() && $users->isNotEmpty())
<form method="GET" class="flex items-center gap-2 mb-4 text-sm">
    <input type="hidden" name="filter" value="{{ $filter }}">
    <span class="text-slate-500">Operator:</span>
    <select name="user_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
        <option value="">Hammasi</option>
        @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
        @endforeach
    </select>
</form>
@endif

@if($autoTasks->isNotEmpty() && in_array($filter, ['active', 'today']))
<div class="rounded-2xl bg-gradient-to-br from-cyan-50 to-blue-50 border border-cyan-200 shadow-sm overflow-hidden mb-4">
    <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-cyan-200/60">
        <div class="flex items-center gap-2">
            <i class="fas fa-phone-volume text-cyan-700"></i>
            <h3 class="text-base font-semibold text-cyan-900">Bugun aloqa kerak — {{ $autoTasks->count() }} ta lid</h3>
        </div>
        <span class="text-[11px] text-cyan-700 bg-cyan-100 rounded-full px-2 py-0.5">Avtomatik</span>
    </div>
    <div class="divide-y divide-cyan-100">
        @foreach($autoTasks as $lead)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white text-cyan-700 shadow-sm">
                    <i class="fas fa-phone text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 truncate">{{ $lead->name }} — {{ $lead->phone }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Status: <span class="font-medium">{{ $lead->statusLabel() }}</span>
                        @if($lead->budget) · Byudjet: {{ number_format($lead->budget / 1000000, 1) }}M @endif
                        @if($lead->rooms_wanted) · {{ $lead->rooms_wanted }} xona @endif
                        @if($lead->payment_method) · {{ \App\Models\Lead::PAYMENT_METHODS[$lead->payment_method] ?? '' }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    @if($lead->phone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $lead->phone) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white" title="Qo'ng'iroq qilish">
                            <i class="fas fa-phone text-sm"></i>
                        </a>
                    @endif
                    <a href="{{ route('leads.show', $lead) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200" title="Lid sahifasi">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
    @if($tasks->isEmpty())
        <div class="px-5 py-16 text-center">
            <i class="fas fa-clipboard-check text-4xl text-slate-300 mb-3 block"></i>
            <p class="text-sm text-slate-500">Tasklar yo'q</p>
        </div>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($tasks as $task)
                @php
                    $isDone = $task->status === 'done';
                    $isCancelled = $task->status === 'cancelled';
                    $isOverdue = $task->isOverdue();
                @endphp
                <div class="flex items-start gap-3 px-5 py-4 border-l-4 {{ $priColors[$task->priorityColor()] ?? 'border-l-slate-300' }} {{ $isDone || $isCancelled ? 'opacity-60' : '' }}">
                    <form action="{{ route('tasks.status', $task) }}" method="POST" class="pt-0.5">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $isDone ? 'pending' : 'done' }}">
                        <button type="submit" class="inline-flex items-center justify-center w-5 h-5 rounded-full border-2 {{ $isDone ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-slate-500' }}">
                            @if($isDone)<i class="fas fa-check text-[10px]"></i>@endif
                        </button>
                    </form>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-medium {{ $isDone ? 'text-slate-400 line-through' : 'text-slate-900' }}">{{ $task->title }}</p>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium {{ $priBadge[$task->priorityColor()] ?? 'bg-slate-100' }}">{{ $task->priorityLabel() }}</span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium {{ $statusBadge[$task->statusColor()] ?? 'bg-slate-100' }}">{{ $task->statusLabel() }}</span>
                            @if($isOverdue)
                                <span class="inline-flex rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[10px] font-semibold">Kechikkan</span>
                            @endif
                        </div>
                        @if($task->description)
                            <p class="text-xs text-slate-500 mt-1 whitespace-pre-wrap">{{ $task->description }}</p>
                        @endif
                        <div class="mt-1.5 flex items-center gap-3 text-[11px] text-slate-400 flex-wrap">
                            @if($task->due_date)
                                <span class="{{ $isOverdue ? 'text-red-500 font-semibold' : '' }}"><i class="far fa-calendar"></i> {{ $task->due_date->format('d.m.Y') }}</span>
                            @endif
                            <span><i class="far fa-user"></i> {{ $task->user->name }}</span>
                            @if($task->assigner && $task->assigned_by !== $task->user_id)
                                <span><i class="fas fa-arrow-right-from-bracket"></i> {{ $task->assigner->name }}</span>
                            @endif
                            @if($task->lead)<a href="{{ route('leads.show', $task->lead) }}" class="hover:text-cyan-600"><i class="fas fa-user-plus"></i> {{ $task->lead->name }}</a>@endif
                        </div>
                        @if(!$isDone && !$isCancelled && $task->status === 'pending')
                            <form action="{{ route('tasks.status', $task) }}" method="POST" class="mt-2 inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="text-xs text-cyan-600 hover:text-cyan-700 font-medium"><i class="fas fa-play text-[10px] mr-0.5"></i> Bajarishni boshlash</button>
                            </form>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('tasks.edit', $task) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100"><i class="fas fa-pen text-xs"></i></a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('O\'chirilsinmi?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        @if($tasks->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $tasks->links() }}</div>
        @endif
    @endif
</div>
@endsection
