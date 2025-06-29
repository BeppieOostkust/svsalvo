<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
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

        // Restrict Filament access to admin users only
        Gate::define('viewFilament', function ($user) {
            return $user->isAdmin();
        });

        // Register observers
        ActivityRegistration::observe(ActivityRegistrationObserver::class);
        Matches::observe(MatchObserver::class);
    }
}
