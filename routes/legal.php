<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LegalAcceptanceController;

// Public routes for legal documents
Route::get('/privacy-policy', function () {
    $document = \App\Models\LegalDocument::getActiveDocument('privacy_policy');
    return inertia('Legal/PrivacyPolicy', ['document' => $document]);
})->name('privacy-policy');

Route::get('/terms-conditions', function () {
    $document = \App\Models\LegalDocument::getActiveDocument('terms_conditions');
    return inertia('Legal/TermsConditions', ['document' => $document]);
})->name('terms-conditions');

// Legal acceptance routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/legal/acceptance-required', [LegalAcceptanceController::class, 'show'])
        ->name('legal.acceptance.required');
    
    Route::post('/legal/accept', [LegalAcceptanceController::class, 'accept'])
        ->name('legal.accept');
    
    Route::post('/legal/decline', [LegalAcceptanceController::class, 'decline'])
        ->name('legal.decline');
});

