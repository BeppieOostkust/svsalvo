<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'access_level',
        'is_public',
        'requires_login',
        'download_count',
        'allowed_roles',
        'uploaded_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'requires_login' => 'boolean',
        'download_count' => 'integer',
        'file_size' => 'integer',
        'allowed_roles' => 'array',
        'access_level' => 'string',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Automatically extract file metadata when file_path is set
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($download) {
            // Handle access level logic
            if ($download->isDirty('access_level')) {
                switch ($download->access_level) {
                    case 'public':
                        $download->is_public = true;
                        $download->requires_login = false;
                        $download->allowed_roles = [];
                        break;
                    case 'members':
                        $download->is_public = false;
                        $download->requires_login = true;
                        $download->allowed_roles = [];
                        break;
                    case 'roles':
                        $download->is_public = false;
                        $download->requires_login = true;
                        // Keep existing allowed_roles
                        break;
                }
            }
            
            // Handle file metadata extraction
            if ($download->isDirty('file_path') && $download->file_path) {
                $filePath = storage_path('app/public/' . $download->file_path);
                
                if (file_exists($filePath)) {
                    // Extract file name if not set
                    if (!$download->file_name) {
                        $download->file_name = basename($download->file_path);
                    }
                    
                    // Extract file size if not set
                    if (!$download->file_size) {
                        $download->file_size = filesize($filePath);
                    }
                    
                    // Extract file type if not set
                    if (!$download->file_type) {
                        $download->file_type = mime_content_type($filePath) ?: 'application/octet-stream';
                    }
                }
            }
        });
    }
}
