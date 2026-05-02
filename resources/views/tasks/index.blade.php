@extends('layouts.main')

@section('title', 'Ish doskasi')

@section('content')
@php
    $isManager = auth()->user()->isManager();
    $controller = app(\App\Http\Controllers\TaskController::class);

    $taskCards = $tasks->map(fn($t) => $controller->serializeTask($t))->all();
    $leadCards = $leads->map(fn($l) => $controller->serializeLead($l))->all();

    $statusList = collect(\App\Models\Task::STATUSES)->map(fn($v, $k) => array_merge($v, ['key' => $k]))->values();
    $priorityList = collect(\App\Models\Task::PRIORITIES)->map(fn($v, $k) => array_merge($v, ['key' => $k]))->values();
@endphp

<style>
    .col-scroll { scrollbar-width: thin; scrollbar-color: rgba(148,163,184,0.3) transparent; }
    .col-scroll::-webkit-scrollbar { width: 6px; }
    .col-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.3); border-radius: 3px; }
    .ws-card { transition: transform 150ms, box-shadow 150ms, opacity 150ms; }
    .ws-card.dragging { opacity: 0.4; transform: scale(0.97) rotate(2deg); }
    .drop-zone { transition: background 150ms; }
    .drop-zone.drag-over { background: rgba(34,211,238,0.08); outline: 2px dashed rgb(34,211,238); outline-offset: -8px; }
    .priority-bar { width: 3px; border-radius: 999px; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<div x-data="workspace()" x-init="init()" class="relative">
    {{-- Toolbar --}}
    <div class="flex flex-col gap-2 mb-4">
        <div class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1 text-sm">
                <button @click="view = 'board'" :class="view === 'board' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition flex items-center gap-1.5">
                    <i class="fas fa-table-columns text-xs"></i> Board
                </button>
                <button @click="view = 'list'" :class="view === 'list' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition flex items-center gap-1.5">
                    <i class="fas fa-list text-xs"></i> Ro'yxat
                </button>
            </div>

            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl p-1 text-sm">
                <button @click="kindFilter = 'all'" :class="kindFilter === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-2.5 py-1.5 font-medium transition text-xs">Hammasi</button>
                <button @click="kindFilter = 'task'" :class="kindFilter === 'task' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-2.5 py-1.5 font-medium transition text-xs">Tasklar</button>
                <button @click="kindFilter = 'lead'" :class="kindFilter === 'lead' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-2.5 py-1.5 font-medium transition text-xs">Lidlar</button>
            </div>

            <input type="search" x-model="search" placeholder="Qidirish..." class="flex-1 sm:flex-initial sm:w-56 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">

            @if($isManager)
                <select x-model="userFilter" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                    <option value="">Hamma operator</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            @endif

            <button @click="openCreate()" class="ml-auto inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 text-sm font-medium transition whitespace-nowrap">
                <i class="fas fa-plus text-xs"></i> Yangi task
            </button>
        </div>

        {{-- Date filter chips --}}
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs text-slate-500 mr-1">Sana:</span>
            <button @click="setDate('all')" :class="dateFilter === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1 text-xs font-medium transition">Hammasi</button>
            <button @click="setDate('overdue')" :class="dateFilter === 'overdue' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1 text-xs font-medium transition">Kechikkan</button>
            <button @click="setDate('today')" :class="dateFilter === 'today' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1 text-xs font-medium transition">Bugun</button>
            <button @click="setDate('tomorrow')" :class="dateFilter === 'tomorrow' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1 text-xs font-medium transition">Ertaga</button>
            <button @click="setDate('week')" :class="dateFilter === 'week' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1 text-xs font-medium transition">Bu hafta</button>
            <button @click="setDate('month')" :class="dateFilter === 'month' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1 text-xs font-medium transition">Bu oy</button>
            <div class="flex items-center gap-1 ml-2 bg-white border border-slate-200 rounded-full px-2 py-0.5">
                <input type="date" x-model="dateFrom" @change="dateFilter = 'custom'" class="text-xs border-0 bg-transparent focus:outline-none w-32">
                <span class="text-slate-300">→</span>
                <input type="date" x-model="dateTo" @change="dateFilter = 'custom'" class="text-xs border-0 bg-transparent focus:outline-none w-32">
                <button x-show="dateFrom || dateTo" @click="dateFrom=''; dateTo=''; dateFilter='all'" class="text-slate-400 hover:text-slate-700 text-xs"><i class="fas fa-xmark"></i></button>
            </div>
        </div>
    </div>

    {{-- Quick add bar --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-2.5 mb-4 flex items-center gap-2">
        <i class="fas fa-plus text-slate-400 text-sm pl-2"></i>
        <input type="text" x-model="quickTitle" @keydown.enter="quickAdd()" placeholder="Tezkor task — yozing va Enter bosing..." class="flex-1 bg-transparent text-sm focus:outline-none placeholder:text-slate-400">
        <select x-model="quickPriority" class="text-xs rounded-lg border border-slate-200 px-2 py-1 bg-white">
            <option value="low">Past</option>
            <option value="normal">Oddiy</option>
            <option value="high">Yuqori</option>
            <option value="urgent">Shoshilinch</option>
        </select>
        <button @click="quickAdd()" :disabled="!quickTitle.trim()" class="rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-medium transition disabled:opacity-30">Qo'shish</button>
    </div>

    {{-- BOARD --}}
    <div x-show="view === 'board'" class="overflow-x-auto pb-4">
        <div class="grid gap-3" style="grid-template-columns: repeat(4, minmax(300px, 1fr)); min-width: max-content;">
            <template x-for="col in columns" :key="col.key">
                <div :class="`rounded-2xl border ${col.bg} flex flex-col`" style="height: calc(100vh - 280px); min-height: 400px;">
                    <div class="px-3 py-2.5 border-b flex items-center justify-between" :class="col.border">
                        <div class="flex items-center gap-2">
                            <span :class="`inline-block w-2 h-2 rounded-full ${col.dot}`"></span>
                            <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wider" x-text="col.label"></h3>
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-200/60 rounded-full px-1.5" x-text="cardsByStatus[col.key].length"></span>
                        </div>
                        <button @click="quickAddTo(col.key)" class="text-slate-400 hover:text-slate-700 p-1" title="Qo'shish">
                            <i class="fas fa-plus text-[11px]"></i>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto col-scroll p-2 space-y-2 drop-zone" :data-status="col.key"
                        @dragover.prevent="$event.currentTarget.classList.add('drag-over')"
                        @dragleave="$event.currentTarget.classList.remove('drag-over')"
                        @drop.prevent="onDrop($event, col.key)">
                        <template x-for="card in cardsByStatus[col.key]" :key="card.kind + '-' + card.id">
                            <div :class="`ws-card group rounded-xl bg-white border hover:shadow-md p-3 cursor-grab ${card.is_overdue ? 'border-red-200 ring-1 ring-red-200/50' : 'border-slate-200 hover:border-slate-300'}`"
                                draggable="true"
                                :data-card-id="card.kind + ':' + card.id"
                                @dragstart="onDragStart($event, card)"
                                @dragend="onDragEnd($event)"
                                @click="openCard(card)">
                                <div class="flex items-start gap-2">
                                    <div :class="priorityBar(card.priority_color)" class="priority-bar self-stretch"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span x-show="card.kind === 'lead'" class="inline-flex h-4 w-4 items-center justify-center rounded bg-cyan-100 text-cyan-700 flex-shrink-0">
                                                <i class="fas fa-user-plus text-[8px]"></i>
                                            </span>
                                            <p class="text-sm font-medium text-slate-900 line-clamp-2" x-text="card.title"></p>
                                        </div>
                                        <p x-show="card.description" class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" x-text="card.description"></p>

                                        {{-- Lead-specific info --}}
                                        <template x-if="card.kind === 'lead'">
                                            <div class="mt-1 text-[11px] text-slate-500 space-y-0.5">
                                                <div class="flex items-center gap-1"><i class="fas fa-phone text-[9px]"></i> <span x-text="card.phone"></span></div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span x-show="card.budget" class="text-emerald-700 font-medium" x-text="(card.budget/1000000).toFixed(1) + 'M'"></span>
                                                    <span x-show="card.rooms_wanted" x-text="card.rooms_wanted + ' xona'"></span>
                                                    <span x-show="card.payment_label" class="text-cyan-700" x-text="card.payment_label"></span>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="mt-2 flex items-center gap-2 flex-wrap text-[10px]">
                                            <span :class="priorityChip(card.priority_color)" class="rounded px-1.5 py-0.5 font-medium" x-text="card.priority_label"></span>
                                            <span x-show="card.due_date_human" :class="card.is_overdue ? 'text-red-600 font-semibold' : 'text-slate-500'" class="inline-flex items-center gap-1">
                                                <i class="far fa-calendar"></i> <span x-text="card.due_date_human"></span>
                                            </span>
                                            <span x-show="card.kind === 'task' && card.lead_id" class="text-slate-500"><i class="fas fa-link text-[9px]"></i> <span x-text="card.lead_name"></span></span>
                                            <span x-show="card.comments_count > 0" class="text-slate-500 inline-flex items-center gap-1"><i class="far fa-comment text-[9px]"></i> <span x-text="card.comments_count"></span></span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 text-[10px] text-slate-500">
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[9px] font-bold text-slate-600" x-text="(card.user_name || '?').charAt(0).toUpperCase()"></span>
                                                <span x-text="card.user_name"></span>
                                            </div>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                                <a x-show="card.kind === 'lead' && card.phone_clean" :href="'tel:' + card.phone_clean" @click.stop class="text-emerald-600 hover:text-emerald-700 p-1" title="Qo'ng'iroq"><i class="fas fa-phone text-[10px]"></i></a>
                                                <button x-show="card.kind === 'task'" @click.stop="deleteCard(card)" class="text-slate-400 hover:text-red-600 p-1"><i class="fas fa-trash text-[10px]"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <p x-show="cardsByStatus[col.key].length === 0" class="text-center text-xs text-slate-400 py-6">Bo'sh</p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- LIST VIEW --}}
    <div x-show="view === 'list'" class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <template x-if="filteredCards.length === 0">
            <div class="px-5 py-16 text-center">
                <i class="fas fa-clipboard-check text-4xl text-slate-300 mb-3 block"></i>
                <p class="text-sm text-slate-500">Hech narsa topilmadi</p>
            </div>
        </template>
        <div class="divide-y divide-slate-100">
            <template x-for="card in filteredCards" :key="'l-' + card.kind + '-' + card.id">
                <div :class="`border-l-4 ${priorityBorder(card.priority_color)} ${card.status === 'done' || card.status === 'cancelled' ? 'opacity-60' : ''}`" class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50/40 transition group cursor-pointer" @click="openCard(card)">
                    <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded {{ '' }}" :class="card.kind === 'lead' ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-100 text-slate-700'">
                        <i :class="card.kind === 'lead' ? 'fas fa-user-plus text-[10px]' : 'fas fa-clipboard-list text-[10px]'"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p :class="card.status === 'done' ? 'text-slate-400 line-through' : 'text-slate-900'" class="text-sm font-medium" x-text="card.title"></p>
                            <span x-show="card.is_overdue" class="inline-flex rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[10px] font-semibold">Kechikkan</span>
                        </div>
                        <div class="mt-1 flex items-center gap-3 text-[11px] text-slate-400 flex-wrap">
                            <span x-show="card.kind === 'lead'"><i class="fas fa-phone text-[9px]"></i> <span x-text="card.phone"></span></span>
                            <span x-show="card.due_date_human" :class="card.is_overdue ? 'text-red-500 font-semibold' : ''"><i class="far fa-calendar"></i> <span x-text="card.due_date_human"></span></span>
                            <span><i class="far fa-user"></i> <span x-text="card.user_name"></span></span>
                            <span x-show="card.comments_count > 0"><i class="far fa-comment"></i> <span x-text="card.comments_count"></span></span>
                        </div>
                    </div>
                    <span :class="statusBadge(card.status)" class="rounded-full px-2.5 py-0.5 text-[11px] font-medium" x-text="statusLabel(card.status)"></span>
                </div>
            </template>
        </div>
    </div>

    {{-- Drawer --}}
    <div x-show="drawer" x-transition.opacity class="fixed inset-0 bg-black/40 z-40" @click="closeDrawer()" style="display: none;"></div>
    <div x-show="drawer" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 w-full max-w-2xl bg-white shadow-2xl z-50 overflow-y-auto" style="display: none;">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between z-10">
            <div class="flex items-center gap-2">
                <span x-show="active && active.kind === 'lead'" class="inline-flex items-center gap-1 rounded-full bg-cyan-100 text-cyan-700 px-2 py-0.5 text-[10px] font-semibold">
                    <i class="fas fa-user-plus text-[9px]"></i> LID
                </span>
                <span x-show="active && active.kind === 'task'" class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-700 px-2 py-0.5 text-[10px] font-semibold">
                    <i class="fas fa-clipboard-list text-[9px]"></i> TASK
                </span>
                <h3 class="text-lg font-semibold text-slate-900" x-text="drawerTitle"></h3>
            </div>
            <button @click="closeDrawer()" class="text-slate-400 hover:text-slate-700 p-1"><i class="fas fa-xmark"></i></button>
        </div>

        {{-- TASK FORM --}}
        <div x-show="active && active.kind === 'task'" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sarlavha *</label>
                <input type="text" x-model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Tafsilot</label>
                <textarea x-model="form.description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bajaruvchi</label>
                    @if($isManager)
                        <select x-model="form.user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <div class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ auth()->user()->name }}</div>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Muddat</label>
                    <input type="date" x-model="form.due_date" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Muhimlik</label>
                    <select x-model="form.priority" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($priorityList as $p)<option value="{{ $p['key'] }}">{{ $p['label'] }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select x-model="form.status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($statusList as $s)<option value="{{ $s['key'] }}">{{ $s['label'] }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Bog'liq lid</label>
                <select x-model="form.lead_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="">— Yo'q —</option>
                    @foreach($allLeads as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach
                </select>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button x-show="drawerMode === 'edit'" @click="deleteFromDrawer()" class="text-sm text-red-600 hover:text-red-700"><i class="fas fa-trash text-xs mr-1"></i> O'chirish</button>
                <div class="flex gap-2 ml-auto">
                    <button @click="closeDrawer()" class="rounded-xl px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Bekor</button>
                    <button @click="saveTask()" :disabled="saving" class="rounded-xl bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white px-4 py-2 text-sm font-medium">
                        <span x-show="!saving">Saqlash</span>
                        <span x-show="saving"><i class="fas fa-spinner fa-spin mr-1"></i></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- LEAD VIEW --}}
        <div x-show="active && active.kind === 'lead'" class="p-5 space-y-4">
            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-[11px] text-slate-500">Telefon</dt><dd class="text-slate-900 font-medium" x-text="active && active.phone"></dd></div>
                    <div><dt class="text-[11px] text-slate-500">Status</dt><dd class="text-slate-900" x-text="active && active.lead_status_label"></dd></div>
                    <div><dt class="text-[11px] text-slate-500">Byudjet</dt><dd class="text-slate-900" x-text="active && active.budget ? (active.budget).toLocaleString('ru-RU').replace(/,/g,' ') + ' UZS' : '—'"></dd></div>
                    <div><dt class="text-[11px] text-slate-500">To'lov</dt><dd class="text-slate-900" x-text="active && active.payment_label || '—'"></dd></div>
                    <div><dt class="text-[11px] text-slate-500">Xonalar</dt><dd class="text-slate-900" x-text="active && active.rooms_wanted ? active.rooms_wanted + ' xona' : '—'"></dd></div>
                    <div><dt class="text-[11px] text-slate-500">Hudud</dt><dd class="text-slate-900" x-text="active && active.preferred_district || '—'"></dd></div>
                </div>
                <div class="mt-3 flex gap-2">
                    <a x-show="active && active.phone_clean" :href="active ? 'tel:' + active.phone_clean : '#'" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 text-sm font-medium"><i class="fas fa-phone text-xs"></i> Qo'ng'iroq</a>
                    <a :href="active ? '/leads/' + active.id : '#'" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 px-3 py-2 text-sm font-medium"><i class="fas fa-arrow-right text-xs"></i> To'liq sahifa</a>
                </div>
            </div>
        </div>

        {{-- COMMENTS --}}
        <div x-show="active" class="p-5 pt-2 border-t border-slate-100">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-comments text-slate-500 text-sm"></i>
                <h4 class="text-sm font-semibold text-slate-900">Kommentlar</h4>
                <span x-show="active" class="text-xs text-slate-400" x-text="'(' + (active && active.comments ? active.comments.length : 0) + ')'"></span>
            </div>
            <div class="rounded-xl border border-slate-200 p-2 mb-3">
                <textarea x-model="newComment" rows="2" placeholder="Komment yozish..." class="w-full bg-transparent text-sm focus:outline-none resize-none"></textarea>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[10px] text-slate-400">{{ auth()->user()->name }}</span>
                    <button @click="addComment()" :disabled="!newComment.trim() || addingComment" class="rounded-lg bg-slate-900 hover:bg-slate-800 disabled:opacity-30 text-white px-3 py-1.5 text-xs font-medium">
                        <span x-show="!addingComment">Qo'shish</span>
                        <span x-show="addingComment"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </div>
            <div class="space-y-2">
                <template x-for="c in (active && active.comments || [])" :key="c.id">
                    <div class="rounded-xl bg-slate-50 p-3 group">
                        <div class="flex items-start gap-2">
                            <div class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex-shrink-0" x-text="(c.user_name || '?').charAt(0).toUpperCase()"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-medium text-slate-900" x-text="c.user_name"></p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-slate-400" x-text="c.created_human"></span>
                                        <button x-show="c.user_id === {{ auth()->id() }} || {{ $isManager ? 'true' : 'false' }}" @click="deleteComment(c)" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition"><i class="fas fa-trash text-[10px]"></i></button>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-700 mt-1 whitespace-pre-wrap" x-text="c.content"></p>
                            </div>
                        </div>
                    </div>
                </template>
                <p x-show="!active || !active.comments || active.comments.length === 0" class="text-center text-xs text-slate-400 py-3">Komment yo'q</p>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast.show" x-transition class="fixed bottom-6 right-6 rounded-xl shadow-lg px-4 py-3 text-sm z-50" :class="toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-slate-900 text-white'" style="display: none;">
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function workspace() {
    return {
        cards: [
            ...@json($taskCards),
            ...@json($leadCards),
        ],
        view: localStorage.getItem('ws_view') || 'board',
        kindFilter: 'all',
        search: '',
        userFilter: '',
        dateFilter: 'all',
        dateFrom: '',
        dateTo: '',
        drawer: false,
        drawerMode: 'create',
        active: null,
        form: {},
        emptyForm: {
            id: null, title: '', description: '', user_id: {{ auth()->id() }},
            lead_id: '', due_date: new Date().toISOString().slice(0,10),
            priority: 'normal', status: 'pending',
        },
        saving: false,
        quickTitle: '',
        quickPriority: 'normal',
        newComment: '',
        addingComment: false,
        toast: { show: false, message: '', type: 'success' },
        draggedCard: null,
        columns: [
            { key: 'pending', label: 'Kutilmoqda', bg: 'bg-slate-50/80 border-slate-200', border: 'border-slate-200', dot: 'bg-slate-400' },
            { key: 'in_progress', label: 'Bajarilmoqda', bg: 'bg-cyan-50/40 border-cyan-200/60', border: 'border-cyan-200/60', dot: 'bg-cyan-500' },
            { key: 'done', label: 'Bajarildi', bg: 'bg-emerald-50/40 border-emerald-200/60', border: 'border-emerald-200/60', dot: 'bg-emerald-500' },
            { key: 'cancelled', label: 'Bekor qilindi', bg: 'bg-red-50/40 border-red-200/40', border: 'border-red-200/40', dot: 'bg-red-300' },
        ],
        statusLabels: { pending: 'Kutilmoqda', in_progress: 'Bajarilmoqda', done: 'Bajarildi', cancelled: 'Bekor qilindi' },

        init() {
            this.$watch('view', v => localStorage.setItem('ws_view', v));
        },

        get drawerTitle() {
            if (!this.active) return 'Yangi task';
            return this.drawerMode === 'create' ? 'Yangi task' : this.active.title;
        },

        get baseFiltered() {
            let list = this.cards;
            if (this.kindFilter === 'task') list = list.filter(c => c.kind === 'task');
            else if (this.kindFilter === 'lead') list = list.filter(c => c.kind === 'lead');
            if (this.userFilter) list = list.filter(c => c.user_id == this.userFilter);
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                list = list.filter(c => (c.title||'').toLowerCase().includes(q) || (c.description||'').toLowerCase().includes(q) || (c.user_name||'').toLowerCase().includes(q) || (c.phone||'').includes(q));
            }
            list = list.filter(c => this.matchesDate(c));
            return list;
        },

        get filteredCards() { return this.baseFiltered; },

        get cardsByStatus() {
            const groups = { pending: [], in_progress: [], done: [], cancelled: [] };
            this.baseFiltered.forEach(c => { (groups[c.status] || groups.pending).push(c); });
            // sort: priority desc, due_date asc
            const prioMap = { urgent: 1, high: 2, normal: 3, low: 4 };
            for (const k of Object.keys(groups)) {
                groups[k].sort((a, b) => {
                    const p = (prioMap[a.priority] || 5) - (prioMap[b.priority] || 5);
                    if (p !== 0) return p;
                    return (a.due_date || '9999') < (b.due_date || '9999') ? -1 : 1;
                });
            }
            return groups;
        },

        matchesDate(card) {
            if (this.dateFilter === 'all') return true;
            const d = card.due_date;
            const today = new Date(); today.setHours(0,0,0,0);
            const t = today.toISOString().slice(0,10);
            const tomorrow = new Date(today); tomorrow.setDate(tomorrow.getDate()+1);
            const tom = tomorrow.toISOString().slice(0,10);
            const weekEnd = new Date(today); weekEnd.setDate(weekEnd.getDate()+7);
            const monthEnd = new Date(today); monthEnd.setMonth(monthEnd.getMonth()+1);
            if (this.dateFilter === 'overdue') return d && d < t && card.status !== 'done' && card.status !== 'cancelled';
            if (this.dateFilter === 'today') return d === t;
            if (this.dateFilter === 'tomorrow') return d === tom;
            if (this.dateFilter === 'week') return d && d >= t && d < weekEnd.toISOString().slice(0,10);
            if (this.dateFilter === 'month') return d && d >= t && d < monthEnd.toISOString().slice(0,10);
            if (this.dateFilter === 'custom') {
                if (this.dateFrom && d < this.dateFrom) return false;
                if (this.dateTo && d > this.dateTo) return false;
                return !!d;
            }
            return true;
        },

        setDate(f) {
            this.dateFilter = f;
            if (f !== 'custom') { this.dateFrom = ''; this.dateTo = ''; }
        },

        statusBadge(s) { return ({ pending: 'bg-slate-100 text-slate-700', in_progress: 'bg-cyan-100 text-cyan-700', done: 'bg-emerald-100 text-emerald-700', cancelled: 'bg-red-100 text-red-700' })[s] || 'bg-slate-100'; },
        statusLabel(s) { return this.statusLabels[s] || s; },
        priorityChip(c) { return ({ red: 'bg-red-50 text-red-700', amber: 'bg-amber-50 text-amber-700', blue: 'bg-blue-50 text-blue-700', slate: 'bg-slate-100 text-slate-600' })[c] || 'bg-slate-100'; },
        priorityBorder(c) { return ({ red: 'border-l-red-500', amber: 'border-l-amber-500', blue: 'border-l-blue-400', slate: 'border-l-slate-300' })[c] || 'border-l-slate-300'; },
        priorityBar(c) { return ({ red: 'bg-red-500', amber: 'bg-amber-500', blue: 'bg-blue-400', slate: 'bg-slate-300' })[c] || 'bg-slate-300'; },

        // Drag & drop
        onDragStart(e, card) {
            this.draggedCard = card;
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        },
        onDragEnd(e) {
            e.target.classList.remove('dragging');
            document.querySelectorAll('.drop-zone.drag-over').forEach(el => el.classList.remove('drag-over'));
        },
        async onDrop(e, newStatus) {
            e.currentTarget.classList.remove('drag-over');
            if (!this.draggedCard || this.draggedCard.status === newStatus) { this.draggedCard = null; return; }
            const c = this.draggedCard;
            const oldStatus = c.status;
            c.status = newStatus;
            this.draggedCard = null;
            try {
                const url = c.kind === 'task' ? `/tasks/${c.id}/status` : `/leads/${c.id}/task-status`;
                const body = c.kind === 'task' ? { status: newStatus } : { task_status: newStatus };
                const data = await this.req(url, 'PATCH', body);
                if (data.task) this.replaceCard(data.task);
                if (data.lead) this.replaceCard(data.lead);
                this.flash('Status o\'zgartirildi');
            } catch (err) {
                c.status = oldStatus;
                this.flash('Xato', 'error');
            }
        },

        openCard(card) {
            this.active = card;
            this.drawerMode = 'edit';
            if (card.kind === 'task') {
                this.form = {
                    id: card.id, title: card.title, description: card.description || '',
                    user_id: card.user_id, lead_id: card.lead_id || '',
                    due_date: card.due_date || '', priority: card.priority, status: card.status,
                };
            }
            this.newComment = '';
            this.drawer = true;
        },

        openCreate() {
            this.active = { kind: 'task', comments: [] };
            this.form = { ...this.emptyForm };
            this.drawerMode = 'create';
            this.newComment = '';
            this.drawer = true;
        },

        closeDrawer() { this.drawer = false; this.active = null; },

        quickAddTo(status) {
            const title = prompt('Task sarlavhasi:');
            if (!title?.trim()) return;
            this.req('/tasks', 'POST', {
                title: title.trim(), user_id: {{ auth()->id() }},
                priority: 'normal', status: status,
                due_date: new Date().toISOString().slice(0, 10),
            }).then(data => {
                if (data.task) {
                    this.cards.unshift(data.task);
                    this.flash('Qo\'shildi');
                }
            }).catch(() => this.flash('Xato', 'error'));
        },

        async saveTask() {
            if (!this.form.title.trim()) { this.flash('Sarlavhani kiriting', 'error'); return; }
            this.saving = true;
            try {
                const url = this.drawerMode === 'create' ? '/tasks' : `/tasks/${this.form.id}`;
                const method = this.drawerMode === 'create' ? 'POST' : 'PUT';
                const data = await this.req(url, method, this.form);
                if (data.task) {
                    if (this.drawerMode === 'create') this.cards.unshift(data.task);
                    else this.replaceCard(data.task);
                    this.active = data.task;
                    this.flash('Saqlandi');
                    if (this.drawerMode === 'create') this.closeDrawer();
                }
            } catch (e) { this.flash('Xato: ' + e.message, 'error'); }
            this.saving = false;
        },

        async deleteFromDrawer() {
            if (!confirm('O\'chirilsinmi?')) return;
            await this.req(`/tasks/${this.active.id}`, 'DELETE');
            this.cards = this.cards.filter(c => !(c.kind === 'task' && c.id === this.active.id));
            this.closeDrawer();
            this.flash('O\'chirildi');
        },

        async deleteCard(card) {
            if (card.kind !== 'task') return;
            if (!confirm('O\'chirilsinmi?')) return;
            try {
                await this.req(`/tasks/${card.id}`, 'DELETE');
                this.cards = this.cards.filter(c => !(c.kind === 'task' && c.id === card.id));
                this.flash('O\'chirildi');
            } catch (e) { this.flash('Xato', 'error'); }
        },

        async quickAdd() {
            if (!this.quickTitle.trim()) return;
            try {
                const data = await this.req('/tasks', 'POST', {
                    title: this.quickTitle.trim(), user_id: {{ auth()->id() }},
                    priority: this.quickPriority, status: 'pending',
                    due_date: new Date().toISOString().slice(0, 10),
                });
                if (data.task) { this.cards.unshift(data.task); this.quickTitle = ''; this.flash('Qo\'shildi'); }
            } catch (e) { this.flash('Xato', 'error'); }
        },

        async addComment() {
            if (!this.newComment.trim() || !this.active) return;
            this.addingComment = true;
            try {
                const data = await this.req('/comments', 'POST', {
                    type: this.active.kind, id: this.active.id, content: this.newComment.trim(),
                });
                if (data.comment) {
                    this.active.comments = [data.comment, ...(this.active.comments || [])];
                    this.active.comments_count = this.active.comments.length;
                    // sync into cards array
                    const idx = this.cards.findIndex(c => c.kind === this.active.kind && c.id === this.active.id);
                    if (idx > -1) this.cards.splice(idx, 1, { ...this.active });
                    this.newComment = '';
                }
            } catch (e) { this.flash('Xato', 'error'); }
            this.addingComment = false;
        },

        async deleteComment(c) {
            if (!confirm('Komment o\'chirilsinmi?')) return;
            try {
                await this.req(`/comments/${c.id}`, 'DELETE');
                this.active.comments = this.active.comments.filter(x => x.id !== c.id);
                this.active.comments_count = this.active.comments.length;
            } catch (e) { this.flash('Xato', 'error'); }
        },

        replaceCard(updated) {
            const idx = this.cards.findIndex(c => c.kind === updated.kind && c.id === updated.id);
            if (idx > -1) this.cards.splice(idx, 1, updated);
            if (this.active && this.active.kind === updated.kind && this.active.id === updated.id) this.active = updated;
        },

        flash(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 2200);
        },

        async req(url, method, body = null) {
            const opts = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            };
            if (body) opts.body = JSON.stringify(body);
            const res = await fetch(url, opts);
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                throw new Error(err.message || `HTTP ${res.status}`);
            }
            return res.json();
        },
    }
}
</script>
@endsection
