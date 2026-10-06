<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users'    => User::count(),
            'products' => Product::count(),
            'orders'   => Order::count(),
            'revenue'  => Order::paid()->sum('total_price'),
        ];

        $latestOrders = Order::with('user')->withCount('items')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'stats'        => $stats,
            'latestOrders' => $latestOrders,
            'demo'         => $this->eagerLoadingDemo(),
        ]);
    }

    /** Demo bonus: membandingkan jumlah query lazy loading (N+1) vs eager loading. */
    private function eagerLoadingDemo(): array
    {
        DB::enableQueryLog();
        Product::take(20)->get()->each(fn ($p) => $p->category->name);
        $lazy = count(DB::getQueryLog());
        DB::flushQueryLog();

        Product::with('category')->take(20)->get()->each(fn ($p) => $p->category->name);
        $eager = count(DB::getQueryLog());
        DB::flushQueryLog();
        DB::disableQueryLog();

        return ['lazy' => $lazy, 'eager' => $eager];
    }
}
