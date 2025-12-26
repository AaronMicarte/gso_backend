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
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/mfa/verify', [App\Http\Controllers\AuthController::class, 'completeMFALogin']);
Route::post('/mfa/setup/generate', [App\Http\Controllers\AuthController::class, 'generateMFASecretForLogin']);
Route::post('/mfa/setup/enable', [App\Http\Controllers\AuthController::class, 'enableMFADuringLogin']);
