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
        //
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
    }
}
