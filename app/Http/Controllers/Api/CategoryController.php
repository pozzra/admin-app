<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function show($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string', // Assuming URL or base64 for now, or handled via upload
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $category = Category::create($request->all());

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'sometimes|required|string|in:Active,Inactive',
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
