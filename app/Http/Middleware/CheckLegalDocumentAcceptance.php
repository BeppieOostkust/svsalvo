<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LegalDocument;
use App\Models\UserLegalAcceptance;
use Illuminate\Support\Facades\Auth;

class CheckLegalDocumentAcceptance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Check if user is blocked
        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('login')->with('error', 
                'Uw account is geblokkeerd: ' . $user->blocked_reason . '. Neem contact op met de beheerder.'
            );
        }

        // Skip check for legal document routes and logout
        if ($request->routeIs(['privacy-policy', 'terms-conditions', 'logout', 'legal.*'])) {
            return $next($request);
        }

        // Get active legal documents
        $activeDocuments = LegalDocument::where('is_active', true)->get();

        foreach ($activeDocuments as $document) {
            $acceptance = UserLegalAcceptance::where('user_id', $user->id)
                ->where('legal_document_id', $document->id)
                ->first();

            // Check if user hasn't accepted this document version
            if (!$acceptance || $acceptance->version_accepted !== $document->version) {
                // Store the required acceptance in session
                session([
                    'required_legal_acceptance' => [
                        'document_id' => $document->id,
                        'document_type' => $document->type,
                        'document_title' => $document->title,
                        'document_version' => $document->version,
                        'document_content' => $document->content,
                    ]
                ]);

                return redirect()->route('legal.acceptance.required');
            }
        }

        return $next($request);
    }
}
