<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ActivityRegistration;
use App\Observers\ActivityRegistrationObserver;
use App\Models\Matches;
use App\Observers\MatchObserver;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the NotificationService
        $this->app->singleton(\App\Services\NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->isProduction());

        // Note: Filament access is now controlled by the FilamentUser contract
        // implemented in the User model via canAccessPanel() method

        // Register observers
        ActivityRegistration::observe(ActivityRegistrationObserver::class);
        Matches::observe(MatchObserver::class);
        
        // Register notification system observers
        \App\Models\Article::observe(\App\Observers\ArticleObserver::class);
        \App\Models\Activity::observe(\App\Observers\ActivityObserver::class);
        \App\Models\Matches::observe(\App\Observers\MatchesObserver::class);
        // Note: UserObserver is already registered in the User model boot method
    }
}
