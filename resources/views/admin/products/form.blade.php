@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Produk' : 'Tambah Produk')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">← Kembali ke inventaris</a>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-indigo-600">Inventaris</p>
                <h1 class="mt-1 text-2xl font-black tracking-tight">{{ $isEditing ? 'Perbarui produk' : 'Tambah produk baru' }}</h1>
                <p class="mt-2 text-sm text-slate-500">Informasi ini akan ditampilkan pada katalog dan API.</p>
            </div>

            <form method="POST" action="{{ $isEditing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="mt-8 space-y-5">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold">Nama produk</label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" maxlength="255" required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    @error('name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="mb-2 block text-sm font-semibold">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" maxlength="5000"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="price" class="mb-2 block text-sm font-semibold">Harga (Rp)</label>
                        <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        @error('price') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stock" class="mb-2 block text-sm font-semibold">Stok</label>
                        <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        @error('stock') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="image" class="mb-2 block text-sm font-semibold">Foto produk <span class="font-normal text-slate-500">(JPG, PNG, WEBP · maks. 5 MB)</span></label>
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(! $isEditing)
                        class="block w-full rounded-xl border border-slate-300 p-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('image') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                    @if ($product->image)
                        <img src="{{ url('/storage/'.$product->image) }}" alt="{{ $product->name }}" class="mt-4 size-24 rounded-xl object-cover">
                    @endif
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">{{ $isEditing ? 'Simpan perubahan' : 'Tambah produk' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
