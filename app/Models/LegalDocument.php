<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalDocument extends Model
{
    protected $fillable = [
        'type',
        'title',
        'content',
        'changes_summary',
        'version',
        'is_active',
        'effective_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($document) {
            if ($document->is_active) {
                // Deactivate other documents of the same type
                static::where('type', $document->type)
                    ->where('id', '!=', $document->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function userAcceptances(): HasMany
    {
        return $this->hasMany(UserLegalAcceptance::class);
    }

    public static function getActiveDocument(string $type): ?self
    {
        return self::where('type', $type)->where('is_active', true)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
