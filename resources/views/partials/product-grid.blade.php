<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse ($products as $product)
        <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            @if ($product->image)
                <img src="{{ url('/storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-48 w-full object-cover">
            @else
                <div class="grid h-48 w-full place-items-center bg-gradient-to-br from-indigo-50 to-slate-100 text-sm font-bold text-indigo-300">TechStore</div>
            @endif
            <div class="flex w-full flex-1 flex-col p-5">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="line-clamp-2 font-bold leading-6">{{ $product->name }}</h2>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $product->stock > 0 ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                        {{ $product->stock > 0 ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>
                <p class="mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-slate-500">{{ $product->description }}</p>
                <div class="mt-auto flex items-end justify-between gap-3 pt-5">
                    <div>
                        <p class="text-xs font-medium text-slate-400">Harga</p>
                        <p class="mt-1 font-black text-indigo-700">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-xs font-medium text-slate-500">{{ $product->stock }} stok</span>
                </div>
                <form method="POST" action="{{ route('cart.store', $product) }}" class="mt-4 flex gap-2">
                    @csrf
                    <input name="quantity" type="number" value="1" min="1" max="{{ max(1, $product->stock) }}" required aria-label="Jumlah {{ $product->name }}"
                        class="w-20 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <button type="submit" @disabled($product->stock < 1)
                        class="flex-1 rounded-lg bg-slate-900 px-3 py-2 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                        {{ $product->stock > 0 ? 'Tambah ke keranjang' : 'Stok habis' }}
                    </button>
                </form>
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <p class="font-bold">Produk belum tersedia</p>
            <p class="mt-2 text-sm text-slate-500">Coba ubah kata kunci atau filter harga.</p>
        </div>
    @endforelse
</div>
