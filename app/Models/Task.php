<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'user_id', 'assigned_by',
        'lead_id', 'client_id', 'due_date', 'priority', 'status', 'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const PRIORITIES = [
        'low' => ['label' => 'Past', 'color' => 'slate'],
        'normal' => ['label' => 'Oddiy', 'color' => 'blue'],
        'high' => ['label' => 'Yuqori', 'color' => 'amber'],
        'urgent' => ['label' => 'Shoshilinch', 'color' => 'red'],
    ];

    public const STATUSES = [
        'pending' => ['label' => 'Kutilmoqda', 'color' => 'slate'],
        'in_progress' => ['label' => 'Bajarilmoqda', 'color' => 'cyan'],
        'done' => ['label' => 'Bajarildi', 'color' => 'emerald'],
        'cancelled' => ['label' => 'Bekor qilindi', 'color' => 'red'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'done' && $this->status !== 'cancelled' && $this->due_date && $this->due_date->isPast() && !$this->due_date->isToday();
    }

    public function isToday(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority]['label'] ?? $this->priority;
    }

    public function priorityColor(): string
    {
        return self::PRIORITIES[$this->priority]['color'] ?? 'slate';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'slate';
    }
}
