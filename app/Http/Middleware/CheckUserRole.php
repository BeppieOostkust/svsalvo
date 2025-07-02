<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is blocked first
        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Uw account is geblokkeerd.');
        }

        // Admins have access to everything
        if ($user->is_admin) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            abort(403, 'Geen toegang tot deze pagina. Vereiste rol: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}
