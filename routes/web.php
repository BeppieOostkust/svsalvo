<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');


Route::get('/nieuws', function () {
    return Inertia::render('nieuws');
})->name('nieuws');

Route::get('/downloads', function () {
    return Inertia::render('downloads');
})->name('downloads');

Route::get('/activiteiten', function () {
    return Inertia::render('activiteiten');
})->name('activiteiten');

Route::get('/organisatie', function () {
    return Inertia::render('organisatie');
})->name('organisatie');

Route::get('/instellingen', function () {
    return Inertia::render('instellingen');
})->name('instellingen');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
