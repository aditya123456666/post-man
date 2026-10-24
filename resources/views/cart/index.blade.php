@extends('layouts.store')

@section('title', 'Keranjang')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">Belanja</p>
        <h1 class="mt-1 text-3xl font-black tracking-tight">Keranjang belanja</h1>

        @if (session('status'))
            <div role="status" class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @error('cart')
            <div role="alert" class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ $message }}</div>
        @enderror

        @if ($items === [])
            <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                <h2 class="text-lg font-bold">Keranjang masih kosong</h2>
                <p class="mt-2 text-sm text-slate-500">Pilih produk yang kamu suka, lalu tambahkan ke keranjang.</p>
                <a href="{{ route('shop') }}" class="mt-6 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">Lanjut belanja</a>
            </div>
        @else
            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="divide-y divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    @foreach ($items as $item)
                        <article class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-5">
                            @if ($item['product']->image)
                                <img src="{{ url('/storage/'.$item['product']->image) }}" alt="{{ $item['product']->name }}" class="h-36 w-full rounded-xl bg-slate-100 object-cover sm:h-24 sm:w-28">
                            @else
                                <div class="grid h-36 w-full place-items-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-300 sm:h-24 sm:w-28">TechStore</div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h2 class="font-bold">{{ $item['product']->name }}</h2>
                                <p class="mt-1 text-sm font-semibold text-indigo-700">Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                                <p class="mt-1 text-xs text-slate-500">Stok tersedia: {{ $item['product']->stock }}</p>
                                @if ($item['quantity'] > $item['product']->stock)
                                    <p class="mt-2 text-xs font-semibold text-rose-700">Stok berubah, perbarui jumlah sebelum checkout.</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-3 sm:flex-col sm:items-end">
                                <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="quantity-{{ $item['product']->id }}">Jumlah</label>
                                    <input id="quantity-{{ $item['product']->id }}" name="quantity" type="number" min="1" max="{{ max(1, $item['product']->stock) }}" value="{{ $item['quantity'] }}" required
                                        class="w-20 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    <button class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold transition hover:bg-slate-50">Ubah</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-rose-700 hover:text-rose-900">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-black">Ringkasan pesanan</h2>
                    <div class="mt-5 flex justify-between gap-4 border-t border-slate-100 pt-4 text-sm">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-3 text-xs leading-5 text-slate-500">Pembayaran dilakukan saat barang diterima (COD). Pesanan menunggu konfirmasi admin.</p>
                    @auth
                        <a href="{{ route('checkout.create') }}" class="mt-5 block rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-indigo-700">Lanjut checkout</a>
                    @else
                        <a href="{{ route('login') }}" class="mt-5 block rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-indigo-700">Login untuk checkout</a>
                        <a href="{{ route('register') }}" class="mt-3 block text-center text-sm font-semibold text-indigo-700 hover:text-indigo-900">Belum punya akun? Daftar</a>
                    @endauth
                </aside>
            </div>
        @endif
    </section>
@endsection
