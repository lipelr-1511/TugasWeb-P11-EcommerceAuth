<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-900">Halo, <strong>{{ auth()->user()->name }}</strong>!
                    Anda login sebagai <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-sm">{{ auth()->user()->role }}</span>
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Lihat Katalog Produk</a>

                    @if (auth()->user()->hasRole('admin', 'editor'))
                        <a href="{{ route('products.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">+ Tambah Produk</a>
                    @endif

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm">Dashboard Admin</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
