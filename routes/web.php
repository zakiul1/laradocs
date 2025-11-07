<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\CategoryAdminController;

/*
|--------------------------------------------------------------------------
| Public redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Auth (guest-only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    // Password reset
    Route::get('/password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Logout (auth-only)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated application
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Dashboard (admin & super_admin)
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->middleware('role:admin,super_admin')
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Admin area (/admin, names: admin.*)
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        /*
        |------------------------------------------------------------------
        | Factories (admin & super_admin) — resource without "show"
        |------------------------------------------------------------------
        */
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::resource('factories', FactoryController::class)->except(['show']);
        });

        /*
        |------------------------------------------------------------------
        | Categories (WordPress-style)
        |------------------------------------------------------------------
        | - index + quick-create available to admin & super_admin
        |   (for runtime category creation from forms)
        | - store/update/destroy restricted to super_admin
        |------------------------------------------------------------------
        */
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::get('categories', [CategoryAdminController::class, 'index'])
                ->name('categories.index'); // ?scope=factory
            Route::post('categories/quick-create', [CategoryAdminController::class, 'quickCreate'])
                ->name('categories.quick-create');
        });

        Route::middleware('role:super_admin')->group(function () {
            Route::post('categories', [CategoryAdminController::class, 'store'])->name('categories.store');
            Route::put('categories/{category}', [CategoryAdminController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [CategoryAdminController::class, 'destroy'])->name('categories.destroy');
        });

        /*
        |------------------------------------------------------------------
        | Employees (super_admin only) — full CRUD without "show"
        |------------------------------------------------------------------
        */
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('employees', EmployeeController::class)->except(['show']);
        });

        /*
        |------------------------------------------------------------------
        | Users
        |------------------------------------------------------------------
        | - index: admin & super_admin
        | - create/store/edit/update/destroy/deactivate: super_admin only
        |------------------------------------------------------------------
        */
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::resource('users', UserController::class)->only(['index']);
        });

        Route::middleware('role:super_admin')->group(function () {
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        });
    });
});