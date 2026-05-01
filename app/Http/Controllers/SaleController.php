<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Property;
use App\Models\Client;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['property', 'client']);
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        $sales = $query->orderByDesc('sale_date')->get();
        $properties = Property::where('status', 'free')->orderBy('title')->get();
        $clients = Client::orderBy('name')->get();
        
        return view('sales.index', compact('sales', 'properties', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'client_id' => 'nullable|exists:clients,id',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:sale,rent',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $sale = Sale::create($request->all());
        
        // Update property status
        $property = Property::find($request->property_id);
        $property->update(['status' => $request->type === 'sale' ? 'sold' : 'rent']);
        
        return back()->with('success', 'Savdo qayd etildi');
    }

    public function destroy(Sale $sale)
    {
        // Restore property status
        $property = $sale->property;
        $property->update(['status' => 'free']);
        
        $sale->delete();
        return back()->with('success', 'Savdo o\'chirildi');
    }
}