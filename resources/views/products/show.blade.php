<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->title }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <p class="text-2xl font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500">
                    Kategori: {{ $product->category->name }} · Stok: {{ $product->stock }} · Oleh: {{ $product->user?->name ?? '-' }}
                </p>
                <div>
                    @foreach ($product->tags as $tag)
                        <span class="inline-block px-2 py-0.5 mr-1 rounded bg-gray-100 text-xs">{{ $tag->name }}</span>
                    @endforeach
                </div>
                <p class="text-gray-800">{{ $product->description }}</p>

                <div class="pt-4 flex gap-3 text-sm">
                    <a href="{{ route('products.index') }}" class="text-gray-600 underline">← Kembali</a>
                    @can('update', $product)
                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 underline">Edit</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
