<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // For handling file storage
use Illuminate\Support\Facades\Auth;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $sliders = Slider::when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        return view('Slider.slider', compact('sliders', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used, handled via modal
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images/sliders'), $imageName);
            $input['image'] = 'images/sliders/' . $imageName;
        }

        $input['status'] = $request->has('status') ? 1 : 0;

        Slider::create($input);

        return redirect()->back()->with('success', 'Slider created successfully.');
    }

    /**
     * display the specified resource.
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        // Not used, handled via modal/pop-up logic if needed, or simple redirect?
        // For simplicity in this project (as per patterns), we might handle edit via modal too.
        // But standard resource routes expect a view here. Let's return the view or handle modal data injection.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $slider = Slider::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $input = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image? Optional but good practice.
             if(file_exists(public_path($slider->image))){
                unlink(public_path($slider->image));
            }

            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images/sliders'), $imageName);
            $input['image'] = 'images/sliders/' . $imageName;
        } else {
            unset($input['image']); // Don't overwrite with null if no image uploaded
        }

        $input['status'] = $request->has('status') ? 1 : 0;

        $slider->update($input);

        return redirect()->back()->with('success', 'Slider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $slider = Slider::findOrFail($id);
        if(file_exists(public_path($slider->image))){
            unlink(public_path($slider->image));
        }
        $slider->delete();

        return redirect()->back()->with('success', 'Slider deleted successfully.');
    }
}
