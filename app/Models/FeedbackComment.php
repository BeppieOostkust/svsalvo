<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'feedback_id',
        'user_id',
        'is_moderator_comment',
        'is_internal',
    ];

    protected $casts = [
        'is_moderator_comment' => 'boolean',
        'is_internal' => 'boolean',
    ];

    // Relationships
    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeModeratorComments($query)
    {
        return $query->where('is_moderator_comment', true);
    }
}
