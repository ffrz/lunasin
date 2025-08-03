<?php

use App\Http\Controllers\App\Auth\AuthenticatedSessionController;
use App\Http\Controllers\App\Auth\ConfirmablePasswordController;
use App\Http\Controllers\App\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\App\Auth\EmailVerificationPromptController;
use App\Http\Controllers\App\Auth\GoogleAuthController;
use App\Http\Controllers\App\Auth\NewPasswordController;
use App\Http\Controllers\App\Auth\PasswordController;
use App\Http\Controllers\App\Auth\PasswordResetLinkController;
use App\Http\Controllers\App\Auth\RegisteredUserController;
use App\Http\Controllers\App\Auth\VerifyEmailController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\NonAuthenticated;
use Illuminate\Support\Facades\Route;

// google oauth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('app.auth.google-login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::middleware(NonAuthenticated::class)->group(function () {
    Route::prefix('app/auth')->group(function () {
        Route::get('register-options', function () {
            return inertia('app/auth/RegisterOptions');
        })->name('app.auth.register-options');

        Route::get('register', [RegisteredUserController::class, 'create'])
            ->name('app.auth.register');

        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('app.auth.login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('app.auth.forgot-password');

        Route::post('request-new-password', [PasswordResetLinkController::class, 'store'])
            ->name('app.auth.request-new-password');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('app.auth.reset-password-with-token');

        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('app.auth.reset-password');
    });
});

Route::middleware([Auth::class])->group(function () {
    Route::prefix('app/auth')->group(function () {
        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('app.auth.verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('app.auth.verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('app.auth.verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('app.auth.password.confirm');

        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::put('password', [PasswordController::class, 'update'])->name('app.auth.password.update');

        Route::match(['get', 'post'], 'logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('app.auth.logout');
    });
});
