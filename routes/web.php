<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShipperController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;

// Factory module (scoped categories + uploads)
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\FactoryCategoryController;
// NOTE: No separate FactorySubcategoryController when using a single-table taxonomy.

// Employee module (super_admin only)
use App\Http\Controllers\Admin\EmployeeController;

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
        | FACTORY MODULE (admin & super_admin)
        |------------------------------------------------------------------
        | - Factories CRUD (no "show")
        | - AJAX endpoints:
        |     * Dependent subcategories list
        |     * Quick-create Category & Subcategory (runtime from form)
        | - WordPress-like management screen for Categories/Subcategories
        |   (single page; mutations gated to super_admin)
        |------------------------------------------------------------------
        */

        // ===== Factories =====
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::resource('factories', FactoryController::class)->except(['show']);

            // Dependent dropdown (subcategory by category)
            Route::get('factories/subcategories/json', [FactoryController::class, 'subcategoriesJson'])
                ->name('factories.subcategories.json');

            // Quick create (runtime from Factory form)
            Route::post('factories/quick-create-category', [FactoryController::class, 'quickCreateCategory'])
                ->name('factories.quick-create-category');
            Route::post('factories/quick-create-subcategory', [FactoryController::class, 'quickCreateSubcategory'])
                ->name('factories.quick-create-subcategory');
        });

        // ===== Factory Categories & Subcategories (single-table taxonomy) =====
        // Index (single UI page) for admin & super_admin
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::get('factory-categories', [FactoryCategoryController::class, 'index'])
                ->name('factory-categories.index');

            // Back-compat: route /admin/factory-subcategories to the same index page
            Route::get('factory-subcategories', [FactoryCategoryController::class, 'index'])
                ->name('factory-subcategories.index');
        });

        // Mutations only for super_admin
        Route::middleware('role:super_admin')->group(function () {
            // Create parent (no factory_category_id) or child (with factory_category_id)
            Route::post('factory-categories', [FactoryCategoryController::class, 'store'])
                ->name('factory-categories.store');

            // Back-compat alias for creating a child via old endpoint
            Route::post('factory-subcategories', [FactoryCategoryController::class, 'store'])
                ->name('factory-subcategories.store');

            // Update (also supports re-parenting)
            Route::put('factory-categories/{factoryCategory}', [FactoryCategoryController::class, 'update'])
                ->name('factory-categories.update');

            // Back-compat alias for updating a child
            Route::put('factory-subcategories/{factoryCategory}', [FactoryCategoryController::class, 'update'])
                ->name('factory-subcategories.update');

            // Delete (parent delete will also delete its children per controller logic)
            Route::delete('factory-categories/{factoryCategory}', [FactoryCategoryController::class, 'destroy'])
                ->name('factory-categories.destroy');

            // Back-compat alias for deleting a child
            Route::delete('factory-subcategories/{factoryCategory}', [FactoryCategoryController::class, 'destroy'])
                ->name('factory-subcategories.destroy');
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

        /*
        |------------------------------------------------------------------
        | Employees (super_admin only)
        |------------------------------------------------------------------
        | - Full resource CRUD for employees
        | - Namespaced under /admin; route names: admin.employees.*
        |------------------------------------------------------------------
        */
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
        });

        // Customers (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('customers', CustomerController::class);
        });
        // Shipper (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('shippers', ShipperController::class)->names('shippers');
        });

        // Bank (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('banks', BankController::class)->names('banks');
        });
    });
});