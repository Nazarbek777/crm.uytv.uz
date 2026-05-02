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

        $base = Task::with(['lead', 'client', 'assigner', 'user']);
        if ($isManager && $request->filled('user_id')) {
            $base->where('user_id', $request->user_id);
        } elseif (!$isManager) {
            $base->where('user_id', $user->id);
        }

        $tasks = (clone $base)
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 1 WHEN 'pending' THEN 2 WHEN 'done' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->orderByDesc('id')
            ->get();

        // Bugun aloqa kerak bo'lgan lidlar (avtomatik tasklar)
        $autoTasksQuery = Lead::with('property')->whereDate('next_follow_up', today())->whereNotIn('status', ['won', 'lost']);
        if (!$isManager) {
            $autoTasksQuery->where('operator_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $autoTasksQuery->where('operator_id', $request->user_id);
        }
        $autoTasks = $autoTasksQuery->orderBy('next_follow_up')->get();

        $users = $isManager ? User::where('active', true)->orderBy('name')->get(['id', 'name']) : collect([$user]);
        $leads = Lead::orderBy('name')->get(['id', 'name']);
        $clients = Client::orderBy('name')->get(['id', 'name']);

        if ($request->wantsJson()) {
            return response()->json([
                'tasks' => $tasks->map(fn($t) => $this->serialize($t)),
                'auto_tasks' => $autoTasks->map(fn($l) => $this->serializeLead($l)),
            ]);
        }

        return view('tasks.index', compact('tasks', 'autoTasks', 'users', 'leads', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['assigned_by'] = auth()->id();
        if (!auth()->user()->isManager()) {
            $data['user_id'] = auth()->id();
        }

        $task = Task::create($data);
        $task->load(['lead', 'client', 'assigner', 'user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serialize($task)]);
        }
        return redirect()->route('tasks.index')->with('success', 'Task qo\'shildi');
    }

    public function update(Request $request, Task $task)
    {
        $this->checkAccess($task);
        $task->update($this->validateData($request));
        $task->load(['lead', 'client', 'assigner', 'user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serialize($task)]);
        }
        return redirect()->route('tasks.index')->with('success', 'Task yangilandi');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->checkAccess($task, true);
        $task->delete();
        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
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
        $task->load(['lead', 'client', 'assigner', 'user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serialize($task)]);
        }
        return back()->with('success', 'Task statusi yangilandi');
    }

    public function setPriority(Request $request, Task $task)
    {
        $this->checkAccess($task);
        $request->validate(['priority' => 'required|in:' . implode(',', array_keys(Task::PRIORITIES))]);
        $task->update(['priority' => $request->priority]);
        $task->load(['lead', 'client', 'assigner', 'user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serialize($task)]);
        }
        return back()->with('success', 'Prioritet o\'zgartirildi');
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

    private function serialize(Task $t): array
    {
        return [
            'id' => $t->id,
            'title' => $t->title,
            'description' => $t->description,
            'user_id' => $t->user_id,
            'user_name' => $t->user?->name,
            'assigner_name' => $t->assigner?->name,
            'assigned_by' => $t->assigned_by,
            'lead_id' => $t->lead_id,
            'lead_name' => $t->lead?->name,
            'client_id' => $t->client_id,
            'client_name' => $t->client?->name,
            'due_date' => $t->due_date?->format('Y-m-d'),
            'due_date_human' => $t->due_date?->format('d.m.Y'),
            'priority' => $t->priority,
            'priority_label' => $t->priorityLabel(),
            'priority_color' => $t->priorityColor(),
            'status' => $t->status,
            'status_label' => $t->statusLabel(),
            'status_color' => $t->statusColor(),
            'is_overdue' => $t->isOverdue(),
            'completed_at' => $t->completed_at?->toIso8601String(),
            'created_at' => $t->created_at->toIso8601String(),
        ];
    }

    private function serializeLead(Lead $l): array
    {
        return [
            'id' => $l->id,
            'name' => $l->name,
            'phone' => $l->phone,
            'phone_clean' => preg_replace('/\s+/', '', $l->phone ?? ''),
            'status' => $l->status,
            'status_label' => $l->statusLabel(),
            'budget' => $l->budget,
            'rooms_wanted' => $l->rooms_wanted,
            'payment_method' => $l->payment_method,
            'payment_label' => Lead::PAYMENT_METHODS[$l->payment_method] ?? null,
        ];
    }
}
