<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        return response()->json(Slider::all());
    }

    public function show($id)
    {
        return response()->json(Slider::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|string',
            'status' => 'boolean',
        ]);

        $slider = Slider::create($request->all());

        return response()->json($slider, 201);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'sometimes|required|string',
            'status' => 'boolean',
        ]);

        $slider->update($request->all());

        return response()->json($slider);
    }

    public function destroy($id)
    {
        Slider::findOrFail($id)->delete();

        return response()->json(['message' => 'Slider deleted successfully']);
    }
}
