<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Katalog Produk</h2>
            @can('create', App\Models\Product::class)
                <a href="{{ route('products.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">+ Tambah Produk</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="mb-4 text-sm">
                @if (request()->boolean('in_stock'))
                    <a href="{{ route('products.index') }}" class="text-indigo-600 underline">Tampilkan semua produk</a>
                @else
                    <a href="{{ route('products.index', ['in_stock' => 1]) }}" class="text-indigo-600 underline">Hanya yang stoknya tersedia</a>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Tag</th>
                            <th class="px-4 py-3 text-right">Harga</th>
                            <th class="px-4 py-3 text-right">Stok</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-3">{{ $products->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 font-medium">
                                    <a href="{{ route('products.show', $product) }}" class="text-indigo-700 hover:underline">{{ $product->title }}</a>
                                    <div class="text-xs text-gray-500">oleh {{ $product->user?->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $product->category->name }}</td>
                                <td class="px-4 py-3">
                                    @foreach ($product->tags as $tag)
                                        <span class="inline-block px-2 py-0.5 mr-1 rounded bg-gray-100 text-xs">{{ $tag->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right {{ $product->stock === 0 ? 'text-red-600' : '' }}">{{ $product->stock }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{-- Tombol disembunyikan hanya untuk UX; keamanan sebenarnya ada di Policy + middleware --}}
                                    @can('update', $product)
                                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('delete', $product)
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-2 text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
