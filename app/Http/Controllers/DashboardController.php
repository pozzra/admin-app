<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $productCount = Product::count();
        $categoryCount = Category::count();
        $adminCount = User::where('role', 'Admin')->count();

        // Recent products for the table
        $recentProducts = Product::with('category')->latest()->take(5)->get();

        // Daily sales for chart (last 7 days)
        $dailySales = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('status', 'Completed')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard', compact('userCount', 'productCount', 'categoryCount', 'adminCount', 'recentProducts', 'dailySales'));
    }
}
