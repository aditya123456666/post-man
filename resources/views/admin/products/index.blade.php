@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-indigo-600">Dashboard</p>
            <h1 class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Manajemen inventaris</h1>
            <p class="mt-2 text-sm text-slate-500">Kelola produk yang ditampilkan pada katalog dan REST API.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
            <span class="mr-2 text-lg leading-none">+</span> Tambah produk
        </a>
    </div>

    <section class="mt-8 grid gap-4 sm:grid-cols-3" aria-label="Ringkasan toko">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total produk</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($productCount) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total pesanan</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($orderCount) }}</p>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-amber-800">Stok menipis (≤ 5)</p>
            <p class="mt-2 text-3xl font-black text-amber-950">{{ number_format($lowStockCount) }}</p>
        </article>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div>
                <h2 class="font-bold">Daftar produk</h2>
                <p class="mt-1 text-sm text-slate-500">Cari, perbarui, atau hapus produk.</p>
            </div>
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <label class="sr-only" for="search">Cari produk</label>
                <input id="search" name="search" value="{{ $search }}" placeholder="Cari nama produk..."
                    class="min-w-0 flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Cari</button>
            </form>
        </div>

        <div class="divide-y divide-slate-200 lg:hidden">
            @forelse ($products as $product)
                <article class="flex gap-4 p-4">
                    @if ($product->image)
                        <img src="{{ url('/storage/'.$product->image) }}" alt="{{ $product->name }}" class="size-20 shrink-0 rounded-xl object-cover">
                    @else
                        <div class="grid size-20 shrink-0 place-items-center rounded-xl bg-slate-100 text-xs font-bold text-slate-400">NO IMG</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="truncate font-bold">{{ $product->name }}</h3>
                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $product->stock }} stok</span>
                        </div>
                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $product->description }}</p>
                        <p class="mt-2 font-black text-indigo-700">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <div class="mt-3 flex gap-3">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-semibold text-rose-700 hover:text-rose-900">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <p class="p-8 text-center text-sm text-slate-500">Tidak ada produk yang cocok.</p>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-bold">Produk</th>
                        <th class="px-5 py-4 font-bold">Harga</th>
                        <th class="px-5 py-4 text-center font-bold">Stok</th>
                        <th class="px-5 py-4 text-right font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-4">
                                    @if ($product->image)
                                        <img src="{{ url('/storage/'.$product->image) }}" alt="{{ $product->name }}" class="size-12 rounded-lg object-cover">
                                    @else
                                        <div class="grid size-12 place-items-center rounded-lg bg-slate-100 text-[9px] font-bold text-slate-400">NO IMG</div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                        <p class="mt-1 max-w-lg truncate text-xs text-slate-500">{{ $product->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $product->stock <= 5 ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-800' }}">{{ $product->stock }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-rose-700 hover:text-rose-900">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">Tidak ada produk yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="border-t border-slate-200 px-4 py-4 sm:px-5">{{ $products->links() }}</div>
        @endif
    </section>
@endsection
