<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Admin melihat semua transaksi, staff hanya miliknya
        $sales = fn () => Sale::where('status', 'completed')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        $purchases = fn () => Purchase::where('status', 'completed')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        return view('dashboard', [
            'totalProducts' => Product::count(),
            'lowStockProducts' => Product::whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock')
                ->get(),
            'salesCount' => $sales()->count(),
            'salesTotal' => $sales()->sum('total_amount'),
            'purchasesCount' => $purchases()->count(),
            'purchasesTotal' => $purchases()->sum('total_amount'),
            'recentSales' => Sale::with('user')
                ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}