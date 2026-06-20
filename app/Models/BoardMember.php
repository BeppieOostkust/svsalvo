<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\PublicStorage;

class BoardMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'email',
        'phone',
        'description',
        'avatar',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'avatar_url',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return PublicStorage::url($this->avatar);
        }
        return null;
    }
}
