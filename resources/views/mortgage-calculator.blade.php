@extends('layouts.main')

@section('title', 'Ipoteka Kalkulyatori')

@section('content')
<div class="max-w-6xl mx-auto" x-data="mortgageCalc()" x-init="calc()">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 transition">
                <i class="fas fa-arrow-left"></i> Dashboardga qaytish
            </a>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Ipoteka Kalkulyatori</h1>
        <p class="text-slate-600">Uy sotib olish uchun ipoteka to‘lovlarini real-vaqtda hisoblang.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl bg-white p-8 shadow-lg border border-slate-200">
            <h2 class="text-xl font-semibold text-slate-900 mb-6">Parametrlar</h2>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kiritish valyutasi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="setCurrency('UZS')" :class="currency === 'UZS' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="rounded-2xl px-4 py-3 text-sm font-medium transition">UZS</button>
                        <button type="button" @click="setCurrency('USD')" :class="currency === 'USD' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="rounded-2xl px-4 py-3 text-sm font-medium transition">USD</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Natija valyutasi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="displayCurrency = 'UZS'; calc()" :class="displayCurrency === 'UZS' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="rounded-2xl px-4 py-3 text-sm font-medium transition">UZS</button>
                        <button type="button" @click="displayCurrency = 'USD'; calc()" :class="displayCurrency === 'USD' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="rounded-2xl px-4 py-3 text-sm font-medium transition">USD</button>
                    </div>
                </div>

                <div>
                    <label for="property_price" class="block text-sm font-medium text-slate-700 mb-2">
                        Uy narxi (<span x-text="currency"></span>)
                    </label>
                    <input type="text" inputmode="numeric" id="property_price" x-model="propertyPriceInput" @input="onNumberInput($event, 'propertyPrice')" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none" placeholder="Masalan: 45 000">
                </div>

                <div>
                    <label for="investor_fee" class="block text-sm font-medium text-slate-700 mb-2">
                        Investor qo‘shimchasi (<span x-text="currency"></span>)
                        <span class="text-xs text-slate-500 font-normal">— mijoz boshlang‘ich to‘lovga qo‘shadi</span>
                    </label>
                    <input type="text" inputmode="numeric" id="investor_fee" x-model="investorFeeInput" @input="onNumberInput($event, 'investorFee')" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none" placeholder="Masalan: 5 000">
                </div>

                <div>
                    <label for="down_payment_percent" class="block text-sm font-medium text-slate-700 mb-2">
                        Boshlang‘ich to‘lov: <span class="font-semibold" x-text="downPaymentPercent + '%'"></span>
                    </label>
                    <input type="range" id="down_payment_percent" x-model.number="downPaymentPercent" @input="calc()" min="0" max="100" step="1" class="w-full accent-slate-900">
                    <div class="flex justify-between text-xs text-slate-500 mt-1">
                        <span>0%</span><span>20%</span><span>50%</span><span>100%</span>
                    </div>
                </div>

                <div>
                    <label for="interest_rate" class="block text-sm font-medium text-slate-700 mb-2">Yillik foiz stavkasi (%)</label>
                    <input type="number" step="0.1" id="interest_rate" x-model.number="interestRate" @input="calc()" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none">
                </div>

                <div>
                    <label for="term_years" class="block text-sm font-medium text-slate-700 mb-2">
                        Muddat: <span class="font-semibold" x-text="termYears + ' yil'"></span>
                    </label>
                    <input type="range" id="term_years" x-model.number="termYears" @input="calc()" min="1" max="30" step="1" class="w-full accent-slate-900">
                    <div class="flex justify-between text-xs text-slate-500 mt-1">
                        <span>1</span><span>10</span><span>20</span><span>30</span>
                    </div>
                </div>

                <div x-show="currency !== displayCurrency || currency === 'USD' || displayCurrency === 'USD'">
                    <label for="exchange_rate" class="block text-sm font-medium text-slate-700 mb-2">USD kursi (1 USD = ? UZS)</label>
                    <input type="number" id="exchange_rate" x-model.number="exchangeRate" @input="calc()" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white shadow-2xl">
                <h3 class="text-xl font-semibold mb-6">Hisoblash natijasi</h3>

                <div x-show="loanAmount > 0" class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-900/90 p-4 border border-slate-700">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Uy narxi</p>
                        <p class="mt-2 text-xl font-semibold" x-text="fmt(disp(propertyPrice)) + ' ' + displayCurrency"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/90 p-4 border border-slate-700">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Boshlang‘ich to‘lov (uy narxidan)</p>
                        <p class="mt-2 text-xl font-semibold" x-text="fmt(disp(downPayment)) + ' ' + displayCurrency"></p>
                        <p class="text-xs text-slate-400 mt-1" x-text="downPaymentPercent + '% uy narxidan'"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/90 p-4 border border-slate-700">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Investor qo‘shimchasi</p>
                        <p class="mt-2 text-xl font-semibold" x-text="fmt(disp(investorFee)) + ' ' + displayCurrency"></p>
                    </div>
                    <div class="rounded-2xl bg-cyan-900/40 p-4 border border-cyan-700/60">
                        <p class="text-xs uppercase tracking-[0.2em] text-cyan-300">Mijoz to‘laydigan jami boshlang‘ich</p>
                        <p class="mt-2 text-xl font-semibold" x-text="fmt(disp(clientUpfront)) + ' ' + displayCurrency"></p>
                        <p class="text-xs text-cyan-300/80 mt-1">Boshlang‘ich + investor qo‘shimchasi</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/90 p-4 border border-slate-700 sm:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Kredit summasi</p>
                        <p class="mt-2 text-2xl font-semibold" x-text="fmt(disp(loanAmount)) + ' ' + displayCurrency"></p>
                        <p class="text-xs text-slate-400 mt-1">Uy narxi − boshlang‘ich to‘lov</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-900/40 p-4 border border-emerald-700/60 sm:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-300">Oylik to‘lov</p>
                        <p class="mt-2 text-3xl font-bold" x-text="fmt(disp(monthlyPayment)) + ' ' + displayCurrency"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/90 p-4 border border-slate-700">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Jami to‘lov</p>
                        <p class="mt-2 text-xl font-semibold" x-text="fmt(disp(totalPayment)) + ' ' + displayCurrency"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/90 p-4 border border-slate-700">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Jami foiz</p>
                        <p class="mt-2 text-xl font-semibold" x-text="fmt(disp(totalInterest)) + ' ' + displayCurrency"></p>
                    </div>
                </div>

                <div x-show="loanAmount > 0 && currency !== displayCurrency" class="mt-4 text-xs text-slate-400 text-center">
                    Kurs: 1 USD = <span x-text="fmt(exchangeRate)"></span> UZS
                </div>

                <div x-show="loanAmount <= 0" class="text-slate-300 text-sm">
                    Hisoblash uchun uy narxini kiriting.
                </div>

                <div x-show="exceedsBankLimit" class="mt-4 rounded-2xl bg-amber-500/15 border border-amber-400/40 p-4">
                    <div class="flex gap-3">
                        <i class="fas fa-triangle-exclamation text-amber-400 mt-0.5"></i>
                        <div class="text-sm text-amber-200">
                            <strong>Diqqat:</strong> kredit summasi <span x-text="fmt(loanAmountUZS)"></span> UZS — bank maksimal limiti 380 000 000 UZS dan oshib ketdi.
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 border border-slate-200 shadow-sm" x-show="loanAmount > 0">
                <h4 class="text-base font-semibold text-slate-900 mb-3">To‘lov tarkibi</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-3 rounded-full bg-slate-100 overflow-hidden flex">
                            <div class="bg-slate-900" :style="`width: ${principalShare}%`"></div>
                            <div class="bg-amber-400" :style="`width: ${interestShare}%`"></div>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span class="flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-900"></span> Asosiy qarz <span x-text="principalShare.toFixed(1) + '%'"></span></span>
                        <span class="flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400"></span> Foiz <span x-text="interestShare.toFixed(1) + '%'"></span></span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-blue-50 p-6 border border-blue-200">
                <h4 class="text-lg font-semibold text-blue-900 mb-3">Maslahat</h4>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Boshlang‘ich to‘lov uy narxidan hisoblanadi (odatda 20–30%)</li>
                    <li>• Investor qo‘shimchasi mijozning boshlang‘ich to‘loviga qo‘shiladi, kreditga ta'sir qilmaydi</li>
                    <li>• Bank maksimal kredit limiti: 380 000 000 UZS</li>
                    <li>• Uzunroq muddat oylik to‘lovni kamaytiradi, lekin jami foizni oshiradi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function mortgageCalc() {
        const initialPrice = {{ (int) round($avgPrice) }};
        return {
            currency: 'UZS',
            displayCurrency: 'UZS',
            propertyPrice: initialPrice,
            propertyPriceInput: initialPrice ? initialPrice.toLocaleString('ru-RU').replace(/,/g, ' ') : '',
            investorFee: 0,
            investorFeeInput: '',
            downPaymentPercent: 30,
            interestRate: 12,
            termYears: 20,
            exchangeRate: 12600,

            downPayment: 0,
            clientUpfront: 0,
            loanAmount: 0,
            loanAmountUZS: 0,
            monthlyPayment: 0,
            totalPayment: 0,
            totalInterest: 0,
            principalShare: 0,
            interestShare: 0,
            exceedsBankLimit: false,

            setCurrency(c) {
                if (this.currency === c) return;
                if (c === 'USD') {
                    this.propertyPrice = Math.round(this.propertyPrice / this.exchangeRate);
                    this.investorFee = Math.round(this.investorFee / this.exchangeRate);
                } else {
                    this.propertyPrice = Math.round(this.propertyPrice * this.exchangeRate);
                    this.investorFee = Math.round(this.investorFee * this.exchangeRate);
                }
                this.currency = c;
                this.displayCurrency = c;
                this.propertyPriceInput = this.propertyPrice ? this.propertyPrice.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                this.investorFeeInput = this.investorFee ? this.investorFee.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                this.calc();
            },

            disp(v) {
                if (this.currency === this.displayCurrency) return v;
                if (this.currency === 'USD' && this.displayCurrency === 'UZS') return v * this.exchangeRate;
                if (this.currency === 'UZS' && this.displayCurrency === 'USD') return v / this.exchangeRate;
                return v;
            },

            onNumberInput(e, field) {
                const raw = e.target.value.replace(/\D/g, '');
                const num = raw ? parseInt(raw, 10) : 0;
                this[field] = num;
                const formatted = num ? num.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
                this[field + 'Input'] = formatted;
                e.target.value = formatted;
                this.calc();
            },

            calc() {
                const price = Number(this.propertyPrice) || 0;
                const investor = Number(this.investorFee) || 0;
                const dpPct = Number(this.downPaymentPercent) || 0;
                const rate = Number(this.interestRate) || 0;
                const years = Number(this.termYears) || 0;

                this.downPayment = price * (dpPct / 100);
                this.clientUpfront = this.downPayment + investor;
                this.loanAmount = price - this.downPayment;

                const monthlyRate = rate / 100 / 12;
                const months = years * 12;

                if (this.loanAmount > 0 && months > 0) {
                    if (monthlyRate > 0) {
                        const factor = Math.pow(1 + monthlyRate, months);
                        this.monthlyPayment = this.loanAmount * (monthlyRate * factor) / (factor - 1);
                    } else {
                        this.monthlyPayment = this.loanAmount / months;
                    }
                    this.totalPayment = this.monthlyPayment * months;
                    this.totalInterest = this.totalPayment - this.loanAmount;
                    this.principalShare = (this.loanAmount / this.totalPayment) * 100;
                    this.interestShare = 100 - this.principalShare;
                } else {
                    this.monthlyPayment = 0;
                    this.totalPayment = 0;
                    this.totalInterest = 0;
                    this.principalShare = 0;
                    this.interestShare = 0;
                }

                this.loanAmountUZS = this.currency === 'USD' ? this.loanAmount * this.exchangeRate : this.loanAmount;
                this.exceedsBankLimit = this.loanAmountUZS > 380000000;
            },

            fmt(v) {
                return Math.round(v).toLocaleString('ru-RU').replace(/,/g, ' ');
            },
        }
    }
</script>
@endsection
