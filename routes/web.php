<?php

/**
 * Lunasin - Personal Debt & Credit Management Application
 *
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * GitHub: https://github.com/ffrz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package   Lunasin
 * @author    Fahmi Fauzi Rahman
 * @license   MIT
 */

use App\Http\Controllers\App\AuthController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PartyController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\TransactionCategoryController;
use App\Http\Controllers\App\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\App\ReportController;
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
});

Route::middleware([Auth::class])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::redirect('', 'app/dashboard', 301);

        Route::get('dashboard', [DashboardController::class, 'index'])->name('app.dashboard');
        Route::get('test', [DashboardController::class, 'test'])->name('app.test');
        Route::get('about', function () {
            return inertia('app/About');
        })->name('app.about');

        Route::prefix('reports')->group(function() {
            Route::get('', [ReportController::class, 'index'])->name('app.report.index');
        });

        Route::prefix('transactions')->group(function () {
            Route::get('', [TransactionController::class, 'index'])->name('app.transaction.index');
            Route::get('data', [TransactionController::class, 'data'])->name('app.transaction.data');
            Route::get('add', [TransactionController::class, 'editor'])->name('app.transaction.add');
            Route::get('edit/{id}', [TransactionController::class, 'editor'])->name('app.transaction.edit');
            Route::get('detail/{id}', [TransactionController::class, 'detail'])->name('app.transaction.detail');
            Route::post('save', [TransactionController::class, 'save'])->name('app.transaction.save');
            Route::post('delete/{id}', [TransactionController::class, 'delete'])->name('app.transaction.delete');
            Route::get('export', [TransactionController::class, 'export'])->name('app.transaction.export');
        });

        Route::prefix('transaction-categories')->group(function () {
            Route::get('', [TransactionCategoryController::class, 'index'])->name('app.transaction-category.index');
            Route::get('data', [TransactionCategoryController::class, 'data'])->name('app.transaction-category.data');
            Route::get('add', [TransactionCategoryController::class, 'editor'])->name('app.transaction-category.add');
            Route::get('duplicate/{id}', [TransactionCategoryController::class, 'duplicate'])->name('app.transaction-category.duplicate');
            Route::get('edit/{id}', [TransactionCategoryController::class, 'editor'])->name('app.transaction-category.edit');
            Route::post('save', [TransactionCategoryController::class, 'save'])->name('app.transaction-category.save');
            Route::post('delete/{id}', [TransactionCategoryController::class, 'delete'])->name('app.transaction-category.delete');
            Route::get('export', [TransactionCategoryController::class, 'export'])->name('app.transaction-category.export');
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

require_once __DIR__ . '/auth.php';
