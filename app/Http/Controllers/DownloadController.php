<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Download;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DownloadController extends Controller
{
    /**
     * Display a listing of downloads
     */
    public function index()
    {
        $downloads = Download::where(function($query) {
                if (auth()->guest()) {
                    $query->where('is_public', true);
                } elseif (auth()->user() && !auth()->user()->is_admin) {
                    $query->where(function($subQuery) {
                        $subQuery->where('is_public', true)
                                 ->orWhere('requires_login', true);
                    });
                }
                // Admins can see all downloads (no additional filter)
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $categories = [
            'reglementen' => 'Reglementen',
            'formulieren' => 'Formulieren',
            'resultaten' => 'Resultaten',
            'documenten' => 'Documenten',
            'fotos' => "Foto's",
        ];

        return Inertia::render('Downloads', [
            'downloads' => $downloads,
            'categories' => $categories,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Handle download request
     */
    public function download($id)
    {
        \Log::info("Download request for ID: {$id}");
        
        $download = Download::findOrFail($id);
        \Log::info("Download found: " . json_encode($download->toArray()));

        // Check access permissions
        if (!$download->is_public && auth()->guest()) {
            \Log::info("Access denied: not public and user not logged in");
            return redirect()->route('login')->with('error', 'Je moet ingelogd zijn om dit bestand te downloaden.');
        }

        if ($download->requires_login && auth()->guest()) {
            \Log::info("Access denied: requires login and user not logged in");
            return redirect()->route('login')->with('error', 'Je moet ingelogd zijn om dit bestand te downloaden.');
        }

        // Check allowed roles if specified
        if ($download->allowed_roles && count($download->allowed_roles) > 0 && auth()->user()) {
            $user = auth()->user();
            $allowedRoles = $download->allowed_roles;
            \Log::info("Checking roles for user: " . json_encode($user->toArray()));
            \Log::info("Allowed roles: " . json_encode($allowedRoles));
            
            // Check if user is admin (always allowed)
            if (!$user->is_admin) {
                $hasAccess = false;
                
                // Example role checks - customize based on your needs:
                foreach ($allowedRoles as $role) {
                    if ($role === 'member' && $user->email_verified_at) {
                        $hasAccess = true;
                        break;
                    }
                    if ($role === 'bestuur' && ($user->is_admin || ($user->is_board_member ?? false))) {
                        $hasAccess = true;
                        break;
                    }
                }
                
                if (!$hasAccess) {
                    \Log::info("Access denied: user does not have required role");
                    return back()->with('error', 'Je hebt geen toegang tot dit bestand.');
                }
            }
        }

        // Increment download count
        $download->increment('download_count');
        \Log::info("Download count incremented");

        // Return file download
        $filePath = storage_path('app/public/' . $download->file_path);
        \Log::info("File path: {$filePath}");
        
        if (!file_exists($filePath)) {
            \Log::error("File not found: {$filePath}");
            return back()->with('error', 'Bestand niet gevonden op de server.');
        }

        \Log::info("Starting file download");
        return response()->download($filePath, $download->file_name);
    }
}
