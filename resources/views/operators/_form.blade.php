@php
    $operator = $operator ?? null;
    $val = fn($key, $default = '') => old($key, $operator->{$key} ?? $default);
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('operators.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Operatorlar ro'yxati
        </a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" class="space-y-4">
        @csrf
        @if($method === 'PUT') @method('PUT') @endif

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-900">Shaxsiy ma'lumotlar</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">F.I.O <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $val('name') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Telefon</label>
                    <input type="text" name="phone" value="{{ $val('phone') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="+998 90 123 45 67">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ $val('email') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Lavozim <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="operator" @selected($val('role', 'operator') === 'operator')>Operator</option>
                        <option value="manager" @selected($val('role') === 'manager')>Menejer</option>
                        <option value="admin" @selected($val('role') === 'admin')>Administrator</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Izoh</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ $val('notes') }}</textarea>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="active" value="1" @checked((bool) $val('active', true)) class="w-4 h-4 rounded accent-slate-900">
                <span class="text-sm text-slate-700">Faol (tizimga kira oladi)</span>
            </label>
        </div>

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-900">Parol</h3>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">
                    @if($operator) Yangi parol (bo'sh qoldirsangiz, eski qoladi) @else Parol <span class="text-red-500">*</span> @endif
                </label>
                <input type="password" name="password" {{ $operator ? '' : 'required' }} minlength="6" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="kamida 6 belgi">
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('operators.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Bekor</a>
            <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-sm font-medium text-white transition">
                <i class="fas fa-check mr-1"></i> {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>
