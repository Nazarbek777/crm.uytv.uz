@php
    $lead = $lead ?? null;
    $val = fn($key, $default = '') => old($key, $lead->{$key} ?? $default);
@endphp

<div class="max-w-3xl mx-auto" x-data="{ budget: @js((int) $val('budget', 0)), budgetInput: '' }" x-init="budgetInput = budget ? budget.toLocaleString('ru-RU').replace(/,/g, ' ') : ''">
    <div class="mb-5">
        <a href="{{ route('leads.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Lidlar
        </a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" class="space-y-4">
        @csrf
        @if($method === 'PUT') @method('PUT') @endif

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-900">Asosiy ma'lumotlar</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Ism <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $val('name') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Telefon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ $val('phone') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="+998 90 ...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ $val('email') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Manba</label>
                    <select name="source" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        @foreach(\App\Models\Lead::SOURCES as $key => $label)
                            <option value="{{ $key }}" @selected($val('source', 'other') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-900">Tafsilotlar</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        @foreach(\App\Models\Lead::STATUSES as $key => $info)
                            <option value="{{ $key }}" @selected($val('status', 'new') === $key)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Biriktirilgan operator</label>
                    <select name="operator_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" @selected($val('operator_id') == $op->id)>{{ $op->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Qiziqayotgan uy</label>
                    <select name="property_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" @selected($val('property_id') == $p->id)>{{ $p->title }} — {{ number_format($p->price, 0, '.', ' ') }} UZS</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Byudjet (UZS)</label>
                    <input type="text" inputmode="numeric" x-model="budgetInput"
                        @input="
                            const raw = $event.target.value.replace(/\D/g, '');
                            budget = raw ? parseInt(raw, 10) : 0;
                            budgetInput = budget ? budget.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                            $event.target.value = budgetInput;
                        "
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                    <input type="hidden" name="budget" :value="budget">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Keyingi aloqa sanasi</label>
                    <input type="date" name="next_follow_up" value="{{ $val('next_follow_up') instanceof \Carbon\Carbon ? $val('next_follow_up')->format('Y-m-d') : $val('next_follow_up') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Izoh</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ $val('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('leads.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Bekor</a>
            <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-sm font-medium text-white transition">
                <i class="fas fa-check mr-1"></i> {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
