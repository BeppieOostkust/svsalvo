<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'email_template_id',
        'user_id',
        'to_email',
        'to_name',
        'subject',
        'html_content',
        'text_content',
        'status',
        'error_message',
        'sent_at',
        'variables',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'variables' => 'array',
    ];

    /**
     * Get the email template that was used
     */
    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    /**
     * Get the user who received the email
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful emails
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed emails
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending emails
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
