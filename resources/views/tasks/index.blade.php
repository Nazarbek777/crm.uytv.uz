@extends('layouts.main')

@section('title', 'Tasklar')

@section('content')
@php
    $isManager = auth()->user()->isManager();
    $initialTasks = $tasks->map(fn($t) => [
        'id' => $t->id, 'title' => $t->title, 'description' => $t->description,
        'user_id' => $t->user_id, 'user_name' => $t->user?->name,
        'assigner_name' => $t->assigner?->name, 'assigned_by' => $t->assigned_by,
        'lead_id' => $t->lead_id, 'lead_name' => $t->lead?->name,
        'client_id' => $t->client_id, 'client_name' => $t->client?->name,
        'due_date' => $t->due_date?->format('Y-m-d'),
        'due_date_human' => $t->due_date?->format('d.m.Y'),
        'priority' => $t->priority, 'priority_label' => $t->priorityLabel(), 'priority_color' => $t->priorityColor(),
        'status' => $t->status, 'status_label' => $t->statusLabel(), 'status_color' => $t->statusColor(),
        'is_overdue' => $t->isOverdue(),
        'created_at' => $t->created_at->toIso8601String(),
    ]);
    $initialAuto = $autoTasks->map(fn($l) => [
        'id' => $l->id, 'name' => $l->name, 'phone' => $l->phone,
        'phone_clean' => preg_replace('/\s+/', '', $l->phone ?? ''),
        'status_label' => $l->statusLabel(),
        'budget' => (float) $l->budget,
        'rooms_wanted' => $l->rooms_wanted,
        'payment_label' => \App\Models\Lead::PAYMENT_METHODS[$l->payment_method] ?? null,
    ]);
    $statusList = collect(\App\Models\Task::STATUSES)->map(fn($v, $k) => array_merge($v, ['key' => $k]))->values();
    $priorityList = collect(\App\Models\Task::PRIORITIES)->map(fn($v, $k) => array_merge($v, ['key' => $k]))->values();
@endphp

