@php
    $lead = $lead ?? null;
    $val = fn($key, $default = '') => old($key, $lead->{$key} ?? $default);
    $isManager = auth()->user()->isManager();
@endphp

<div class="max-w-4xl mx-auto" x-data="{ budget: @js((int) $val('budget', 0)), budgetInput: '' }" x-init="budgetInput = budget ? budget.toLocaleString('ru-RU').replace(/,/g, ' ') : ''">
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

        {{-- 1. Aloqa ma'lumotlari --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100 text-cyan-700 text-xs font-bold">1</span>
                <h3 class="text-base font-semibold text-slate-900">Aloqa ma'lumotlari</h3>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Mijoz ismi <span class="text-red-500">*</span></label>
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
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Mijoz qaerdan keldi?</label>
                    <select name="source" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        @foreach(\App\Models\Lead::SOURCES as $key => $label)
                            <option value="{{ $key }}" @selected($val('source', 'call') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 2. Mijoz nima istaydi --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 text-xs font-bold">2</span>
                <h3 class="text-base font-semibold text-slate-900">Mijoz nima istaydi?</h3>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Byudjet (UZS)</label>
                    <input type="text" inputmode="numeric" x-model="budgetInput"
                        @input="
                            const raw = $event.target.value.replace(/\D/g, '');
                            budget = raw ? parseInt(raw, 10) : 0;
                            budgetInput = budget ? budget.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                            $event.target.value = budgetInput;
                        "
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="500 000 000">
                    <input type="hidden" name="budget" :value="budget">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">To'lov turi</label>
                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach(\App\Models\Lead::PAYMENT_METHODS as $key => $label)
                            <option value="{{ $key }}" @selected($val('payment_method') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Necha xonali kerak</label>
                    <select name="rooms_wanted" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Muhim emas —</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" @selected((int) $val('rooms_wanted') === $i)>{{ $i }} xona{{ $i === 6 ? '+' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Qachon kerak</label>
                    <select name="urgency" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach(\App\Models\Lead::URGENCY as $key => $label)
                            <option value="{{ $key }}" @selected($val('urgency') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Maydon (m²) — kamida</label>
                    <input type="number" name="area_min" value="{{ $val('area_min') }}" min="0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="60">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Maydon (m²) — ko'pi bilan</label>
                    <input type="number" name="area_max" value="{{ $val('area_max') }}" min="0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="100">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Qaysi tuman/hudud</label>
                    <input type="text" name="preferred_district" value="{{ $val('preferred_district') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Yunusobod, Mirzo Ulug'bek...">
                </div>
            </div>
        </div>

        {{-- 3. Kuzatuv va biriktirish --}}
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700 text-xs font-bold">3</span>
                <h3 class="text-base font-semibold text-slate-900">Kuzatuv</h3>
            </div>
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
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Keyingi aloqa sanasi</label>
                    <input type="date" name="next_follow_up" value="{{ $val('next_follow_up') instanceof \Carbon\Carbon ? $val('next_follow_up')->format('Y-m-d') : $val('next_follow_up') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Mavjud uy bilan bog'lash (ixtiyoriy)</label>
                    <select name="property_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" @selected($val('property_id') == $p->id)>{{ $p->title }} — {{ number_format($p->price, 0, '.', ' ') }} UZS</option>
                        @endforeach
                    </select>
                </div>
                @if($isManager)
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Biriktirilgan operator</label>
                        <select name="operator_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                            <option value="">— Tanlang —</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}" @selected($val('operator_id', auth()->id()) == $op->id)>{{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="operator_id" value="{{ auth()->id() }}">
                    <div class="sm:col-span-2 rounded-xl bg-slate-50 border border-slate-200 px-3 py-2 text-sm text-slate-600">
                        <i class="fas fa-circle-info text-slate-400 mr-1"></i>
                        Bu lid sizga biriktiriladi: <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                    </div>
                @endif
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Suhbat izohi</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Mijoz nimalar dedi, qanday talablar bor...">{{ $val('notes') }}</textarea>
                </div>
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
