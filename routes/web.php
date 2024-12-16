<?php

use App\Models\Invitation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';


// Route::get('/invitations/{invitation:slug}', [InvitationController::class, 'show'])
//     ->name('invitations.show');

// Route::get('/invitations/{invitation}/preview', [InvitationController::class, 'preview'])
//     ->name('invitations.preview')
//     ->middleware(['auth']);

// Route::get('/test-invitation', function () {
//     $invitation = \App\Models\Invitation::first();
//     return view('test-invitation', [
//         'invitation' => $invitation
//     ]);
// });

Route::get('/invitations/create', function() {
    return view('invitations.create');
})->name('invitations.create');
