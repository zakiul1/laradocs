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
| If not authenticated -> /login
| If authenticated -> /dashboard
*/
Route::get('/', fn() => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Auth routes (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

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
    |----------------------------- Dashboard ------------------------------
    | Dashboard is the default landing page for authenticated users.
    | Both roles (admin, super_admin) can access.
    */
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->middleware('role:admin,super_admin')
        ->name('dashboard');

    /*
    |----------------------------- Admin area -----------------------------
    | All admin pages live under /admin with route names admin.*



    */



    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

        // Factories (admin + super_admin)
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::resource('factories', FactoryController::class)->except(['show']);
        });

        // Global category admin (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::get('categories', [CategoryAdminController::class, 'index'])->name('categories.index'); // ?scope=factory
            Route::post('categories/root', [CategoryAdminController::class, 'storeRoot'])->name('categories.root.store');
            Route::post('categories/child', [CategoryAdminController::class, 'storeChild'])->name('categories.child.store');
            Route::delete('categories/{category}', [CategoryAdminController::class, 'destroy'])->name('categories.destroy');
        });

    });
    Route::prefix('admin')->name('admin.')->group(function () {

        /*
        |------------------------- Employees (super_admin only) -------------
        | Full CRUD for Employees
        | URL: /admin/employees/*
        | Names: admin.employees.*
        */
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('employees', EmployeeController::class)->except(['show']);
        });

        /*
        |------------------------- Users -----------------------------------
        | Index is allowed for admin & super_admin (list only).
        | Create/Store/Edit/Update/Destroy/Deactivate are super_admin only.
        | URL: /admin/users/*
        | Names: admin.users.*
        */
        Route::middleware('role:admin,super_admin')->group(function () {
            // List only
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