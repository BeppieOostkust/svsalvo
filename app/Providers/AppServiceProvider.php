<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;

class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }

        return route('storage.public', ['path' => $path]);
    }

    public static function modelUrl(EloquentModel $model, string $attribute): ?string
    {
        return self::url($model->getRawOriginal($attribute) ?? $model->getAttribute($attribute));
    }

    public static function expose(EloquentModel $model, string $attribute): EloquentModel
    {
        $model->setAttribute($attribute, self::modelUrl($model, $attribute));

        return $model;
    }
}

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
