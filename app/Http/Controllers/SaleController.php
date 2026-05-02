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
        $user = auth()->user();
        $query = Sale::with(['property', 'client', 'operator']);

        if (!$user->isManager()) {
            $query->where('operator_id', $user->id);
        }

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
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'client_id' => 'nullable|exists:clients,id',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:sale,rent',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $data['operator_id'] = auth()->id();
        Sale::create($data);

        Property::where('id', $data['property_id'])->update([
            'status' => $data['type'] === 'sale' ? 'sold' : 'rent',
        ]);

        return back()->with('success', 'Savdo qayd etildi');
    }

    public function destroy(Sale $sale)
    {
        $user = auth()->user();
        if (!$user->isManager() && $sale->operator_id !== $user->id) {
            abort(403);
        }

        $sale->property?->update(['status' => 'free']);
        $sale->delete();
        return back()->with('success', 'Savdo o\'chirildi');
    }
}