<div x-data="taskBoard()" x-init="init()" class="relative">
    {{-- Top toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
        <div class="flex flex-1 gap-1.5 bg-white border border-slate-200 rounded-xl p-1 text-sm">
            <button @click="filter = 'active'" :class="filter === 'active' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition flex items-center gap-1.5">
                <span>Faol</span>
                <span class="text-[10px] rounded-full px-1.5" :class="filter === 'active' ? 'bg-white/20' : 'bg-slate-100'" x-text="counts.active"></span>
            </button>
            <button @click="filter = 'today'" :class="filter === 'today' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition flex items-center gap-1.5">
                <span>Bugun</span>
                <span class="text-[10px] rounded-full px-1.5" :class="filter === 'today' ? 'bg-white/20' : 'bg-slate-100'" x-text="counts.today"></span>
            </button>
            <button @click="filter = 'overdue'" :class="filter === 'overdue' ? 'bg-red-600 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition flex items-center gap-1.5">
                <span>Kechikkan</span>
                <span class="text-[10px] rounded-full px-1.5" :class="filter === 'overdue' ? 'bg-white/20' : 'bg-red-100 text-red-700'" x-text="counts.overdue"></span>
            </button>
            <button @click="filter = 'done'" :class="filter === 'done' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition flex items-center gap-1.5">
                <span>Bajarilgan</span>
                <span class="text-[10px] rounded-full px-1.5" :class="filter === 'done' ? 'bg-white/20' : 'bg-slate-100'" x-text="counts.done"></span>
            </button>
            <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 font-medium transition">Hammasi</button>
        </div>

        <div class="flex items-center gap-2">
            <input type="search" x-model="search" placeholder="Qidirish..." class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none w-full sm:w-48">
            @if($isManager)
                <select x-model="userFilter" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                    <option value="">Hamma operator</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            @endif
            <button @click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 text-sm font-medium transition whitespace-nowrap">
                <i class="fas fa-plus text-xs"></i> Yangi task
            </button>
        </div>
    </div>

    {{-- Quick add bar --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-3 mb-4 flex items-center gap-2">
        <i class="fas fa-plus text-slate-400 text-sm pl-2"></i>
        <input type="text" x-model="quickTitle" @keydown.enter="quickAdd()" placeholder="Tezkor task qo'shish — yozing va Enter bosing..." class="flex-1 bg-transparent text-sm focus:outline-none placeholder:text-slate-400">
        <select x-model="quickPriority" class="text-xs rounded-lg border border-slate-200 px-2 py-1 bg-white">
            <option value="low">Past</option>
            <option value="normal">Oddiy</option>
            <option value="high">Yuqori</option>
            <option value="urgent">Shoshilinch</option>
        </select>
        <button @click="quickAdd()" :disabled="!quickTitle.trim()" class="rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-medium transition disabled:opacity-30 disabled:cursor-not-allowed">Qo'shish</button>
    </div>

    {{-- Auto: today's calls --}}
    <div x-show="filteredAutoTasks.length > 0" class="rounded-2xl bg-gradient-to-br from-cyan-50 to-blue-50 border border-cyan-200 shadow-sm overflow-hidden mb-4">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-cyan-200/60">
            <div class="flex items-center gap-2">
                <i class="fas fa-phone-volume text-cyan-700"></i>
                <h3 class="text-base font-semibold text-cyan-900">Bugun aloqa kerak — <span x-text="filteredAutoTasks.length"></span> ta lid</h3>
            </div>
            <span class="text-[11px] text-cyan-700 bg-cyan-100 rounded-full px-2 py-0.5">Avtomatik</span>
        </div>
        <div class="divide-y divide-cyan-100">
            <template x-for="lead in filteredAutoTasks" :key="lead.id">
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white text-cyan-700 shadow-sm">
                        <i class="fas fa-phone text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate" x-text="lead.name + ' — ' + lead.phone"></p>
                        <p class="text-xs text-slate-600 mt-0.5">
                            <span x-text="lead.status_label"></span>
                            <template x-if="lead.budget"><span> · <span x-text="(lead.budget / 1000000).toFixed(1) + 'M'"></span></span></template>
                            <template x-if="lead.rooms_wanted"><span> · <span x-text="lead.rooms_wanted + ' xona'"></span></span></template>
                            <template x-if="lead.payment_label"><span> · <span x-text="lead.payment_label"></span></span></template>
                        </p>
                    </div>
                    <div class="flex items-center gap-1">
                        <a :href="'tel:' + lead.phone_clean" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white" title="Qo'ng'iroq">
                            <i class="fas fa-phone text-sm"></i>
                        </a>
                        <a :href="'/leads/' + lead.id" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200" title="Lid">
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Tasks list --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <template x-if="filteredTasks.length === 0">
            <div class="px-5 py-16 text-center">
                <i class="fas fa-clipboard-check text-4xl text-slate-300 mb-3 block"></i>
                <p class="text-sm text-slate-500">Tasklar yo'q</p>
            </div>
        </template>
        <div class="divide-y divide-slate-100">
            <template x-for="task in filteredTasks" :key="task.id">
                <div :class="`border-l-4 ${priorityBorder(task.priority_color)} ${task.status === 'done' || task.status === 'cancelled' ? 'opacity-60' : ''}`" class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50/40 transition group">
                    {{-- Status checkbox --}}
                    <button @click="toggleDone(task)" :class="task.status === 'done' ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-slate-500'" class="mt-1 inline-flex items-center justify-center w-5 h-5 rounded-full border-2 flex-shrink-0">
                        <i x-show="task.status === 'done'" class="fas fa-check text-[10px]"></i>
                    </button>

                    {{-- Title + meta --}}
                    <div class="flex-1 min-w-0 cursor-pointer" @click="openEdit(task)">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p :class="task.status === 'done' ? 'text-slate-400 line-through' : 'text-slate-900'" class="text-sm font-medium" x-text="task.title"></p>
                            <span x-show="task.is_overdue && task.status !== 'done'" class="inline-flex rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-[10px] font-semibold">Kechikkan</span>
                        </div>
                        <p x-show="task.description" class="text-xs text-slate-500 mt-0.5 line-clamp-1" x-text="task.description"></p>
                        <div class="mt-1 flex items-center gap-3 text-[11px] text-slate-400 flex-wrap">
                            <span x-show="task.due_date_human" :class="task.is_overdue && task.status !== 'done' ? 'text-red-500 font-semibold' : ''">
                                <i class="far fa-calendar"></i> <span x-text="task.due_date_human"></span>
                            </span>
                            <span><i class="far fa-user"></i> <span x-text="task.user_name"></span></span>
                            <span x-show="task.assigner_name && task.assigned_by != task.user_id"><i class="fas fa-arrow-right-from-bracket"></i> <span x-text="task.assigner_name"></span></span>
                            <a x-show="task.lead_id" :href="'/leads/' + task.lead_id" @click.stop class="hover:text-cyan-600"><i class="fas fa-user-plus"></i> <span x-text="task.lead_name"></span></a>
                        </div>
                    </div>

                    {{-- Inline status --}}
                    <div class="relative" @click.outside="task._statusOpen = false">
                        <button @click="task._statusOpen = !task._statusOpen" :class="statusBadge(task.status_color)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-medium hover:ring-2 hover:ring-slate-200 transition">
                            <span x-text="task.status_label"></span>
                            <i class="fas fa-chevron-down text-[8px] opacity-60"></i>
                        </button>
                        <div x-show="task._statusOpen" x-transition class="absolute right-0 top-full mt-1 w-36 rounded-xl bg-white border border-slate-200 shadow-lg z-20 overflow-hidden text-sm" style="display: none;">
                            @foreach($statusList as $s)
                                <button @click="changeStatus(task, '{{ $s['key'] }}')" class="block w-full text-left px-3 py-1.5 hover:bg-slate-50">{{ $s['label'] }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Inline priority --}}
                    <div class="relative" @click.outside="task._prioOpen = false">
                        <button @click="task._prioOpen = !task._prioOpen" :class="priorityBadge(task.priority_color)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-medium hover:ring-2 hover:ring-slate-200 transition">
                            <span x-text="task.priority_label"></span>
                            <i class="fas fa-chevron-down text-[8px] opacity-60"></i>
                        </button>
                        <div x-show="task._prioOpen" x-transition class="absolute right-0 top-full mt-1 w-32 rounded-xl bg-white border border-slate-200 shadow-lg z-20 overflow-hidden text-sm" style="display: none;">
                            @foreach($priorityList as $p)
                                <button @click="changePriority(task, '{{ $p['key'] }}')" class="block w-full text-left px-3 py-1.5 hover:bg-slate-50">{{ $p['label'] }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center opacity-0 group-hover:opacity-100 transition">
                        <button @click="openEdit(task)" class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100"><i class="fas fa-pen text-xs"></i></button>
                        <button @click="deleteTask(task)" class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Right drawer --}}
    <div x-show="drawer" x-transition.opacity class="fixed inset-0 bg-black/40 z-40" @click="closeDrawer()" style="display: none;"></div>
    <div x-show="drawer" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl z-50 overflow-y-auto" style="display: none;">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between z-10">
            <h3 class="text-lg font-semibold text-slate-900" x-text="drawerMode === 'create' ? 'Yangi task' : 'Tahrirlash'"></h3>
            <button @click="closeDrawer()" class="text-slate-400 hover:text-slate-700 p-1"><i class="fas fa-xmark"></i></button>
        </div>

        <div class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sarlavha *</label>
                <input type="text" x-model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Mas: Mijozga qo'ng'iroq qilish">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Tafsilot</label>
                <textarea x-model="form.description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bajaruvchi *</label>
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
                    <input type="date" x-model="form.due_date" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Muhimlik</label>
                    <select x-model="form.priority" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($priorityList as $p)
                            <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select x-model="form.status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($statusList as $s)
                            <option value="{{ $s['key'] }}">{{ $s['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bog'liq lid</label>
                    <select x-model="form.lead_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Yo'q —</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bog'liq mijoz</label>
                    <select x-model="form.client_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Yo'q —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button x-show="drawerMode === 'edit' && form.id" @click="deleteFromDrawer()" class="text-sm text-red-600 hover:text-red-700"><i class="fas fa-trash text-xs mr-1"></i> O'chirish</button>
                <div class="flex gap-2 ml-auto">
                    <button @click="closeDrawer()" class="rounded-xl px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Bekor</button>
                    <button @click="saveTask()" :disabled="saving" class="rounded-xl bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white px-4 py-2 text-sm font-medium">
                        <span x-show="!saving">Saqlash</span>
                        <span x-show="saving"><i class="fas fa-spinner fa-spin mr-1"></i> Saqlanmoqda</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast.show" x-transition class="fixed bottom-6 right-6 rounded-xl shadow-lg px-4 py-3 text-sm z-50" :class="toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-slate-900 text-white'" style="display: none;">
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function taskBoard() {
    return {
        tasks: @json($initialTasks),
        autoTasks: @json($initialAuto),
        filter: 'active',
        search: '',
        userFilter: '',
        drawer: false,
        drawerMode: 'create',
        saving: false,
        quickTitle: '',
        quickPriority: 'normal',
        toast: { show: false, message: '', type: 'success' },
        emptyForm: {
            id: null, title: '', description: '', user_id: {{ auth()->id() }},
            lead_id: '', client_id: '', due_date: new Date().toISOString().slice(0,10),
            priority: 'normal', status: 'pending',
        },
        form: {},

        init() {
            this.tasks = this.tasks.map(t => ({ ...t, _statusOpen: false, _prioOpen: false }));
        },

        get filteredTasks() {
            let list = this.tasks;
            if (this.userFilter) list = list.filter(t => t.user_id == this.userFilter);
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                list = list.filter(t => (t.title || '').toLowerCase().includes(q) || (t.description || '').toLowerCase().includes(q) || (t.user_name || '').toLowerCase().includes(q));
            }
            const today = new Date().toISOString().slice(0, 10);
            if (this.filter === 'active') list = list.filter(t => t.status === 'pending' || t.status === 'in_progress');
            else if (this.filter === 'today') list = list.filter(t => (t.status === 'pending' || t.status === 'in_progress') && t.due_date === today);
            else if (this.filter === 'overdue') list = list.filter(t => (t.status === 'pending' || t.status === 'in_progress') && t.due_date && t.due_date < today);
            else if (this.filter === 'done') list = list.filter(t => t.status === 'done');
            return list;
        },

        get filteredAutoTasks() {
            if (this.filter !== 'active' && this.filter !== 'today') return [];
            return this.autoTasks;
        },

        get counts() {
            const today = new Date().toISOString().slice(0, 10);
            const list = this.userFilter ? this.tasks.filter(t => t.user_id == this.userFilter) : this.tasks;
            return {
                active: list.filter(t => t.status === 'pending' || t.status === 'in_progress').length,
                today: list.filter(t => (t.status === 'pending' || t.status === 'in_progress') && t.due_date === today).length,
                overdue: list.filter(t => (t.status === 'pending' || t.status === 'in_progress') && t.due_date && t.due_date < today).length,
                done: list.filter(t => t.status === 'done').length,
            };
        },

        statusBadge(c) {
            return ({
                slate: 'bg-slate-100 text-slate-700', cyan: 'bg-cyan-100 text-cyan-700',
                emerald: 'bg-emerald-100 text-emerald-700', red: 'bg-red-100 text-red-700',
            })[c] || 'bg-slate-100 text-slate-700';
        },
        priorityBadge(c) {
            return ({
                red: 'bg-red-50 text-red-700 border border-red-200', amber: 'bg-amber-50 text-amber-700 border border-amber-200',
                blue: 'bg-blue-50 text-blue-700 border border-blue-200', slate: 'bg-slate-100 text-slate-600 border border-slate-200',
            })[c] || 'bg-slate-100';
        },
        priorityBorder(c) {
            return ({ red: 'border-l-red-500', amber: 'border-l-amber-500', blue: 'border-l-blue-400', slate: 'border-l-slate-300' })[c] || 'border-l-slate-300';
        },

        openCreate() {
            this.form = { ...this.emptyForm };
            this.drawerMode = 'create';
            this.drawer = true;
        },

        openEdit(task) {
            this.form = {
                id: task.id, title: task.title, description: task.description || '',
                user_id: task.user_id, lead_id: task.lead_id || '', client_id: task.client_id || '',
                due_date: task.due_date || '', priority: task.priority, status: task.status,
            };
            this.drawerMode = 'edit';
            this.drawer = true;
        },

        closeDrawer() { this.drawer = false; },

        async saveTask() {
            if (!this.form.title.trim()) { this.flash('Sarlavhani kiriting', 'error'); return; }
            this.saving = true;
            try {
                const url = this.drawerMode === 'create' ? '/tasks' : `/tasks/${this.form.id}`;
                const method = this.drawerMode === 'create' ? 'POST' : 'PUT';
                const data = await this.req(url, method, this.form);
                if (data.task) {
                    if (this.drawerMode === 'create') {
                        this.tasks.unshift({ ...data.task, _statusOpen: false, _prioOpen: false });
                    } else {
                        this.replaceTask(data.task);
                    }
                    this.closeDrawer();
                    this.flash('Saqlandi');
                }
            } catch (e) { this.flash('Xato: ' + e.message, 'error'); }
            this.saving = false;
        },

        async deleteFromDrawer() {
            if (!confirm('O\'chirilsinmi?')) return;
            await this.req(`/tasks/${this.form.id}`, 'DELETE');
            this.tasks = this.tasks.filter(t => t.id !== this.form.id);
            this.closeDrawer();
            this.flash('O\'chirildi');
        },

        async deleteTask(task) {
            if (!confirm('Taskni o\'chirilsinmi?')) return;
            try {
                await this.req(`/tasks/${task.id}`, 'DELETE');
                this.tasks = this.tasks.filter(t => t.id !== task.id);
                this.flash('O\'chirildi');
            } catch (e) { this.flash('Xato: ' + e.message, 'error'); }
        },

        async toggleDone(task) {
            const newStatus = task.status === 'done' ? 'pending' : 'done';
            await this.changeStatus(task, newStatus);
        },

        async changeStatus(task, status) {
            task._statusOpen = false;
            try {
                const data = await this.req(`/tasks/${task.id}/status`, 'PATCH', { status });
                if (data.task) this.replaceTask(data.task);
            } catch (e) { this.flash('Xato', 'error'); }
        },

        async changePriority(task, priority) {
            task._prioOpen = false;
            try {
                const data = await this.req(`/tasks/${task.id}/priority`, 'PATCH', { priority });
                if (data.task) this.replaceTask(data.task);
            } catch (e) { this.flash('Xato', 'error'); }
        },

        async quickAdd() {
            if (!this.quickTitle.trim()) return;
            try {
                const data = await this.req('/tasks', 'POST', {
                    title: this.quickTitle.trim(),
                    user_id: {{ auth()->id() }},
                    priority: this.quickPriority,
                    status: 'pending',
                    due_date: new Date().toISOString().slice(0, 10),
                });
                if (data.task) {
                    this.tasks.unshift({ ...data.task, _statusOpen: false, _prioOpen: false });
                    this.quickTitle = '';
                    this.flash('Task qo\'shildi');
                }
            } catch (e) { this.flash('Xato: ' + e.message, 'error'); }
        },

        replaceTask(updated) {
            const i = this.tasks.findIndex(t => t.id === updated.id);
            if (i > -1) this.tasks.splice(i, 1, { ...updated, _statusOpen: false, _prioOpen: false });
        },

        flash(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 2500);
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
