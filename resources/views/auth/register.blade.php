<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar pelanggan · TechStore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 font-sans text-slate-900">
    <main class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl shadow-black/20 sm:p-10">
        <a href="{{ route('shop') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">← Kembali ke katalog</a>
        <div class="mt-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-indigo-600">Akun pelanggan</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Buat akun</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Daftar untuk menyelesaikan pesanan dan melihat konfirmasi checkout.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="name" class="mb-2 block text-sm font-semibold">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" maxlength="255" required autofocus
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                @error('name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                @error('email') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold">Kata sandi</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter, harus mengandung huruf dan angka.</p>
                @error('password') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold">Konfirmasi kata sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>
            <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Buat akun pelanggan</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-indigo-700 hover:text-indigo-900">Masuk</a></p>
    </main>
</body>
</html>
