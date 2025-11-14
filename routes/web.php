<?php

use App\Http\Controllers\SalesInvoiceController;
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

// NEW: Sample Invoice module controller
use App\Http\Controllers\SampleInvoiceController;

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
            // Or the namespaced admin controller if you prefer:
            // Route::resource('employees', AdminEmployeeController::class);
        });

        /*
        |------------------------------------------------------------------
        | Core master data (super_admin only)
        |------------------------------------------------------------------
        */
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('customers', CustomerController::class);


            // Companies
            Route::resource('companies', \App\Http\Controllers\CompanyController::class)
                ->names('companies');

            // Real-time category create + optional search
            Route::post(
                'companies/categories/quick-create',
                [\App\Http\Controllers\CompanyController::class, 'quickCreateCategory']
            )->name('companies.categories.quick-create');

            Route::get(
                'companies/categories/json',
                [\App\Http\Controllers\CompanyController::class, 'categoriesJson']
            )->name('companies.categories.json');

            // AJAX company search for Bank form
            Route::get('banks/company-options', [BankController::class, 'companyOptions'])
                ->name('banks.company-options');

            // Bank CRUD
            Route::resource('banks', BankController::class)->names('banks');
        });

        /*
        |------------------------------------------------------------------
        | INVOICE MODULE
        |------------------------------------------------------------------
        | Only SAMPLE INVOICES now (admin + super_admin)
        |------------------------------------------------------------------
        */
        Route::middleware('role:admin,super_admin')->group(function () {

            // SALES INVOICES
            Route::resource('sales-invoices', SalesInvoiceController::class)
                ->names('sales-invoices')
                ->parameters(['sales-invoices' => 'salesInvoice']);

            Route::get('sales-invoices/{salesInvoice}/pdf', [SalesInvoiceController::class, 'pdf'])
                ->name('sales-invoices.pdf');

            // SAMPLE INVOICES (you already had this)
            Route::resource('sample-invoices', SampleInvoiceController::class)
                ->names('sample-invoices')
                ->parameters(['sample-invoices' => 'sampleInvoice'])
                ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

            Route::get('sample-invoices/{sampleInvoice}/pdf', [SampleInvoiceController::class, 'pdf'])
                ->name('sample-invoices.pdf');
        });

        /*
         * SALES INVOICES (LC / TT)
         */
        Route::resource('sales-invoices', SalesInvoiceController::class)
            ->names('sales-invoices')
            ->parameters(['sales-invoices' => 'salesInvoice']);

        Route::get('sales-invoices/{salesInvoice}/pdf', [SalesInvoiceController::class, 'pdf'])
            ->name('sales-invoices.pdf');
        Route::post(
            '/admin/sales-invoices/{salesInvoice}/preview',
            [SalesInvoiceController::class, 'previewEdit']
        )
            ->name('admin.sales-invoices.preview');



    });
});