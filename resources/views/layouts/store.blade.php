<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TechStore') · TechStore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-lg font-black tracking-tight text-indigo-700 sm:text-2xl">TechStore</a>
            <div class="flex items-center gap-2 text-xs font-semibold sm:gap-6 sm:text-sm">
                <a href="{{ route('home') }}" class="hidden text-slate-600 transition hover:text-indigo-700 sm:inline">Beranda</a>
                <a href="{{ route('shop') }}" class="text-slate-600 transition hover:text-indigo-700">Katalog</a>
                <a href="{{ route('cart.index') }}" class="rounded-lg border border-slate-200 px-2 py-2 text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700 sm:px-3">
                    Keranjang
                    @if (array_sum(session('cart', [])) > 0)
                        <span class="ml-1 rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-800">{{ array_sum(session('cart', [])) }}</span>
                    @endif
                </a>
                @auth
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.products.index') }}" class="rounded-lg bg-indigo-600 px-2.5 py-2 text-white transition hover:bg-indigo-700 sm:px-3">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-600 transition hover:text-indigo-700">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-white transition hover:bg-indigo-700">Login</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-slate-500 sm:px-6 lg:px-8">
            © {{ now()->year }} TechStore · Toko perangkat teknologi.
        </div>
    </footer>
</body>
</html>
