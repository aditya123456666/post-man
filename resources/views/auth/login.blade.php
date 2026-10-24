<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login · TechStore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 font-sans text-slate-900">
    <main class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl shadow-black/20 sm:p-10">
        <a href="{{ route('home') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">← Kembali ke toko</a>
        <div class="mt-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-indigo-600">Selamat datang kembali</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Masuk ke TechStore</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Masuk untuk melanjutkan belanja atau mengelola toko.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                @error('email') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold">Kata sandi</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                @error('password') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Ingat saya
            </label>
            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Masuk</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-500">Belum punya akun pelanggan? <a href="{{ route('register') }}" class="font-bold text-indigo-700 hover:text-indigo-900">Daftar di sini</a></p>
    </main>
</body>
</html>
