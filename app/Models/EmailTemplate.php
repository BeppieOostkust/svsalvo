<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subject',
        'html_content',
        'text_content',
        'available_variables',
        'is_active',
        'category',
        'description',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all email logs for this template
     */
    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    /**
     * Replace variables in content
     */
    public function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }

    /**
     * Get processed subject with variables
     */
    public function getProcessedSubject(array $variables): string
    {
        return $this->replaceVariables($this->subject, $variables);
    }

    /**
     * Get processed HTML content with variables
     */
    public function getProcessedHtmlContent(array $variables): string
    {
        return $this->replaceVariables($this->html_content, $variables);
    }

    /**
     * Get processed text content with variables
     */
    public function getProcessedTextContent(array $variables): string
    {
        return $this->replaceVariables($this->text_content ?? strip_tags($this->html_content), $variables);
    }
}
