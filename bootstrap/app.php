<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\CheckLegalDocumentAcceptance;
use App\Http\Middleware\RequirePasswordChange;
use App\Http\Middleware\CheckBlockedUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            CheckBlockedUser::class,
            RequirePasswordChange::class,
        ]);

        $middleware->alias([
            'legal.check' => \App\Http\Middleware\CheckLegalDocumentAcceptance::class,
            'password.change' => \App\Http\Middleware\RequirePasswordChange::class,
            'blocked.check' => \App\Http\Middleware\CheckBlockedUser::class,
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
