<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PartyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\TransactionCategoryController;
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

        // Route::prefix('products')->group(function () {
        //     Route::get('', [ProductController::class, 'index'])->name('admin.product.index');
        //     Route::get('data', [ProductController::class, 'data'])->name('admin.product.data');
        //     Route::get('add', [ProductController::class, 'editor'])->name('admin.product.add');
        //     Route::get('duplicate/{id}', [ProductController::class, 'duplicate'])->name('admin.product.duplicate');
        //     Route::get('edit/{id}', [ProductController::class, 'editor'])->name('admin.product.edit');
        //     Route::post('save', [ProductController::class, 'save'])->name('admin.product.save');
        //     Route::post('delete/{id}', [ProductController::class, 'delete'])->name('admin.product.delete');
        //     Route::get('detail/{id}', [ProductController::class, 'detail'])->name('admin.product.detail');
        //     Route::get('export', [ProductController::class, 'export'])->name('admin.product.export');
        // });

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

        // Route::prefix('activity-types')->group(function () {
        //     Route::get('', [ActivityTypeController::class, 'index'])->name('admin.activity-type.index');
        //     Route::get('data', [ActivityTypeController::class, 'data'])->name('admin.activity-type.data');
        //     Route::get('add', [ActivityTypeController::class, 'editor'])->name('admin.activity-type.add');
        //     Route::get('duplicate/{id}', [ActivityTypeController::class, 'duplicate'])->name('admin.activity-type.duplicate');
        //     Route::get('edit/{id}', [ActivityTypeController::class, 'editor'])->name('admin.activity-type.edit');
        //     Route::post('save', [ActivityTypeController::class, 'save'])->name('admin.activity-type.save');
        //     Route::post('delete/{id}', [ActivityTypeController::class, 'delete'])->name('admin.activity-type.delete');
        // });

        Route::prefix('settings')->group(function () {
            Route::get('profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
            Route::post('profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
            Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');

            // Route::get('company-profile/edit', [CompanyProfileController::class, 'edit'])->name('admin.company-profile.edit');
            // Route::post('company-profile/update', [CompanyProfileController::class, 'update'])->name('admin.company-profile.update');

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
