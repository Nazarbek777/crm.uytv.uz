<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'pipeline');
        $user = auth()->user();

        $query = Lead::with(['operator', 'property']);

        if (!$user->isManager()) {
            $query->where('operator_id', $user->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('operator_id') && $user->isManager()) {
            $query->where('operator_id', $request->operator_id);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($view === 'list' && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        $operators = User::where('active', true)->orderBy('name')->get(['id', 'name']);

        if ($view === 'pipeline') {
            $leads = $query->orderByDesc('id')->get()->groupBy('status');
            return view('leads.pipeline', compact('leads', 'operators'));
        }

        $leads = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('leads.index', compact('leads', 'operators'));
    }

    public function create()
    {
        $operators = User::where('active', true)->orderBy('name')->get();
        $properties = Property::where('status', 'free')->orderBy('title')->get();
        return view('leads.create', compact('operators', 'properties'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if (empty($data['operator_id']) || !auth()->user()->isManager()) {
            $data['operator_id'] = auth()->id();
        }
        Lead::create($data);
        return redirect()->route('leads.index')->with('success', 'Lid qo\'shildi');
    }

    public function show(Lead $lead)
    {
        $this->authorize($lead);
        $lead->load(['operator', 'property', 'reminders.user']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->authorize($lead);
        $operators = User::where('active', true)->orderBy('name')->get();
        $properties = Property::orderBy('title')->get();
        return view('leads.edit', compact('lead', 'operators', 'properties'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize($lead);
        $data = $this->validateData($request);
        $lead->update($data);
        return redirect()->route('leads.show', $lead)->with('success', 'Lid yangilandi');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize($lead);
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lid o\'chirildi');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $this->authorize($lead);
        $request->validate(['status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES))]);
        $lead->update(['status' => $request->status]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Status yangilandi');
    }

    private function authorize(Lead $lead): void
    {
        $user = auth()->user();
        if (!$user->isManager() && $lead->operator_id !== $user->id) {
            abort(403, 'Sizga bu lid biriktirilmagan');
        }
    }

    public function convert(Request $request, Lead $lead)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'type' => 'required|in:sale,rent',
            'property_id' => 'required|exists:properties,id',
        ]);

        DB::transaction(function () use ($request, $lead) {
            $client = Client::create([
                'name' => $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'notes' => $lead->notes,
            ]);

            Sale::create([
                'property_id' => $request->property_id,
                'client_id' => $client->id,
                'operator_id' => $lead->operator_id ?? auth()->id(),
                'price' => $request->price,
                'type' => $request->type,
                'sale_date' => $request->sale_date,
                'notes' => 'Lid #' . $lead->id . ' dan',
            ]);

            Property::where('id', $request->property_id)->update(['status' => $request->type === 'rent' ? 'rent' : 'sold']);

            $lead->update(['status' => 'won']);
        });

        return redirect()->route('leads.index')->with('success', 'Lid muvaffaqiyatli savdoga aylantirildi');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'source' => 'required|in:' . implode(',', array_keys(Lead::SOURCES)),
            'status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES)),
            'operator_id' => 'nullable|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'budget' => 'nullable|numeric|min:0',
            'rooms_wanted' => 'nullable|integer|min:1|max:10',
            'area_min' => 'nullable|integer|min:0',
            'area_max' => 'nullable|integer|min:0',
            'preferred_district' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:' . implode(',', array_keys(Lead::PAYMENT_METHODS)),
            'urgency' => 'nullable|in:' . implode(',', array_keys(Lead::URGENCY)),
            'notes' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
        ]);
    }
}
