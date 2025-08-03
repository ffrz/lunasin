<?php

use App\Http\Controllers\App\AuthController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PartyController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\TransactionCategoryController;
use App\Http\Controllers\App\TransactionController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Middleware\Auth;
use App\Http\Middleware\NonAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homepage-new');
})->name('home');

Route::get('/test', function () {
    return inertia('Test');
})->name('test');

Route::middleware(NonAuthenticated::class)->group(function () {
    Route::redirect('/', 'app/auth/login', 301);
    Route::prefix('/app/auth')->group(function () {
        Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('app.auth.login');
        Route::match(['get', 'post'], 'register', [AuthController::class, 'register'])->name('app.auth.register');
        Route::match(['get', 'post'], 'forgot-password', [AuthController::class, 'forgotPassword'])->name('app.auth.forgot-password');
    });
});

Route::middleware([Auth::class])->group(function () {
    Route::match(['get', 'post'], 'app/auth/logout', [AuthController::class, 'logout'])->name('app.auth.logout');

    Route::prefix('admin')->group(function () {
        Route::redirect('', 'app/dashboard', 301);

        Route::get('dashboard', [DashboardController::class, 'index'])->name('app.dashboard');
        Route::get('test', [DashboardController::class, 'test'])->name('app.test');
        Route::get('about', function () {
            return inertia('app/About');
        })->name('app.about');

        Route::prefix('transactions')->group(function () {
            Route::get('', [TransactionController::class, 'index'])->name('app.transaction.index');
            Route::get('data', [TransactionController::class, 'data'])->name('app.transaction.data');
            Route::get('add', [TransactionController::class, 'editor'])->name('app.transaction.add');
            Route::get('edit/{id}', [TransactionController::class, 'editor'])->name('app.transaction.edit');
            Route::get('detail/{id}', [TransactionController::class, 'detail'])->name('app.transaction.detail');
            Route::post('save', [TransactionController::class, 'save'])->name('app.transaction.save');
            Route::post('delete/{id}', [TransactionController::class, 'delete'])->name('app.transaction.delete');
        });

        Route::prefix('transaction-categories')->group(function () {
            Route::get('', [TransactionCategoryController::class, 'index'])->name('app.transaction-category.index');
            Route::get('data', [TransactionCategoryController::class, 'data'])->name('app.transaction-category.data');
            Route::get('add', [TransactionCategoryController::class, 'editor'])->name('app.transaction-category.add');
            Route::get('duplicate/{id}', [TransactionCategoryController::class, 'duplicate'])->name('app.transaction-category.duplicate');
            Route::get('edit/{id}', [TransactionCategoryController::class, 'editor'])->name('app.transaction-category.edit');
            Route::post('save', [TransactionCategoryController::class, 'save'])->name('app.transaction-category.save');
            Route::post('delete/{id}', [TransactionCategoryController::class, 'delete'])->name('app.transaction-category.delete');
        });

        Route::prefix('parties')->group(function () {
            Route::get('', [PartyController::class, 'index'])->name('app.party.index');
            Route::get('data', [PartyController::class, 'data'])->name('app.party.data');
            Route::get('add', [PartyController::class, 'editor'])->name('app.party.add');
            Route::get('duplicate/{id}', [PartyController::class, 'duplicate'])->name('app.party.duplicate');
            Route::get('edit/{id}', [PartyController::class, 'editor'])->name('app.party.edit');
            Route::post('save', [PartyController::class, 'save'])->name('app.party.save');
            Route::post('delete/{id}', [PartyController::class, 'delete'])->name('app.party.delete');
            Route::get('detail/{id}', [PartyController::class, 'detail'])->name('app.party.detail');
            Route::get('export', [PartyController::class, 'export'])->name('app.party.export');
        });

        Route::prefix('settings')->group(function () {
            Route::get('profile/edit', [ProfileController::class, 'edit'])->name('app.profile.edit');
            Route::post('profile/update', [ProfileController::class, 'update'])->name('app.profile.update');
            Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('app.profile.update-password');

            Route::prefix('users')->group(function () {
                Route::get('', [UserController::class, 'index'])->name('app.user.index');
                Route::get('data', [UserController::class, 'data'])->name('app.user.data');
                Route::get('add', [UserController::class, 'editor'])->name('app.user.add');
                Route::get('edit/{id}', [UserController::class, 'editor'])->name('app.user.edit');
                Route::get('duplicate/{id}', [UserController::class, 'duplicate'])->name('app.user.duplicate');
                Route::post('save', [UserController::class, 'save'])->name('app.user.save');
                Route::post('delete/{id}', [UserController::class, 'delete'])->name('app.user.delete');
                Route::get('detail/{id}', [UserController::class, 'detail'])->name('app.user.detail');
                Route::get('export', [UserController::class, 'export'])->name('app.user.export');
            });
        });
    });
});
