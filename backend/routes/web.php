<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/', function () {
    return view('welcome');
});



// Sanctum CSRF Cookie Route for SPA authentication
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])
    ->middleware('web');

// Authentication routes
Route::get('/login', function () {
    $frontendUrl = env('FRONTEND_URL') ?: env('GSO_FRONTEND_URL') ?: 'https://gso-iams.vercel.app';
    return redirect($frontendUrl);
})->name('login');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/auth/change-temporary-password', [App\Http\Controllers\AuthController::class, 'changeTemporaryPassword']);
Route::post('/mfa/verify', [App\Http\Controllers\AuthController::class, 'completeMFALogin']);
Route::post('/mfa/setup/generate', [App\Http\Controllers\AuthController::class, 'generateMFASecretForLogin']);
Route::post('/mfa/setup/enable', [App\Http\Controllers\AuthController::class, 'enableMFADuringLogin']);
