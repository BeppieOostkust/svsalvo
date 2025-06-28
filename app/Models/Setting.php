<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
        'is_editable',
        'validation_rules',
        'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
        'validation_rules' => 'array',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    // Helper methods
    public function getCastedValue()
    {
        return match($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    // Static helper method to get setting value
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        return $setting ? $setting->getCastedValue() : $default;
    }

    // Static helper method to set setting value
    public static function set($key, $value)
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => is_array($value) ? json_encode($value) : $value]);
        }
        
        return $setting;
    }
}
