<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Lead;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $filter = $request->get('filter', 'all');

        $query = Reminder::with(['lead', 'client', 'user']);

        if (!auth()->user()->isManager()) {
            $query->where('user_id', $userId);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($filter === 'today') {
            $query->whereDate('remind_at', today())->where('completed', false);
        } elseif ($filter === 'overdue') {
            $query->where('remind_at', '<', now())->where('completed', false);
        } elseif ($filter === 'upcoming') {
            $query->where('remind_at', '>', now())->where('completed', false);
        } elseif ($filter === 'completed') {
            $query->where('completed', true);
        }

        $reminders = $query->orderBy('remind_at')->paginate(20)->withQueryString();
        $users = auth()->user()->isManager() ? User::where('active', true)->orderBy('name')->get() : collect();

        $counts = [
            'today' => Reminder::where('user_id', $userId)->whereDate('remind_at', today())->where('completed', false)->count(),
            'overdue' => Reminder::where('user_id', $userId)->where('remind_at', '<', now())->where('completed', false)->count(),
            'upcoming' => Reminder::where('user_id', $userId)->where('remind_at', '>', now())->where('completed', false)->count(),
        ];

        return view('reminders.index', compact('reminders', 'filter', 'counts', 'users'));
    }

    public function create(Request $request)
    {
        $leads = Lead::orderBy('name')->get(['id', 'name']);
        $clients = Client::orderBy('name')->get(['id', 'name']);
        $users = User::where('active', true)->orderBy('name')->get(['id', 'name']);
        return view('reminders.create', compact('leads', 'clients', 'users'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        Reminder::create($data);
        return redirect()->route('reminders.index')->with('success', 'Eslatma yaratildi');
    }

    public function edit(Reminder $reminder)
    {
        $this->authorize($reminder);
        $leads = Lead::orderBy('name')->get(['id', 'name']);
        $clients = Client::orderBy('name')->get(['id', 'name']);
        $users = User::where('active', true)->orderBy('name')->get(['id', 'name']);
        return view('reminders.edit', compact('reminder', 'leads', 'clients', 'users'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorize($reminder);
        $reminder->update($this->validateData($request));
        return redirect()->route('reminders.index')->with('success', 'Eslatma yangilandi');
    }

    public function complete(Reminder $reminder)
    {
        $this->authorize($reminder);
        $reminder->update(['completed' => true, 'completed_at' => now()]);
        return back()->with('success', 'Eslatma bajarilgan deb belgilandi');
    }

    public function uncomplete(Reminder $reminder)
    {
        $this->authorize($reminder);
        $reminder->update(['completed' => false, 'completed_at' => null]);
        return back()->with('success', 'Eslatma qaytadan ochildi');
    }

    public function destroy(Reminder $reminder)
    {
        $this->authorize($reminder);
        $reminder->delete();
        return back()->with('success', 'Eslatma o\'chirildi');
    }

    private function authorize(Reminder $reminder): void
    {
        if ($reminder->user_id !== auth()->id() && !auth()->user()->isManager()) {
            abort(403);
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remind_at' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
            'lead_id' => 'nullable|exists:leads,id',
            'client_id' => 'nullable|exists:clients,id',
        ]);
    }
}
