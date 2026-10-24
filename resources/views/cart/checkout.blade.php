@extends('layouts.store')

@section('title', 'Konfirmasi Checkout')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
        <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">← Kembali ke keranjang</a>
        <div class="mt-5 grid gap-6 lg:grid-cols-[1fr_320px]">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-indigo-600">Langkah terakhir</p>
                <h1 class="mt-1 text-2xl font-black">Konfirmasi pesanan</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">Pesanan akan dibuat dengan metode bayar di tempat (COD). Stok akan dikurangi setelah pesanan berhasil dibuat.</p>

                <div class="mt-6 rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Pesanan atas nama</p>
                    <p class="mt-1 font-bold">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
                </div>
                <div class="mt-5">
                    <label for="shipping_address" class="mb-2 block text-sm font-semibold">Alamat pengiriman</label>
                    <textarea id="shipping_address" name="shipping_address" form="checkout-form" rows="4" minlength="10" maxlength="2000" required
                        placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan, kota, provinsi, dan kode pos"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">{{ old('shipping_address') }}</textarea>
                    @error('shipping_address') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="mt-5 rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                    <p class="font-bold text-indigo-950">Pembayaran COD</p>
                    <p class="mt-1 text-sm leading-5 text-indigo-800">Bayar tunai kepada kurir saat barang diterima. Status pesanan akan dimulai dari “Menunggu konfirmasi”.</p>
                </div>
            </div>

            <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="font-black">Ringkasan barang</h2>
                <div class="mt-4 space-y-4">
                    @foreach ($items as $item)
                        <div class="flex justify-between gap-3 text-sm">
                            <div>
                                <p class="font-semibold">{{ $item['product']->name }}</p>
                                <p class="text-xs text-slate-500">{{ $item['quantity'] }} × Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="shrink-0 font-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between border-t border-slate-100 pt-4">
                    <span class="font-bold">Total COD</span>
                    <span class="font-black text-indigo-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}" class="mt-5">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-bold text-white transition hover:bg-indigo-700">Buat pesanan COD</button>
                </form>
            </aside>
        </div>
    </section>
@endsection
