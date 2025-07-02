<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLegalAcceptance extends Model
{
    protected $fillable = [
        'user_id',
        'legal_document_id',
        'version_accepted',
        'accepted_at',
        'ip_address',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function legalDocument(): BelongsTo
    {
        return $this->belongsTo(LegalDocument::class);
    }
}
