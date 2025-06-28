<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'weapon_type',
        'caliber',
        'rules',
        'scoring_system',
        'max_shots',
        'time_limit',
        'target_distance',
        'target_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rules' => 'array',
        'scoring_system' => 'array',
        'max_shots' => 'integer',
        'time_limit' => 'integer',
        'target_distance' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class, 'competition_type_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByWeaponType($query, $weaponType)
    {
        return $query->where('weapon_type', $weaponType);
    }

    public function scopeByCaliber($query, $caliber)
    {
        return $query->where('caliber', $caliber);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
