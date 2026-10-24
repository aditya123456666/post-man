<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · TechStore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-800 bg-slate-950 text-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('admin.products.index') }}" class="text-lg font-black tracking-tight sm:text-xl">TechStore <span class="text-indigo-400">Admin</span></a>
            <div class="flex items-center gap-3 sm:gap-5">
                <span class="hidden text-sm text-slate-300 sm:inline">{{ auth()->user()->name }}</span>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-300 transition hover:text-white">Lihat toko</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold transition hover:bg-slate-800">Keluar</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        @if (session('status'))
            <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->has('product'))
            <div role="alert" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ $errors->first('product') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
