@extends('layouts.store')

@section('title', 'Beranda')

@section('content')
    <section class="overflow-hidden bg-slate-950 text-white">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-24">
            <div>
                <span class="inline-flex rounded-full border border-indigo-400/30 bg-indigo-400/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-indigo-200">Teknologi pilihan</span>
                <h1 class="mt-6 max-w-2xl text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">Perangkat terbaik, siap menemani produktivitasmu.</h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">Temukan laptop, komponen, dan aksesori teknologi pilihan dengan informasi stok yang selalu diperbarui.</p>
                <a href="{{ route('shop') }}" class="mt-8 inline-flex rounded-xl bg-indigo-500 px-6 py-3.5 font-bold text-white transition hover:bg-indigo-400">Jelajahi katalog <span class="ml-2">→</span></a>
            </div>
            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-500/20 to-sky-400/10 p-6 sm:p-10">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-200">Belanja lebih nyaman</p>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="rounded-2xl bg-white/10 p-4 sm:p-5"><p class="text-2xl font-black">01</p><p class="mt-2 text-sm text-slate-300">Katalog produk</p></div>
                    <div class="rounded-2xl bg-white/10 p-4 sm:p-5"><p class="text-2xl font-black">02</p><p class="mt-2 text-sm text-slate-300">Stok transparan</p></div>
                    <div class="rounded-2xl bg-white/10 p-4 sm:p-5"><p class="text-2xl font-black">03</p><p class="mt-2 text-sm text-slate-300">API terintegrasi</p></div>
                    <div class="rounded-2xl bg-white/10 p-4 sm:p-5"><p class="text-2xl font-black">04</p><p class="mt-2 text-sm text-slate-300">Admin aman</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <div class="mb-7 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">Pilihan untukmu</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Produk unggulan</h2>
            </div>
            <a href="{{ route('shop') }}" class="shrink-0 text-sm font-bold text-indigo-700 hover:text-indigo-900">Lihat semua →</a>
        </div>
        @include('partials.product-grid', ['products' => $products])
    </section>
@endsection
