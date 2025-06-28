<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'status',
        'notes',
        'registered_at',
        'paid_amount',
        'payment_confirmed',
        'additional_data',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'paid_amount' => 'decimal:2',
        'payment_confirmed' => 'boolean',
        'additional_data' => 'array',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'bevestigd');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_confirmed', true);
    }
}
