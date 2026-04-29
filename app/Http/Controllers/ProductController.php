<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        $categories = Category::all();
        return view('Products.products', compact('products', 'categories', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('product_images'), $imageName);
            $input['image'] = $imageName;
        }

        Product::create($input);

        // Send Telegram Notification
        $this->telegramService->sendMessage("🆕 <b>NEW PRODUCT CREATED</b>\n\n" .
                                           "📦 <b>Name:</b> {$input['name']}\n" .
                                           "💵 <b>Price:</b> $" . number_format($input['price'], 2) . "\n" .
                                           "📊 <b>Stock:</b> {$input['stock']}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);

        $product = Product::findOrFail($id);
        $input = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && file_exists(public_path('product_images/' . $product->image))) {
                unlink(public_path('product_images/' . $product->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('product_images'), $imageName);
            $input['image'] = $imageName;
        }

        $product->update($input);

        // Send Telegram Notification
        $this->telegramService->sendMessage("🔄 <b>PRODUCT UPDATED</b>\n\n" .
                                           "📦 <b>Name:</b> {$product->name}\n" .
                                           "💵 <b>Price:</b> $" . number_format($product->price, 2) . "\n" .
                                           "🔔 <b>Status:</b> {$product->status}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $product = Product::findOrFail($id);

        if ($product->image && file_exists(public_path('product_images/' . $product->image))) {
            unlink(public_path('product_images/' . $product->image));
        }

        $productName = $product->name;
        $product->delete();

        // Send Telegram Notification
        $this->telegramService->sendMessage("🗑️ <b>PRODUCT DELETED</b>\n\n" .
                                           "📦 <b>Name:</b> {$productName}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
