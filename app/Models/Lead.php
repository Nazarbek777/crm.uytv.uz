<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'source', 'status',
        'operator_id', 'property_id', 'budget', 'notes', 'next_follow_up',
        'rooms_wanted', 'area_min', 'area_max', 'preferred_district', 'payment_method', 'urgency',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'next_follow_up' => 'date',
    ];

    public const STATUSES = [
        'new' => ['label' => 'Yangi', 'color' => 'slate'],
        'contacted' => ['label' => 'Aloqada', 'color' => 'blue'],
        'qualified' => ['label' => 'Qiziqgan', 'color' => 'cyan'],
        'negotiating' => ['label' => 'Muzokarada', 'color' => 'amber'],
        'won' => ['label' => 'Sotildi', 'color' => 'emerald'],
        'lost' => ['label' => 'Yo\'qoldi', 'color' => 'red'],
    ];

    public const SOURCES = [
        'call' => 'Qo\'ng\'iroq',
        'website' => 'Sayt',
        'referral' => 'Tavsiya',
        'social' => 'Ijtimoiy tarmoq',
        'walk_in' => 'O\'zi keldi',
        'other' => 'Boshqa',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Naqd',
        'mortgage' => 'Ipoteka',
        'installment' => 'Bo\'lib to\'lash',
        'mixed' => 'Aralash',
    ];

    public const URGENCY = [
        'immediate' => 'Hoziroq',
        '1_3_months' => '1-3 oy ichida',
        '3_6_months' => '3-6 oy ichida',
        'later' => 'Kechroq',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'slate';
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
