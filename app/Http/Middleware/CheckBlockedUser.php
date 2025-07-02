<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_blocked) {
            $user = Auth::user();
            $reason = $user->blocked_reason ? $user->blocked_reason : 'Geen specifieke reden opgegeven.';
            $blockedAt = $user->blocked_at ? $user->blocked_at->format('d-m-Y H:i') : 'Onbekend';
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            $errorMessage = "Uw account is geblokkeerd sinds {$blockedAt}.\n\nReden: {$reason}\n\nNeem contact op met de beheerder voor meer informatie.";
            
            return redirect()->route('login')->with('error', $errorMessage);
        }

        return $next($request);
    }
}
