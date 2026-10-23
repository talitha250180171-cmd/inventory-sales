<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'totalProducts' => Product::count(),
            'lowStockProducts' => Product::whereColumn('stock', '<=', 'min_stock')->get(),
        ]);
    }
}