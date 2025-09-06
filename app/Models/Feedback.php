<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'priority',
        'status',
        'moderator_notes',
        'admin_response',
        'user_id',
        'moderator_id',
        'is_anonymous',
        'is_featured',
        'upvotes',
        'downvotes',
        'reviewed_at',
        'resolved_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_featured' => 'boolean',
        'reviewed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FeedbackComment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(FeedbackVote::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublic($query)
    {
        return $query->whereIn('status', ['approved', 'implemented', 'under_review']);
    }

    // Helper methods
    public function getNetVotesAttribute()
    {
        return $this->upvotes - $this->downvotes;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'under_review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'implemented' => 'purple',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'idea' => 'blue',
            'feedback' => 'green',
            'suggestion' => 'purple',
            'bug_report' => 'red',
            'feature_request' => 'indigo',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }
}
