<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'avg_name',
        'email',
        'password',
        'is_admin',
        'first_name',
        'last_name',
        'date_of_birth',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'position',
        'bio',
        'profile_image',
        'show_in_organization',
        'organization_sort_order',
        'member_since',
        'preferred_discipline',
        'license_number',
        'license_expiry',
        'show_contact_info',
        'show_scores_public',
        'is_active_member',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'date_of_birth' => 'date',
            'member_since' => 'date',
            'license_expiry' => 'date',
            'show_in_organization' => 'boolean',
            'show_contact_info' => 'boolean',
            'show_scores_public' => 'boolean',
            'is_active_member' => 'boolean',
            'organization_sort_order' => 'integer',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'profile_image_url',
    ];
    
    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
    
    /**
     * Scope to get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name;
    }

    /**
     * Get the profile image URL
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (!$this->profile_image) {
            return null;
        }
        
        // If the path already starts with http, return as is
        if (str_starts_with($this->profile_image, 'http')) {
            return $this->profile_image;
        }
        
        // Otherwise, construct the storage URL
        return asset('storage/' . $this->profile_image);
    }

    /**
     * Get user's match scores
     */
    public function matchScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'gebruiker_id');
    }

    /**
     * Get user's match registrations
     */
    public function matchRegistrations()
    {
        return $this->hasMany(MatchRegistration::class);
    }

    /**
     * Get user's activity registrations
     */
    public function activityRegistrations()
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    /**
     * Scope for organization members (people to show on organization page)
     */
    public function scopeOrganizationMembers($query)
    {
        return $query->where('show_in_organization', true)
                    ->orderBy('organization_sort_order');
    }

    /**
     * Scope for active members
     */
    public function scopeActiveMembers($query)
    {
        return $query->where('is_active_member', true);
    }

    /**
     * Check if user's scores should be public
     */
    public function scoresArePublic(): bool
    {
        return $this->show_scores_public;
    }

    /**
     * Check if contact info should be shown
     */
    public function contactInfoIsPublic(): bool
    {
        return $this->show_contact_info;
    }

    /**
     * Get user's public scores with match relation
     */
    public function scores()
    {
        return $this->hasMany(\App\Models\MatchGebruikerScore::class, 'gebruiker_id')->with('matches');
    }
}
