@php
    $reminder = $reminder ?? null;
    $val = fn($key, $default = '') => old($key, $reminder->{$key} ?? $default);
    $remindAt = $val('remind_at') instanceof \Carbon\Carbon ? $val('remind_at')->format('Y-m-d\TH:i') : $val('remind_at', now()->addHour()->format('Y-m-d\TH:i'));
@endphp

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('reminders.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Eslatmalar
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
                <input type="text" name="title" value="{{ $val('title') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Vaqt <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="remind_at" value="{{ $remindAt }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Operator</label>
                    <select name="user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Mening eslatmam —</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected($val('user_id', auth()->id()) == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Lid (ixtiyoriy)</label>
                    <select name="lead_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}" @selected(old('lead_id', $reminder->lead_id ?? request('lead_id')) == $l->id)>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Mijoz (ixtiyoriy)</label>
                <select name="client_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                    <option value="">— Tanlang —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" @selected($val('client_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Tavsif</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ $val('description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('reminders.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Bekor</a>
            <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-sm font-medium text-white transition">
                <i class="fas fa-check mr-1"></i> {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>
