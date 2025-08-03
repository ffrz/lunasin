<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PartyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TransactionCategoryController;
use App\Http\Controllers\Admin\TransactionController;
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
    Route::redirect('/', 'admin/auth/login', 301);
    Route::prefix('/admin/auth')->group(function () {
        Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('admin.auth.login');
        Route::match(['get', 'post'], 'register', [AuthController::class, 'register'])->name('admin.auth.register');
        Route::match(['get', 'post'], 'forgot-password', [AuthController::class, 'forgotPassword'])->name('admin.auth.forgot-password');
    });
});

Route::middleware([Auth::class])->group(function () {
    Route::match(['get', 'post'], 'admin/auth/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');

    Route::prefix('admin')->group(function () {
        Route::redirect('', 'admin/dashboard', 301);

        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('test', [DashboardController::class, 'test'])->name('admin.test');
        Route::get('about', function () {
            return inertia('admin/About');
        })->name('admin.about');

        Route::prefix('transactions')->group(function () {
            Route::get('', [TransactionController::class, 'index'])->name('admin.transaction.index');
            Route::get('data', [TransactionController::class, 'data'])->name('admin.transaction.data');
            Route::get('add', [TransactionController::class, 'editor'])->name('admin.transaction.add');
            Route::get('edit/{id}', [TransactionController::class, 'editor'])->name('admin.transaction.edit');
            Route::get('detail/{id}', [TransactionController::class, 'detail'])->name('admin.transaction.detail');
            Route::post('save', [TransactionController::class, 'save'])->name('admin.transaction.save');
            Route::post('delete/{id}', [TransactionController::class, 'delete'])->name('admin.transaction.delete');
        });

        Route::prefix('transaction-categories')->group(function () {
            Route::get('', [TransactionCategoryController::class, 'index'])->name('admin.transaction-category.index');
            Route::get('data', [TransactionCategoryController::class, 'data'])->name('admin.transaction-category.data');
            Route::get('add', [TransactionCategoryController::class, 'editor'])->name('admin.transaction-category.add');
            Route::get('duplicate/{id}', [TransactionCategoryController::class, 'duplicate'])->name('admin.transaction-category.duplicate');
            Route::get('edit/{id}', [TransactionCategoryController::class, 'editor'])->name('admin.transaction-category.edit');
            Route::post('save', [TransactionCategoryController::class, 'save'])->name('admin.transaction-category.save');
            Route::post('delete/{id}', [TransactionCategoryController::class, 'delete'])->name('admin.transaction-category.delete');
        });

        Route::prefix('parties')->group(function () {
            Route::get('', [PartyController::class, 'index'])->name('admin.party.index');
            Route::get('data', [PartyController::class, 'data'])->name('admin.party.data');
            Route::get('add', [PartyController::class, 'editor'])->name('admin.party.add');
            Route::get('duplicate/{id}', [PartyController::class, 'duplicate'])->name('admin.party.duplicate');
            Route::get('edit/{id}', [PartyController::class, 'editor'])->name('admin.party.edit');
            Route::post('save', [PartyController::class, 'save'])->name('admin.party.save');
            Route::post('delete/{id}', [PartyController::class, 'delete'])->name('admin.party.delete');
            Route::get('detail/{id}', [PartyController::class, 'detail'])->name('admin.party.detail');
            Route::get('export', [PartyController::class, 'export'])->name('admin.party.export');
        });

        Route::prefix('settings')->group(function () {
            Route::get('profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
            Route::post('profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
            Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');

            Route::prefix('users')->group(function () {
                Route::get('', [UserController::class, 'index'])->name('admin.user.index');
                Route::get('data', [UserController::class, 'data'])->name('admin.user.data');
                Route::get('add', [UserController::class, 'editor'])->name('admin.user.add');
                Route::get('edit/{id}', [UserController::class, 'editor'])->name('admin.user.edit');
                Route::get('duplicate/{id}', [UserController::class, 'duplicate'])->name('admin.user.duplicate');
                Route::post('save', [UserController::class, 'save'])->name('admin.user.save');
                Route::post('delete/{id}', [UserController::class, 'delete'])->name('admin.user.delete');
                Route::get('detail/{id}', [UserController::class, 'detail'])->name('admin.user.detail');
                Route::get('export', [UserController::class, 'export'])->name('admin.user.export');
            });
        });
    });
});
