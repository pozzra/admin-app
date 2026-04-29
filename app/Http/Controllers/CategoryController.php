<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
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

        $categories = Category::when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        return view('Catagory.categorise', compact('categories', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('category_images'), $imageName);
            $data['image'] = $imageName;
        }

        Category::create($data);

        // Send Telegram Notification
        $this->telegramService->sendMessage("📂🆕 <b>NEW CATEGORY CREATED</b>\n\n" .
                                           "📁 <b>Name:</b> {$data['name']}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category = Category::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image && file_exists(public_path('category_images/' . $category->image))) {
                unlink(public_path('category_images/' . $category->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('category_images'), $imageName);
            $data['image'] = $imageName;
        }

        $category->update($data);

        // Send Telegram Notification
        $this->telegramService->sendMessage("📂🔄 <b>CATEGORY UPDATED</b>\n\n" .
                                           "📁 <b>Name:</b> {$category->name}\n" .
                                           "🔔 <b>Status:</b> {$category->status}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::findOrFail($id);
        
        if ($category->image && file_exists(public_path('category_images/' . $category->image))) {
            unlink(public_path('category_images/' . $category->image));
        }

        $categoryName = $category->name;
        $category->delete();

        // Send Telegram Notification
        $this->telegramService->sendMessage("📂🗑️ <b>CATEGORY DELETED</b>\n\n" .
                                           "📁 <b>Name:</b> {$categoryName}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
