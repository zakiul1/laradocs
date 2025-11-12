<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;

// Factory module (scoped categories + uploads)
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\FactoryCategoryController;

// Employee module (legacy alias in /admin and new root App\Http\Controllers)
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\EmployeeController as RootEmployeeController;

// Core master data modules
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShipperController;
use App\Http\Controllers\BankController;

// NEW: Invoice module controllers
use App\Http\Controllers\SampleInvoiceController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesReportController;

/*
|--------------------------------------------------------------------------
| Public redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => auth()->check()
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
        */
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

        // Factory Categories & Subcategories management (single-table taxonomy)
        Route::middleware('role:admin,super_admin')->group(function () {
            Route::get('factory-categories', [FactoryCategoryController::class, 'index'])
                ->name('factory-categories.index');
            Route::get('factory-subcategories', [FactoryCategoryController::class, 'index'])
                ->name('factory-subcategories.index');
        });

        // Mutations only for super_admin
        Route::middleware('role:super_admin')->group(function () {
            Route::post('factory-categories', [FactoryCategoryController::class, 'store'])
                ->name('factory-categories.store');
            Route::post('factory-subcategories', [FactoryCategoryController::class, 'store'])
                ->name('factory-subcategories.store');

            Route::put('factory-categories/{factoryCategory}', [FactoryCategoryController::class, 'update'])
                ->name('factory-categories.update');
            Route::put('factory-subcategories/{factoryCategory}', [FactoryCategoryController::class, 'update'])
                ->name('factory-subcategories.update');

            Route::delete('factory-categories/{factoryCategory}', [FactoryCategoryController::class, 'destroy'])
                ->name('factory-categories.destroy');
            Route::delete('factory-subcategories/{factoryCategory}', [FactoryCategoryController::class, 'destroy'])
                ->name('factory-subcategories.destroy');
        });

        /*
        |------------------------------------------------------------------
        | Users
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
        */
        Route::middleware('role:super_admin')->group(function () {
            // legacy: root controller alias
            Route::resource('employees', RootEmployeeController::class);
            // or the namespaced admin controller if you prefer:
            // Route::resource('employees', AdminEmployeeController::class);
        });

        /*
        |------------------------------------------------------------------
        | Core master data (super_admin only)
        |------------------------------------------------------------------
        */
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('customers', CustomerController::class);
            Route::resource('shippers', ShipperController::class)->names('shippers');
            Route::resource('banks', BankController::class)->names('banks');
        });

        /*
        |------------------------------------------------------------------
        | INVOICE MODULE
        |------------------------------------------------------------------
        | We allow both admin and super_admin to work with invoices.
        | Adjust to only super_admin if you want tighter control.
        |------------------------------------------------------------------
        */
        Route::middleware('role:admin,super_admin')->group(function () {
            /*
             * SAMPLE INVOICES
             * Route names: admin.sample-invoices.*
             */
            Route::get('sample-invoices', [SampleInvoiceController::class, 'index'])->name('sample-invoices.index');
            Route::get('sample-invoices/create', [SampleInvoiceController::class, 'create'])->name('sample-invoices.create');
            Route::post('sample-invoices', [SampleInvoiceController::class, 'store'])->name('sample-invoices.store');
            Route::get('sample-invoices/{invoice}/edit', [SampleInvoiceController::class, 'edit'])->name('sample-invoices.edit');
            Route::put('sample-invoices/{invoice}', [SampleInvoiceController::class, 'update'])->name('sample-invoices.update');
            Route::delete('sample-invoices/{invoice}', [SampleInvoiceController::class, 'destroy'])->name('sample-invoices.destroy');

            // Preview (HTML)
            Route::get('sample-invoices/{invoice}/preview', [SampleInvoiceController::class, 'show'])
                ->name('sample-invoices.show');

            // PDF export
            Route::get('sample-invoices/{invoice}/pdf', [SampleInvoiceController::class, 'pdf'])
                ->name('sample-invoices.pdf');

            // Select2-style lookups / small JSON helpers
            Route::get('sample-invoices/lookups/shippers', [SampleInvoiceController::class, 'lookupShippers'])
                ->name('sample-invoices.lookups.shippers');
            Route::get('sample-invoices/lookups/customers', [SampleInvoiceController::class, 'lookupCustomers'])
                ->name('sample-invoices.lookups.customers');
            Route::get('sample-invoices/lookups/currencies', [SampleInvoiceController::class, 'lookupCurrencies'])
                ->name('sample-invoices.lookups.currencies');

            /*
             * SALES INVOICES (LC/TT)
             * Route names: admin.sales-invoices.*
             */
            Route::get('sales-invoices', [SalesInvoiceController::class, 'index'])->name('sales-invoices.index');
            Route::get('sales-invoices/create', [SalesInvoiceController::class, 'create'])->name('sales-invoices.create');
            Route::post('sales-invoices', [SalesInvoiceController::class, 'store'])->name('sales-invoices.store');
            Route::get('sales-invoices/{invoice}/edit', [SalesInvoiceController::class, 'edit'])->name('sales-invoices.edit');
            Route::put('sales-invoices/{invoice}', [SalesInvoiceController::class, 'update'])->name('sales-invoices.update');
            Route::delete('sales-invoices/{invoice}', [SalesInvoiceController::class, 'destroy'])->name('sales-invoices.destroy');

            // Preview (HTML)
            Route::get('sales-invoices/{invoice}/preview', [SalesInvoiceController::class, 'show'])
                ->name('sales-invoices.show');

            // PDF export
            Route::get('sales-invoices/{invoice}/pdf', [SalesInvoiceController::class, 'pdf'])
                ->name('sales-invoices.pdf');

            // Lookups shared with sales screens
            Route::get('sales-invoices/lookups/shippers', [SalesInvoiceController::class, 'lookupShippers'])
                ->name('sales-invoices.lookups.shippers');
            Route::get('sales-invoices/lookups/customers', [SalesInvoiceController::class, 'lookupCustomers'])
                ->name('sales-invoices.lookups.customers');
            Route::get('sales-invoices/lookups/currencies', [SalesInvoiceController::class, 'lookupCurrencies'])
                ->name('sales-invoices.lookups.currencies');

            /*
             * REPORTS
             */
            Route::get('reports/sales', [SalesReportController::class, 'index'])
                ->name('reports.sales');
            // Optional JSON endpoint for chart data (group by fiscal year etc.)
            Route::get('reports/sales/data', [SalesReportController::class, 'data'])
                ->name('reports.sales.data');
        });
    });
});
