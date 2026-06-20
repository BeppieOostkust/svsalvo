<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicStorage;

class MarketplaceListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'price',
        'condition',
        'contact_name',
        'contact_phone',
        'image',
        'images',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLatestFirst($query)
    {
        return $query->latest();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return PublicStorage::url($this->image);
    }

    public function getImageUrlsAttribute(): array
    {
        $paths = is_array($this->images) ? $this->images : [];

        if (empty($paths) && $this->image) {
            $paths = [$this->image];
        }

        return array_map(
            fn (string $path) => PublicStorage::url($path),
            $paths,
        );
    }
}
