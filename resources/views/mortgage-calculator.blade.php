@extends('layouts.main')

@section('title', 'Ipoteka Kalkulyatori')

@section('content')
<div class="max-w-5xl mx-auto" x-data="mortgageCalc()" x-init="syncDownPaymentAmountFromPercent(); calc()">
    <div class="mb-5 flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition mb-1">
                <i class="fas fa-arrow-left text-xs"></i> Dashboard
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Ipoteka Kalkulyatori</h1>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <div class="lg:col-span-2 rounded-2xl bg-white p-5 shadow-sm border border-slate-200 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Kiritish</label>
                    <div class="flex rounded-xl bg-slate-100 p-0.5 text-sm">
                        <button type="button" @click="setCurrency('UZS')" :class="currency === 'UZS' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="flex-1 rounded-lg py-1.5 font-medium transition">UZS</button>
                        <button type="button" @click="setCurrency('USD')" :class="currency === 'USD' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="flex-1 rounded-lg py-1.5 font-medium transition">USD</button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Natija</label>
                    <div class="flex rounded-xl bg-slate-100 p-0.5 text-sm">
                        <button type="button" @click="displayCurrency = 'UZS'; calc()" :class="displayCurrency === 'UZS' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="flex-1 rounded-lg py-1.5 font-medium transition">UZS</button>
                        <button type="button" @click="displayCurrency = 'USD'; calc()" :class="displayCurrency === 'USD' ? 'bg-white shadow text-slate-900' : 'text-slate-500'" class="flex-1 rounded-lg py-1.5 font-medium transition">USD</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Uy narxi (<span x-text="currency"></span>)</label>
                    <input type="text" inputmode="numeric" x-model="propertyPriceInput" @input="onNumberInput($event, 'propertyPrice')" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Investor qo‘shimchasi</label>
                    <input type="text" inputmode="numeric" x-model="investorFeeInput" @input="onNumberInput($event, 'investorFee')" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="0">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-medium text-slate-500">Boshlang‘ich to‘lov</label>
                    <span class="text-xs text-slate-500">min <span x-text="effectiveMinPercent"></span>%</span>
                </div>
                <div class="flex gap-2 items-center">
                    <input type="range" x-model.number="downPaymentPercent" @input="onPercentChange()" :min="effectiveMinPercent" max="100" step="1" class="flex-1 accent-slate-900">
                    <div class="text-sm font-semibold w-12 text-right" x-text="downPaymentPercent + '%'"></div>
                </div>
                <input type="text" inputmode="numeric" x-model="downPaymentAmountInput" @input="onDownPaymentAmountInput($event)" :placeholder="'Summa (' + currency + ')'" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                <p x-show="effectiveMinPercent > 20" class="text-[11px] text-amber-600 mt-1.5">
                    <i class="fas fa-info-circle"></i> Bank limiti 380M UZS — minimal <span x-text="effectiveMinPercent"></span>%
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Foiz stavkasi (%)</label>
                    <input type="number" step="0.1" x-model.number="interestRate" @input="calc()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-medium text-slate-500">Muddat</label>
                        <span class="text-xs font-semibold" x-text="termYears + ' yil'"></span>
                    </div>
                    <input type="range" x-model.number="termYears" @input="calc()" min="1" max="30" step="1" class="w-full accent-slate-900 mt-2">
                </div>
            </div>

            <div x-show="currency === 'USD' || displayCurrency === 'USD'">
                <label class="block text-xs font-medium text-slate-500 mb-1.5">USD kursi (UZS)</label>
                <input type="number" x-model.number="exchangeRate" @input="calc()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            </div>
        </div>

        <div class="lg:col-span-3 space-y-4">
            <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-5 text-white shadow-lg">
                <div x-show="loanAmount > 0">
                    <div class="rounded-xl bg-emerald-500/15 border border-emerald-400/30 p-4 mb-4">
                        <p class="text-[11px] uppercase tracking-wider text-emerald-300">Oylik to‘lov</p>
                        <p class="mt-1 text-3xl font-bold" x-text="fmt(disp(monthlyPayment)) + ' ' + displayCurrency"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-px bg-slate-700/50 rounded-xl overflow-hidden text-sm">
                        <div class="bg-slate-900 p-3">
                            <p class="text-[11px] text-slate-400">Uy narxi</p>
                            <p class="font-semibold mt-0.5" x-text="fmt(disp(propertyPrice)) + ' ' + displayCurrency"></p>
                        </div>
                        <div class="bg-slate-900 p-3">
                            <p class="text-[11px] text-slate-400">Boshlang‘ich (<span x-text="downPaymentPercent + '%'"></span>)</p>
                            <p class="font-semibold mt-0.5" x-text="fmt(disp(downPayment)) + ' ' + displayCurrency"></p>
                        </div>
                        <div class="bg-slate-900 p-3">
                            <p class="text-[11px] text-slate-400">Investor qo‘shimchasi</p>
                            <p class="font-semibold mt-0.5" x-text="fmt(disp(investorFee)) + ' ' + displayCurrency"></p>
                        </div>
                        <div class="bg-cyan-900/40 p-3">
                            <p class="text-[11px] text-cyan-300">Mijoz to‘laydi</p>
                            <p class="font-semibold mt-0.5" x-text="fmt(disp(clientUpfront)) + ' ' + displayCurrency"></p>
                        </div>
                        <div class="bg-slate-900 p-3 col-span-2 border-t border-slate-700">
                            <p class="text-[11px] text-slate-400">Kredit summasi</p>
                            <p class="text-xl font-semibold mt-0.5" x-text="fmt(disp(loanAmount)) + ' ' + displayCurrency"></p>
                        </div>
                        <div class="bg-slate-900 p-3">
                            <p class="text-[11px] text-slate-400">Jami to‘lov</p>
                            <p class="font-semibold mt-0.5" x-text="fmt(disp(totalPayment)) + ' ' + displayCurrency"></p>
                        </div>
                        <div class="bg-slate-900 p-3">
                            <p class="text-[11px] text-slate-400">Jami foiz</p>
                            <p class="font-semibold mt-0.5" x-text="fmt(disp(totalInterest)) + ' ' + displayCurrency"></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="h-2 rounded-full bg-slate-700 overflow-hidden flex">
                            <div class="bg-slate-300" :style="`width: ${principalShare}%`"></div>
                            <div class="bg-amber-400" :style="`width: ${interestShare}%`"></div>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-400 mt-1.5">
                            <span><span class="inline-block w-2 h-2 rounded-full bg-slate-300 mr-1"></span>Asosiy <span x-text="principalShare.toFixed(0) + '%'"></span></span>
                            <span x-show="currency !== displayCurrency">1 USD = <span x-text="fmt(exchangeRate)"></span> UZS</span>
                            <span><span class="inline-block w-2 h-2 rounded-full bg-amber-400 mr-1"></span>Foiz <span x-text="interestShare.toFixed(0) + '%'"></span></span>
                        </div>
                    </div>
                </div>

                <div x-show="loanAmount <= 0" class="text-slate-300 text-sm py-8 text-center">
                    <i class="fas fa-calculator text-3xl text-slate-600 mb-2 block"></i>
                    Uy narxini kiriting
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function mortgageCalc() {
        const initialPrice = {{ (int) round($avgPrice) }};
        const fmtNum = n => n ? n.toLocaleString('ru-RU').replace(/,/g, ' ') : '';
        return {
            currency: 'UZS',
            displayCurrency: 'UZS',
            propertyPrice: initialPrice,
            propertyPriceInput: fmtNum(initialPrice),
            investorFee: 0,
            investorFeeInput: '',
            downPaymentPercent: 30,
            downPaymentAmountInput: '',
            interestRate: 12,
            termYears: 20,
            exchangeRate: 12600,
            downPayment: 0, clientUpfront: 0, loanAmount: 0,
            monthlyPayment: 0, totalPayment: 0, totalInterest: 0,
            principalShare: 0, interestShare: 0,
            effectiveMinPercent: 20,
            MAX_LOAN_UZS: 380000000,

            setCurrency(c) {
                if (this.currency === c) return;
                const r = c === 'USD' ? 1 / this.exchangeRate : this.exchangeRate;
                this.propertyPrice = Math.round(this.propertyPrice * r);
                this.investorFee = Math.round(this.investorFee * r);
                this.currency = c;
                this.displayCurrency = c;
                this.propertyPriceInput = fmtNum(this.propertyPrice);
                this.investorFeeInput = fmtNum(this.investorFee);
                this.syncDownPaymentAmountFromPercent();
                this.calc();
            },

            disp(v) {
                if (this.currency === this.displayCurrency) return v;
                return this.currency === 'USD' ? v * this.exchangeRate : v / this.exchangeRate;
            },

            onNumberInput(e, field) {
                const num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
                this[field] = num;
                this[field + 'Input'] = fmtNum(num);
                e.target.value = this[field + 'Input'];
                if (field === 'propertyPrice') this.syncDownPaymentAmountFromPercent();
                this.calc();
            },

            onPercentChange() {
                if (this.downPaymentPercent < this.effectiveMinPercent) this.downPaymentPercent = this.effectiveMinPercent;
                this.syncDownPaymentAmountFromPercent();
                this.calc();
            },

            onDownPaymentAmountInput(e) {
                const num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
                this.downPaymentAmountInput = fmtNum(num);
                e.target.value = this.downPaymentAmountInput;
                const price = Number(this.propertyPrice) || 0;
                if (price > 0) {
                    let pct = (num / price) * 100;
                    pct = Math.max(this.effectiveMinPercent, Math.min(100, pct));
                    this.downPaymentPercent = Math.round(pct);
                }
                this.calc();
            },

            syncDownPaymentAmountFromPercent() {
                const amount = Math.round((Number(this.propertyPrice) || 0) * (this.downPaymentPercent / 100));
                this.downPaymentAmountInput = fmtNum(amount);
            },

            calc() {
                const price = Number(this.propertyPrice) || 0;
                const investor = Number(this.investorFee) || 0;
                const rate = Number(this.interestRate) || 0;
                const months = (Number(this.termYears) || 0) * 12;

                const maxLoan = this.currency === 'USD' ? this.MAX_LOAN_UZS / this.exchangeRate : this.MAX_LOAN_UZS;
                let minPct = 20;
                if (price > 0) minPct = Math.max(20, Math.ceil(((price - maxLoan) / price) * 100));
                this.effectiveMinPercent = Math.min(100, minPct);

                if (this.downPaymentPercent < this.effectiveMinPercent) {
                    this.downPaymentPercent = this.effectiveMinPercent;
                    this.syncDownPaymentAmountFromPercent();
                }

                const dpPct = Number(this.downPaymentPercent) || 0;
                this.downPayment = price * (dpPct / 100);
                this.clientUpfront = this.downPayment + investor;
                this.loanAmount = price - this.downPayment;

                const mr = rate / 100 / 12;
                if (this.loanAmount > 0 && months > 0) {
                    if (mr > 0) {
                        const f = Math.pow(1 + mr, months);
                        this.monthlyPayment = this.loanAmount * (mr * f) / (f - 1);
                    } else {
                        this.monthlyPayment = this.loanAmount / months;
                    }
                    this.totalPayment = this.monthlyPayment * months;
                    this.totalInterest = this.totalPayment - this.loanAmount;
                    this.principalShare = (this.loanAmount / this.totalPayment) * 100;
                    this.interestShare = 100 - this.principalShare;
                } else {
                    this.monthlyPayment = this.totalPayment = this.totalInterest = 0;
                    this.principalShare = this.interestShare = 0;
                }
            },

            fmt(v) { return Math.round(v).toLocaleString('ru-RU').replace(/,/g, ' '); },
        }
    }
</script>
@endsection
