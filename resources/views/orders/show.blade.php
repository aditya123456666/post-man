@extends('layouts.store')

@section('title', 'Pesanan diterima')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
        <div class="rounded-3xl border border-emerald-200 bg-white p-6 text-center shadow-sm sm:p-10">
            <div class="mx-auto grid size-16 place-items-center rounded-full bg-emerald-100 text-3xl font-black text-emerald-700">✓</div>
            <p class="mt-5 text-sm font-bold uppercase tracking-[0.16em] text-emerald-700">Pesanan berhasil</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Terima kasih, {{ auth()->user()->name }}!</h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">Pesanan #{{ $order->id }} sudah kami terima dan menunggu konfirmasi admin. Pembayaran dilakukan saat barang tiba (COD).</p>

            <div class="mt-8 rounded-2xl bg-slate-50 p-5 text-left">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
                    <span class="text-sm text-slate-500">Status</span>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">Menunggu konfirmasi</span>
                </div>
                <div class="border-b border-slate-200 py-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Alamat pengiriman</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-6">{{ $order->shipping_address }}</p>
                </div>
                <div class="mt-4 space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <div>
                                <p class="font-semibold">{{ $item->product?->name ?? 'Produk #' . $item->product_id }}</p>
                                <p class="text-slate-500">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="shrink-0 font-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between border-t border-slate-200 pt-4 font-black">
                    <span>Total COD</span>
                    <span class="text-indigo-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
            <a href="{{ route('shop') }}" class="mt-7 inline-flex rounded-xl bg-indigo-600 px-6 py-3 font-bold text-white transition hover:bg-indigo-700">Lanjut belanja</a>
        </div>
    </section>
@endsection
