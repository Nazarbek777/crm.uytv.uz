<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRM Uy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 text-white flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-slate-900/90 border border-white/10 rounded-3xl shadow-2xl backdrop-blur-xl p-8">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gradient-to-br from-cyan-400 to-blue-600 text-slate-950 mb-4 shadow-lg">
                <i class="fas fa-building text-2xl"></i>
            </div>
            <h1 class="text-3xl font-semibold mb-2">CRM Uy tizimiga kirish</h1>
            <p class="text-slate-400">Hisobingiz bilan tizimga kiring va barcha operatsiyalarni boshqaring.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-2xl bg-red-500/10 border border-red-500/20 p-4 text-sm text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm text-slate-300 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="hello@crm.uy">
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-2">Parol</label>
                <input type="password" name="password" required class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="••••••••">
            </div>
            <div class="flex items-center justify-between text-sm text-slate-400">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-cyan-400 focus:ring-cyan-400">
                    Yodda saqlash
                </label>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-600 px-5 py-3 text-slate-950 font-semibold shadow-lg shadow-cyan-500/20 hover:opacity-95 transition">Kirish</button>
        </form>

        <div class="mt-8 text-center text-slate-500 text-sm">
            <p>Hali tizimda ro‘yxatdan o‘tmagansizmi? Admin bilan bog‘laning.</p>
        </div>
    </div>
</body>
</html>
