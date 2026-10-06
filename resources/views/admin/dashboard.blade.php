<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([['Pengguna', $stats['users']], ['Produk', $stats['products']], ['Pesanan', $stats['orders']], ['Pendapatan (paid)', 'Rp '.number_format($stats['revenue'], 0, ',', '.')]] as [$label, $value])
                    <div class="bg-white shadow-sm sm:rounded-lg p-4">
                        <div class="text-sm text-gray-500">{{ $label }}</div>
                        <div class="text-xl font-bold">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <h3 class="font-semibold mb-2">Pesanan Terbaru</h3>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-600"><tr><th class="py-1">#</th><th>Pelanggan</th><th>Item</th><th>Status</th><th class="text-right">Total</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach ($latestOrders as $order)
                            <tr>
                                <td class="py-1">{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->items_count }}</td>
                                <td>{{ $order->status }}</td>
                                <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <h3 class="font-semibold mb-2">Demo Eager Loading (20 produk + nama kategori)</h3>
                <ul class="text-sm list-disc ml-5">
                    <li>Lazy loading (N+1): <strong>{{ $demo['lazy'] }}</strong> query</li>
                    <li>Eager loading <code>with('category')</code>: <strong>{{ $demo['eager'] }}</strong> query</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
