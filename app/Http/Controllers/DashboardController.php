<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view('dashbord', compact('userCount', 'productCount', 'categoryCount', 'adminCount', 'recentProducts'));
    }
}
