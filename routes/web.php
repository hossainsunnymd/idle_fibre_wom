<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\SessionAuthenticateMiddleware;




// ================== User Routes ==================

Route::post('/user-login', [AuthController::class, 'userLogin'])->name('userLogin');
Route::get('/user-logout', [AuthController::class, 'userLogout'])->name('userLogout');

// User Page Routes
Route::get('/', [AuthController::class, 'loginPage'])->name('loginPage');


// ================== Middleware Protected Routes ==================
Route::middleware([SessionAuthenticateMiddleware::class])->group(function () {


    // Sale page Routes
    Route::get('/sale-page', [InvoiceController::class, 'salePage'])->name('salePage');



    Route::get('/profile-page', [ProfileController::class, 'profilePage'])->name('profilePage');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('updateProfile');

    // Customer Routes
    Route::post('/create-customer', [CustomerController::class, 'createCustomer'])->name('createCustomer');
    Route::post('/update-customer', [CustomerController::class, 'updateCustomer'])->name('updateCustomer');
    Route::get('/delete-customer', [CustomerController::class, 'deleteCustomer'])->name('deleteCustomer');

    // Product Routes
    Route::post('/create-product', [ProductController::class, 'createProduct'])->name('createProduct');
    Route::post('/update-product', [ProductController::class, 'updateProduct'])->name('updateProduct');
    Route::get('/delete-product', [ProductController::class, 'deleteProduct'])->name('deleteProduct');

    // Invoice Routes
    Route::post('/create-invoice', [InvoiceController::class, 'createInvoice'])->name('createInvoice');
    Route::get('/delete-invoice', [InvoiceController::class, 'deleteInvoice'])->name('deleteInvoice');

});

// ================== Page Routes ==================
Route::middleware([SessionAuthenticateMiddleware::class])->group(function () {
    // Customer Pages
    Route::get('/customer-page', [CustomerController::class, 'customerPage'])->name('customerPage');
    Route::get('/customer-save-page', [CustomerController::class, 'customerSavePage'])->name('customerSavePage');

    // Product Pages
    Route::get('/product-page', [ProductController::class, 'productPage'])->name('productPage');
    Route::get('/product-save-page', [ProductController::class, 'productSavePage'])->name('productSavePage');

    // Invoice Pages
    Route::get('/invoice-page', [InvoiceController::class, 'listInvoice'])->name('listInvoice');

});

Route::middleware([SessionAuthenticateMiddleware::class,SuperAdminMiddleware::class])->group(function () {

  //users
    Route::get('/user-page', [UserController::class, 'userPage'])->name('userPage');
    Route::get('/user-save-page', [UserController::class, 'userSavePage'])->name('userSavePage');
    Route::post('/create-user', [UserController::class, 'createUser'])->name('createUser');
    Route::post('/update-user', [UserController::class, 'updateUser'])->name('updateUser');
    Route::get('/delete-user', [UserController::class, 'deleteUser'])->name('deleteUser');

    });

