@php
    $property = $property ?? null;
    $val = fn($key, $default = '') => old($key, $property->{$key} ?? $default);
@endphp

<div class="max-w-4xl mx-auto" x-data="{ price: @js((int) $val('price', 0)), priceInput: '' }" x-init="priceInput = price ? price.toLocaleString('ru-RU').replace(/,/g, ' ') : ''">
    <div class="mb-5">
        <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left text-xs"></i> Uylar ro'yxati
        </a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1"><i class="fas fa-circle-exclamation mr-1"></i> Quyidagi xatolarni to'g'rilang:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" class="space-y-5">
        @csrf
        @if($method === 'PUT') @method('PUT') @endif

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-900">Asosiy ma'lumotlar</h3>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $val('title') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="2 xonali kvartira">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Investor</label>
                    <select name="investor_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">— Tanlang —</option>
                        @foreach($investors as $inv)
                            <option value="{{ $inv->id }}" @selected($val('investor_id') == $inv->id)>{{ $inv->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Manzil <span class="text-red-500">*</span></label>
                <input type="text" name="address" value="{{ $val('address') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Toshkent, Yunusobod tumani, ...">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Narx (UZS) <span class="text-red-500">*</span></label>
                    <input type="text" inputmode="numeric" x-model="priceInput"
                        @input="
                            const raw = $event.target.value.replace(/\D/g, '');
                            price = raw ? parseInt(raw, 10) : 0;
                            priceInput = price ? price.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                            $event.target.value = priceInput;
                        "
                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="500 000 000">
                    <input type="hidden" name="price" :value="price">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Holat <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="free" @selected($val('status', 'free') === 'free')>Bo'sh</option>
                        <option value="sold" @selected($val('status') === 'sold')>Sotilgan</option>
                        <option value="rent" @selected($val('status') === 'rent')>Ijarada</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-900">Texnik xususiyatlari</h3>
            <div class="grid gap-4 sm:grid-cols-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Xonalar</label>
                    <input type="number" name="rooms" min="1" value="{{ $val('rooms') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Qavat</label>
                    <input type="number" name="floor" min="0" value="{{ $val('floor') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Umumiy qavat</label>
                    <input type="number" name="total_floors" min="0" value="{{ $val('total_floors') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Maydon (m²)</label>
                    <input type="number" name="area" min="0" value="{{ $val('area') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Izoh</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Qo'shimcha ma'lumot...">{{ $val('description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('properties.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Bekor</a>
            <button type="submit" class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-sm font-medium text-white transition">
                <i class="fas fa-check mr-1"></i> {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
