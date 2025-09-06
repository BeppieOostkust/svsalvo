<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_id',
        'user_id',
        'vote_type',
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
    public function scopeUpvotes($query)
    {
        return $query->where('vote_type', 'upvote');
    }

    public function scopeDownvotes($query)
    {
        return $query->where('vote_type', 'downvote');
    }
}
