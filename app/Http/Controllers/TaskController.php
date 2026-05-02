<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Lead;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isManager = $user->isManager();
        $filter = $request->get('filter', 'active');

        $query = Task::with(['lead', 'client', 'assigner', 'user']);

        if ($isManager && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif (!$isManager) {
            $query->where('user_id', $user->id);
        }

        if ($filter === 'today') {
            $query->whereIn('status', ['pending', 'in_progress'])->whereDate('due_date', today());
        } elseif ($filter === 'overdue') {
            $query->whereIn('status', ['pending', 'in_progress'])->whereDate('due_date', '<', today());
        } elseif ($filter === 'done') {
            $query->where('status', 'done');
        } elseif ($filter === 'all') {
            // hammasi
        } else { // active
            $query->whereIn('status', ['pending', 'in_progress']);
        }

        $tasks = $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->paginate(20)->withQueryString();

        // Bugun aloqa kerak bo'lgan lidlar (avtomatik tasklar)
        $autoTasksQuery = Lead::with('property')->whereDate('next_follow_up', today())->whereNotIn('status', ['won', 'lost']);
        if (!$isManager) {
            $autoTasksQuery->where('operator_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $autoTasksQuery->where('operator_id', $request->user_id);
        }
        $autoTasks = $autoTasksQuery->orderBy('next_follow_up')->get();

        $counts = $this->counts($user, $isManager, $request->filled('user_id') ? (int) $request->user_id : null);
        $users = $isManager ? User::where('active', true)->orderBy('name')->get(['id', 'name']) : collect();

        return view('tasks.index', compact('tasks', 'autoTasks', 'filter', 'counts', 'users'));
    }

    public function create()
    {
        $isManager = auth()->user()->isManager();
        $users = $isManager ? User::where('active', true)->orderBy('name')->get(['id', 'name']) : collect([auth()->user()]);
        $leads = Lead::orderBy('name')->get(['id', 'name']);
        $clients = Client::orderBy('name')->get(['id', 'name']);
        return view('tasks.create', compact('users', 'leads', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['assigned_by'] = auth()->id();

        if (!auth()->user()->isManager()) {
            $data['user_id'] = auth()->id();
        }

        Task::create($data);
        return redirect()->route('tasks.index')->with('success', 'Task qo\'shildi');
    }

    public function edit(Task $task)
    {
        $this->checkAccess($task);
        $isManager = auth()->user()->isManager();
        $users = $isManager ? User::where('active', true)->orderBy('name')->get(['id', 'name']) : collect([auth()->user()]);
        $leads = Lead::orderBy('name')->get(['id', 'name']);
        $clients = Client::orderBy('name')->get(['id', 'name']);
        return view('tasks.edit', compact('task', 'users', 'leads', 'clients'));
    }

    public function update(Request $request, Task $task)
    {
        $this->checkAccess($task);
        $task->update($this->validateData($request));
        return redirect()->route('tasks.index')->with('success', 'Task yangilandi');
    }

    public function destroy(Task $task)
    {
        $this->checkAccess($task, true);
        $task->delete();
        return back()->with('success', 'Task o\'chirildi');
    }

    public function setStatus(Request $request, Task $task)
    {
        $this->checkAccess($task);
        $request->validate(['status' => 'required|in:' . implode(',', array_keys(Task::STATUSES))]);
        $update = ['status' => $request->status];
        if ($request->status === 'done') {
            $update['completed_at'] = now();
        } elseif ($task->status === 'done') {
            $update['completed_at'] = null;
        }
        $task->update($update);
        return back()->with('success', 'Task statusi yangilandi');
    }

    private function counts(User $user, bool $isManager, ?int $userId): array
    {
        $base = Task::query();
        if (!$isManager) {
            $base->where('user_id', $user->id);
        } elseif ($userId) {
            $base->where('user_id', $userId);
        }
        return [
            'active' => (clone $base)->whereIn('status', ['pending', 'in_progress'])->count(),
            'today' => (clone $base)->whereIn('status', ['pending', 'in_progress'])->whereDate('due_date', today())->count(),
            'overdue' => (clone $base)->whereIn('status', ['pending', 'in_progress'])->whereDate('due_date', '<', today())->count(),
            'done' => (clone $base)->where('status', 'done')->count(),
        ];
    }

    private function checkAccess(Task $task, bool $forDelete = false): void
    {
        $user = auth()->user();
        if ($user->isManager()) return;
        if ($task->user_id !== $user->id) abort(403);
        if ($forDelete && $task->assigned_by && $task->assigned_by !== $user->id) {
            abort(403, 'Menejer biriktirgan taskni o\'chira olmaysiz');
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'lead_id' => 'nullable|exists:leads,id',
            'client_id' => 'nullable|exists:clients,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:' . implode(',', array_keys(Task::PRIORITIES)),
            'status' => 'required|in:' . implode(',', array_keys(Task::STATUSES)),
        ]);
    }
}
