<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'Active')->get();
        $products = Product::where('status', 'Active')->latest()->take(8)->get();
        $sliders = Slider::all();

        return view('home', compact('categories', 'products', 'sliders'));
    }

    public function products(Request $request)
    {
        $query = Product::where('status', 'Active');

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::where('status', 'Active')->get();

        return view('products', compact('products', 'categories'));
    }
}
