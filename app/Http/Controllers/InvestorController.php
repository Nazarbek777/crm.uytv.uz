<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $query = Investor::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $investors = $query->orderBy('name')->paginate(10);
        return view('investors.index', compact('investors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        Investor::create($request->all());
        return back()->with('success', 'Investor qo\'shildi');
    }

    public function update(Request $request, Investor $investor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $investor->update($request->all());
        return back()->with('success', 'Investor yangilandi');
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();
        return back()->with('success', 'Investor o\'chirildi');
    }
}