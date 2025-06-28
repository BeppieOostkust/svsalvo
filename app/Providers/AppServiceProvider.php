<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use App\Models\ActivityRegistration;
use App\Observers\ActivityRegistrationObserver;

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
        // Restrict Filament access to admin users only
        Gate::define('viewFilament', function ($user) {
            return $user->isAdmin();
        });

        // Register observers
        ActivityRegistration::observe(ActivityRegistrationObserver::class);
    }
}
