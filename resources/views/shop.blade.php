@extends('layouts.store')

@section('title', 'Katalog')

@section('content')
    <section class="bg-indigo-700 text-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-200">TechStore</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Katalog produk</h1>
            <p class="mt-3 max-w-2xl text-indigo-100">Cari perlengkapan teknologi pilihan sesuai kebutuhan dan anggaranmu.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <form method="GET" action="{{ route('shop') }}" class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
            <div>
                <label for="search" class="sr-only">Cari produk</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>
            <div>
                <label for="min_price" class="sr-only">Harga minimum</label>
                <input id="min_price" name="min_price" type="number" min="0" value="{{ request('min_price') }}" placeholder="Harga minimum"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>
            <div>
                <label for="max_price" class="sr-only">Harga maksimum</label>
                <input id="max_price" name="max_price" type="number" min="0" value="{{ request('max_price') }}" placeholder="Harga maksimum"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>
            <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">Terapkan filter</button>
        </form>

        @include('partials.product-grid', ['products' => $products])
        @if ($products->hasPages())
            <div class="mt-8">{{ $products->links() }}</div>
        @endif
    </section>
@endsection
