<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Lead;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private const LEAD_TO_TASK_STATUS = [
        'new' => 'pending',
        'contacted' => 'in_progress',
        'qualified' => 'in_progress',
        'negotiating' => 'in_progress',
        'won' => 'done',
        'lost' => 'cancelled',
    ];

    private const TASK_TO_LEAD_STATUS = [
        'pending' => 'new',
        'in_progress' => 'contacted',
        'done' => 'won',
        'cancelled' => 'lost',
    ];

    public function index(Request $request)
    {
        $user = auth()->user();
        $isManager = $user->isManager();

        $taskQuery = Task::with(['lead', 'client', 'assigner', 'user', 'comments.user']);
        $leadQuery = Lead::with(['operator', 'property', 'comments.user']);

        if (!$isManager) {
            $taskQuery->where('user_id', $user->id);
            $leadQuery->where('operator_id', $user->id);
        }

        $tasks = (clone $taskQuery)
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->orderByDesc('id')
            ->get();

        $leads = (clone $leadQuery)->orderByDesc('id')->get();

        $allUsers = $isManager ? User::where('active', true)->orderBy('name')->get(['id', 'name']) : collect([$user]);
        $allLeads = Lead::orderBy('name')->get(['id', 'name']);
        $allClients = Client::orderBy('name')->get(['id', 'name']);

        return view('tasks.index', [
            'tasks' => $tasks,
            'leads' => $leads,
            'users' => $allUsers,
            'allLeads' => $allLeads,
            'allClients' => $allClients,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['assigned_by'] = auth()->id();
        if (!auth()->user()->isManager()) {
            $data['user_id'] = auth()->id();
        }

        $task = Task::create($data);
        $task->load(['lead', 'client', 'assigner', 'user', 'comments.user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serializeTask($task)]);
        }
        return redirect()->route('tasks.index')->with('success', 'Task qo\'shildi');
    }

    public function update(Request $request, Task $task)
    {
        $this->checkAccess($task);
        $task->update($this->validateData($request));
        $task->load(['lead', 'client', 'assigner', 'user', 'comments.user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serializeTask($task)]);
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

        // Card type: task or lead, but route is the same. Let's accept both via leads endpoint too.
        $request->validate(['status' => 'required|in:' . implode(',', array_keys(Task::STATUSES))]);

        $update = ['status' => $request->status];
        if ($request->status === 'done') {
            $update['completed_at'] = now();
        } elseif ($task->status === 'done') {
            $update['completed_at'] = null;
        }
        $task->update($update);
        $task->load(['lead', 'client', 'assigner', 'user', 'comments.user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serializeTask($task)]);
        }
        return back()->with('success', 'Task statusi yangilandi');
    }

    public function setPriority(Request $request, Task $task)
    {
        $this->checkAccess($task);
        $request->validate(['priority' => 'required|in:' . implode(',', array_keys(Task::PRIORITIES))]);
        $task->update(['priority' => $request->priority]);
        $task->load(['lead', 'client', 'assigner', 'user', 'comments.user']);

        if ($request->wantsJson()) {
            return response()->json(['task' => $this->serializeTask($task)]);
        }
        return back()->with('success', 'Prioritet o\'zgartirildi');
    }

    public function setLeadStatus(Request $request, Lead $lead)
    {
        $user = auth()->user();
        if (!$user->isManager() && $lead->operator_id !== $user->id) abort(403);

        $request->validate(['task_status' => 'required|in:' . implode(',', array_keys(Task::STATUSES))]);
        $newLeadStatus = self::TASK_TO_LEAD_STATUS[$request->task_status] ?? 'new';

        // Aqlli mapping: agar lid hozir contacted/qualified/negotiating bo'lsa va target = in_progress bo'lsa, o'zgartirmaymiz
        if ($request->task_status === 'in_progress' && in_array($lead->status, ['contacted', 'qualified', 'negotiating'])) {
            $newLeadStatus = $lead->status;
        }

        $lead->update(['status' => $newLeadStatus]);
        $lead->load(['operator', 'property', 'comments.user']);

        if ($request->wantsJson()) {
            return response()->json(['lead' => $this->serializeLead($lead)]);
        }
        return back()->with('success', 'Lid statusi yangilandi');
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

    public function serializeTask(Task $t): array
    {
        return [
            'kind' => 'task',
            'id' => $t->id,
            'title' => $t->title,
            'description' => $t->description,
            'user_id' => $t->user_id,
            'user_name' => $t->user?->name,
            'assigner_name' => $t->assigner?->name,
            'assigned_by' => $t->assigned_by,
            'lead_id' => $t->lead_id,
            'lead_name' => $t->lead?->name,
            'lead_phone' => $t->lead?->phone,
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
            'comments_count' => $t->comments->count(),
            'comments' => $t->comments->map(fn($c) => [
                'id' => $c->id, 'content' => $c->content,
                'user_id' => $c->user_id, 'user_name' => $c->user?->name,
                'created_human' => $c->created_at->diffForHumans(),
            ])->values()->all(),
        ];
    }

    public function serializeLead(Lead $l): array
    {
        $taskStatus = self::LEAD_TO_TASK_STATUS[$l->status] ?? 'pending';
        return [
            'kind' => 'lead',
            'id' => $l->id,
            'title' => $l->name,
            'description' => null,
            'phone' => $l->phone,
            'phone_clean' => preg_replace('/\s+/', '', $l->phone ?? ''),
            'budget' => (float) $l->budget,
            'rooms_wanted' => $l->rooms_wanted,
            'preferred_district' => $l->preferred_district,
            'payment_method' => $l->payment_method,
            'payment_label' => Lead::PAYMENT_METHODS[$l->payment_method] ?? null,
            'urgency' => $l->urgency,
            'lead_status' => $l->status,
            'lead_status_label' => $l->statusLabel(),
            'user_id' => $l->operator_id,
            'user_name' => $l->operator?->name,
            'property_title' => $l->property?->title,
            'due_date' => $l->next_follow_up?->format('Y-m-d'),
            'due_date_human' => $l->next_follow_up?->format('d.m.Y'),
            'priority' => $l->urgency === 'immediate' ? 'urgent' : ($l->urgency === '1_3_months' ? 'high' : 'normal'),
            'priority_label' => $l->urgency === 'immediate' ? 'Shoshilinch' : ($l->urgency === '1_3_months' ? 'Yuqori' : 'Oddiy'),
            'priority_color' => $l->urgency === 'immediate' ? 'red' : ($l->urgency === '1_3_months' ? 'amber' : 'blue'),
            'status' => $taskStatus,
            'is_overdue' => $l->next_follow_up && $l->next_follow_up->isPast() && !$l->next_follow_up->isToday() && !in_array($l->status, ['won', 'lost']),
            'comments_count' => $l->comments->count(),
            'comments' => $l->comments->map(fn($c) => [
                'id' => $c->id, 'content' => $c->content,
                'user_id' => $c->user_id, 'user_name' => $c->user?->name,
                'created_human' => $c->created_at->diffForHumans(),
            ])->values()->all(),
        ];
    }
}
