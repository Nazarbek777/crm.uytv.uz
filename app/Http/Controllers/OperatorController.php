<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OperatorController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isManager()) {
                abort(403, 'Bu bo\'lim faqat menejerlar uchun');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::query()->withCount(['leads', 'sales']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('active')) {
            $query->where('active', $request->active === '1');
        }

        $operators = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('operators.index', compact('operators'));
    }

    public function create()
    {
        return view('operators.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $request->boolean('active');
        User::create($data);
        return redirect()->route('operators.index')->with('success', 'Operator qo\'shildi');
    }

    public function show(User $operator)
    {
        $operator->loadCount(['leads', 'sales']);
        $totalRevenue = Sale::where('operator_id', $operator->id)->sum('price');
        $leadsByStatus = Lead::where('operator_id', $operator->id)
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')->pluck('c', 'status');
        $recentSales = Sale::with(['property', 'client'])->where('operator_id', $operator->id)->latest('sale_date')->limit(5)->get();
        $recentLeads = Lead::with('property')->where('operator_id', $operator->id)->latest()->limit(5)->get();

        return view('operators.show', compact('operator', 'totalRevenue', 'leadsByStatus', 'recentSales', 'recentLeads'));
    }

    public function edit(User $operator)
    {
        return view('operators.edit', compact('operator'));
    }

    public function update(Request $request, User $operator)
    {
        $data = $this->validateData($request, $operator);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $data['active'] = $request->boolean('active');
        $operator->update($data);
        return redirect()->route('operators.index')->with('success', 'Operator yangilandi');
    }

    public function destroy(User $operator)
    {
        if ($operator->id === auth()->id()) {
            return back()->withErrors(['delete' => 'O\'zingizni o\'chira olmaysiz']);
        }
        $operator->delete();
        return back()->with('success', 'Operator o\'chirildi');
    }

    private function validateData(Request $request, ?User $operator = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($operator?->id)],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:admin,manager,operator',
            'notes' => 'nullable|string',
            'password' => $operator ? 'nullable|min:6' : 'required|min:6',
        ];
        return $request->validate($rules);
    }
}
