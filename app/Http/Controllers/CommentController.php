<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\Lead;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:task,lead',
            'id' => 'required|integer',
            'content' => 'required|string|max:5000',
        ]);

        $model = $data['type'] === 'task' ? Task::findOrFail($data['id']) : Lead::findOrFail($data['id']);
        $this->authorizeCommentable($model);

        $comment = $model->comments()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ])->load('user');

        if ($request->wantsJson()) {
            return response()->json(['comment' => $this->serialize($comment)]);
        }
        return back()->with('success', 'Komment qo\'shildi');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->isManager()) {
            abort(403);
        }
        $comment->delete();
        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Komment o\'chirildi');
    }

    private function authorizeCommentable($model): void
    {
        $user = auth()->user();
        if ($user->isManager()) return;

        if ($model instanceof Task && $model->user_id !== $user->id) abort(403);
        if ($model instanceof Lead && $model->operator_id !== $user->id) abort(403);
    }

    private function serialize(Comment $c): array
    {
        return [
            'id' => $c->id,
            'content' => $c->content,
            'user_id' => $c->user_id,
            'user_name' => $c->user?->name,
            'created_at' => $c->created_at->toIso8601String(),
            'created_human' => $c->created_at->diffForHumans(),
        ];
    }
}
