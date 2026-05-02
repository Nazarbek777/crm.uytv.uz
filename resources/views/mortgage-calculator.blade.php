@extends('layouts.main')

@section('title', 'Ipoteka Kalkulyatori')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 transition">
                <i class="fas fa-arrow-left"></i> Dashboardga qaytish
            </a>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Ipoteka Kalkulyatori</h1>
        <p class="text-slate-600">Uy sotib olish uchun ipoteka to‘lovlarini hisoblang.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl bg-white p-8 shadow-lg border border-slate-200">
            <h2 class="text-xl font-semibold text-slate-900 mb-6">Hisoblash</h2>
            <form action="{{ route('calculate.mortgage') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="property_price" class="block text-sm font-medium text-slate-700 mb-2">Uy narxi</label>
                    <div class="flex gap-2">
                        <input type="number" id="property_price" name="property_price" value="{{ old('property_price', $avgPrice) }}" class="flex-1 rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none" placeholder="Masalan: 500000000" required>
                        <select name="currency" id="currency" class="rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none">
                            <option value="UZS" {{ old('currency', 'UZS') == 'UZS' ? 'selected' : '' }}>UZS</option>
                            <option value="USD" {{ old('currency', 'UZS') == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                    @error('property_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    @error('currency') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="down_payment_percent" class="block text-sm font-medium text-slate-700 mb-2">Boshlang‘ich to‘lov (%)</label>
                    <input type="number" step="0.01" id="down_payment_percent" name="down_payment_percent" value="{{ old('down_payment_percent', 30) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none" placeholder="Masalan: 30" required>
                    @error('down_payment_percent') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="interest_rate" class="block text-sm font-medium text-slate-700 mb-2">Yillik foiz stavkasi (%)</label>
                    <input type="number" step="0.01" id="interest_rate" name="interest_rate" value="{{ old('interest_rate', 12) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none" placeholder="Masalan: 12" required>
                    @error('interest_rate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="term_years" class="block text-sm font-medium text-slate-700 mb-2">Muddat (yil)</label>
                    <input type="number" id="term_years" name="term_years" value="{{ old('term_years', 20) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none" placeholder="Masalan: 20" required>
                    @error('term_years') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-6 py-4 text-white font-medium hover:bg-slate-800 transition">
                    <i class="fas fa-calculator mr-2"></i> Hisoblash
                </button>
            </form>
        </div>

        <div class="space-y-6">
            @if(session('mortgage_result'))
                <div class="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-2xl">
                    <h3 class="text-xl font-semibold mb-6">Hisoblash natijasi</h3>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-700">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Uy narxi</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format(session('mortgage_result')['property_price'], 0, ',', ' ') }} {{ session('mortgage_result')['currency'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-700">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Boshlang‘ich to‘lov</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format(session('mortgage_result')['down_payment'], 0, ',', ' ') }} {{ session('mortgage_result')['currency'] }}</p>
                            <p class="text-xs text-slate-400">{{ session('mortgage_result')['down_payment_percent'] }}%</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-700">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Kredit summasi</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format(session('mortgage_result')['loan_amount'], 0, ',', ' ') }} {{ session('mortgage_result')['currency'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-700">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Oylik to‘lov</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format(session('mortgage_result')['monthly_payment'], 0, ',', ' ') }} {{ session('mortgage_result')['currency'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-700">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Jami to‘lov</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format(session('mortgage_result')['total_payment'], 0, ',', ' ') }} {{ session('mortgage_result')['currency'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-900/90 p-4 border border-slate-700">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Jami foiz</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format(session('mortgage_result')['total_interest'], 0, ',', ' ') }} {{ session('mortgage_result')['currency'] }}</p>
                        </div>
                    </div>
                    <div class="mt-6 p-4 rounded-3xl bg-slate-950/80 border border-slate-700">
                        <p class="text-sm text-slate-300">
                            <strong>Muddat:</strong> {{ session('mortgage_result')['term_years'] }} yil<br>
                            <strong>Foiz stavkasi:</strong> {{ session('mortgage_result')['interest_rate'] }}% yillik<br>
                            <strong>Valyuta:</strong> {{ session('mortgage_result')['currency'] }}
                            @if(session('mortgage_result')['currency'] === 'USD')
                                (1 USD = {{ number_format(session('mortgage_result')['exchange_rate'], 0, ',', ' ') }} UZS)
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="rounded-3xl bg-slate-50 p-8 border border-slate-200 text-center">
                    <i class="fas fa-calculator text-6xl text-slate-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Hisoblashni boshlang</h3>
                    <p class="text-slate-600">Yuqoridagi formani to‘ldirib, ipoteka to‘lovlarini hisoblang.</p>
                </div>
            @endif

            <div class="rounded-3xl bg-blue-50 p-6 border border-blue-200">
                <h4 class="text-lg font-semibold text-blue-900 mb-3">Maslahat</h4>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Boshlang‘ich to‘lov odatda 20-30% ni tashkil qiladi</li>
                    <li>• Investor qo'shimi boshlang‘ich to‘lovga qo'shiladi</li>
                    <li>• Bank maksimal kredit limiti 380 million UZS</li>
                    <li>• Foiz stavkasi bankdan bankga farq qiladi</li>
                    <li>• Uzunroq muddat oylik to‘lovni kamaytiradi, lekin jami foizni oshiradi</li>
                    <li>• Hisob-kitob taxminiy va maslahat uchun mo‘ljallangan</li>
                    <li>• Valyuta kursi: 1 USD ≈ 12,600 UZS (taxminiy)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection