<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::observe(\App\Observers\UserObserver::class);
    }

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
        'show_full_name',
        'show_contact_on_members_page',
        'show_in_participants',
        'is_active_member',
        'membership_status',
        'membership_number',
        'is_blocked',
        'blocked_reason',
        'blocked_at',
        'password_change_required',
        'email_verified_at',
        'roles',
        'remember_token',
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
            'show_full_name' => 'boolean',
            'show_contact_on_members_page' => 'boolean',
            'is_active_member' => 'boolean',
            'is_blocked' => 'boolean',
            'organization_sort_order' => 'integer',
            'blocked_at' => 'datetime',
            'password_change_required' => 'boolean',
            'roles' => 'array',
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
     * Scope to get only active, non-blocked members
     * Used for quick lookup in registration forms
     */
    public function scopeWhereActive($query)
    {
        return $query
            ->where('is_active_member', true)
            ->where('is_blocked', false);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        // Check if first_name and last_name attributes are loaded by checking the attributes array
        if (array_key_exists('first_name', $this->attributes) && array_key_exists('last_name', $this->attributes)) {
            return ($this->attributes['first_name'] ?? '') . ' ' . ($this->attributes['last_name'] ?? '');
        }
        
        // Fall back to name if first_name/last_name are not available
        return $this->name ?? 'Unknown User';
    }

    /**
     * Get the profile image URL
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        // Check if profile_image attribute is loaded
        if (!array_key_exists('profile_image', $this->attributes)) {
            return null;
        }
        
        if (!$this->profile_image) {
            return null;
        }
        
        // If the path already starts with http, return as is
        if (str_starts_with($this->profile_image, 'http')) {
            return $this->profile_image;
        }
        
        // Check if the file exists in storage
        $storagePath = storage_path('app/public/' . $this->profile_image);
        $publicPath = public_path('storage/' . $this->profile_image);
        
        // If file exists in public/storage (shared hosting fallback)
        if (file_exists($publicPath)) {
            return asset('storage/' . $this->profile_image);
        }
        
        // If file exists in storage but not accessible via symlink, try direct access
        if (file_exists($storagePath)) {
            // For shared hosting, sometimes we need a different approach
            return route('storage.image', ['path' => $this->profile_image]);
        }
        
        // Otherwise, construct the standard storage URL
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
     * Get user's competition registrations (new system)
     */
    public function competitionRegistrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    /**
     * Get user's competition scores (new system)
     */
    public function competitionScores()
    {
        return $this->hasMany(CompetitionScore::class);
    }

    /**
     * Get competitions this user is registered for (active only)
     */
    public function competitions()
    {
        return $this->belongsToMany(
            Competition::class,
            'competition_registrations',
            'user_id',
            'competition_id'
        )->where('competition_registrations.status', 'actief');
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
     * Get user's public scores with match relation (only official scores)
     */
    public function scores()
    {
        return $this->hasMany(\App\Models\MatchGebruikerScore::class, 'gebruiker_id')
            ->where('is_official', true)
            ->with('matches');
    }

    /**
     * Get all user's scores including fun games
     */
    public function allScores()
    {
        return $this->hasMany(\App\Models\MatchGebruikerScore::class, 'gebruiker_id')->with('matches');
    }

    /**
     * Get user's legal document acceptances
     */
    public function legalAcceptances()
    {
        return $this->hasMany(\App\Models\UserLegalAcceptance::class);
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get user's unread notifications
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    // Feedback relationships
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function moderatedFeedback()
    {
        return $this->hasMany(Feedback::class, 'moderator_id');
    }

    public function feedbackComments()
    {
        return $this->hasMany(FeedbackComment::class);
    }

    public function feedbackVotes()
    {
        return $this->hasMany(FeedbackVote::class);
    }

    /**
     * Role constants
     */
    public const ROLES = [
        'wedstrijdcommisie' => 'Wedstrijdcommisie',
        'secretaris' => 'Secretaris',
        'webmaster' => 'Webmaster',
        'activiteitencommisie' => 'Activiteitencommisie',
        'kascommisie' => 'Kascommisie',
        'voorzitter' => 'Voorzitter',
    ];

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? []);
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return !empty(array_intersect($this->roles ?? [], $roles));
    }

    /**
     * Check if user can access all admin areas (super admin roles)
     */
    public function canAccessAll(): bool
    {
        return $this->is_admin || $this->hasAnyRole(['secretaris', 'webmaster', 'voorzitter']);
    }

    /**
     * Check if user can access matches/competitions
     */
    public function canAccessMatches(): bool
    {
        return $this->canAccessAll() || $this->hasRole('wedstrijdcommisie');
    }

    /**
     * Check if user can access activities
     */
    public function canAccessActivities(): bool
    {
        return $this->canAccessAll() || $this->hasRole('activiteitencommisie');
    }

    /**
     * Check if user can access financial areas (prices)
     */
    public function canAccessFinancial(): bool
    {
        return $this->canAccessAll() || $this->hasRole('kascommisie');
    }

    /**
     * Get formatted roles for display
     */
    public function getFormattedRolesAttribute(): string
    {
        if (empty($this->roles)) {
            return 'Geen rollen';
        }
        
        return collect($this->roles)
            ->map(fn($role) => self::ROLES[$role] ?? $role)
            ->join(', ');
    }

    /**
     * Add a role to the user
     */
    public function addRole(string $role): void
    {
        $roles = $this->roles ?? [];
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $this->update(['roles' => $roles]);
        }
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string $role): void
    {
        $roles = $this->roles ?? [];
        $roles = array_filter($roles, fn($r) => $r !== $role);
        $this->update(['roles' => array_values($roles)]);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // For production: Only allow users with admin privileges or specific roles
        return ($this->is_admin || count($this->roles ?? []) > 0);
    }
}
