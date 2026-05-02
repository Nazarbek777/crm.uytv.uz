<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Investor;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with('investor');
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->investor_id) {
            $query->where('investor_id', $request->investor_id);
        }
        
        $properties = $query->orderByDesc('id')->paginate(10);
        $investors = Investor::orderBy('name')->get();
        
        return view('properties.index', compact('properties', 'investors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:free,sold,rent',
            'rooms' => 'nullable|integer|min:1',
            'floor' => 'nullable|integer|min:0',
            'total_floors' => 'nullable|integer|min:0',
            'area' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'investor_id' => 'nullable|exists:investors,id',
        ]);

        Property::create($request->all());
        return back()->with('success', 'Uy qo\'shildi');
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:free,sold,rent',
            'rooms' => 'nullable|integer|min:1',
            'floor' => 'nullable|integer|min:0',
            'total_floors' => 'nullable|integer|min:0',
            'area' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'investor_id' => 'nullable|exists:investors,id',
        ]);

        $property->update($request->all());
        return back()->with('success', 'Uy yangilandi');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return back()->with('success', 'Uy o\'chirildi');
    }
}