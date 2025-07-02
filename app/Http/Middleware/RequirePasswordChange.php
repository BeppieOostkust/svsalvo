<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug logging
        \Log::info('RequirePasswordChange middleware called', [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'route' => $request->route()?->getName(),
            'url' => $request->url(),
        ]);

        // Check if user is authenticated and needs to change password
        if (auth()->check() && auth()->user()->password_change_required) {
            \Log::info('User needs password change', [
                'user_id' => auth()->id(),
                'password_change_required' => auth()->user()->password_change_required,
            ]);

            // Allow access to password change routes and logout
            $allowedRoutes = [
                'password.change.form',
                'password.change',
                'logout',
            ];
            
            if (!in_array($request->route()?->getName(), $allowedRoutes) && 
                !$request->is('change-password*') && 
                !$request->is('logout')) {
                \Log::info('Redirecting to password change form');
                return redirect()->route('password.change.form');
            }
        }

        return $next($request);
    }
}
