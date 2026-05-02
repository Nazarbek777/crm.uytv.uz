@php
    $task = $task ?? null;
    $val = fn($key, $default = '') => old($key, $task->{$key} ?? $default);
    $isManager = auth()->user()->isManager();
@endphp

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Tasklar
        </a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" class="space-y-4">
        @csrf
        @if($method === 'PUT') @method('PUT') @endif

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Sarlavha <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ $val('title') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Mas: Mijozga qo'ng'iroq qilish">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Tafsilot</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ $val('description') }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bajaruvchi <span class="text-red-500">*</span></label>
                    @if($isManager)
                        <select name="user_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected($val('user_id', auth()->id()) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-sm text-slate-700">{{ auth()->user()->name }} (siz)</div>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Muddat</label>
                    <input type="date" name="due_date" value="{{ $val('due_date') instanceof \Carbon\Carbon ? $val('due_date')->format('Y-m-d') : $val('due_date', now()->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Muhimlik</label>
                    <select name="priority" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        @foreach(\App\Models\Task::PRIORITIES as $key => $info)
                            <option value="{{ $key }}" @selected($val('priority', 'normal') === $key)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        @foreach(\App\Models\Task::STATUSES as $key => $info)
                            <option value="{{ $key }}" @selected($val('status', 'pending') === $key)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bog'liq lid</label>
                    <select name="lead_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Yo'q —</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}" @selected(old('lead_id', $task->lead_id ?? request('lead_id')) == $l->id)>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Bog'liq mijoz</label>
                    <select name="client_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Yo'q —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected($val('client_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('tasks.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Bekor</a>
            <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-sm font-medium text-white transition">
                <i class="fas fa-check mr-1"></i> {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>
