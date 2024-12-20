<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TableStatusController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::middleware('is_admin')->group(function () {

        // Admin Users
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Admin Tables
        Route::post('/tables', [TableController::class, 'store']);
        Route::put('/tables/{table}', [TableController::class, 'update']);
        Route::delete('/tables/{table}', [TableController::class, 'destroy']);

        // Admin Menus
        Route::post('/menus', [MenuController::class, 'store']);
        Route::put('/menus/{menu}', [MenuController::class, 'update']);
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy']);

        // Admin Categories
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Admin Config
        Route::get('/configs', [ConfigController::class, 'index']);
        Route::put('/configs', [ConfigController::class, 'update']);
    });

    // Admin Manager
    Route::middleware(['role:admin,manager'])->group(function () {
        // Reports
        // Route::get('/reports', [ReportController::class, 'index']);
        // Route::get('/reports/order-paid', [ReportController::class, 'orderPaidReports']);
        Route::get('/reports/tables', [ReportController::class, 'tableReports']);
        Route::get('/reports/menus', [ReportController::class, 'menuReports']);
        // Export
        Route::get('/reports/menus/excel', [ReportController::class, 'menuReportsExcel']);
        Route::get('/reports/menus/pdf', [ReportController::class, 'menuReportsPdf']);
        Route::get('/reports/tables/excel', [ReportController::class, 'tableReportsExcel']);
        Route::get('/reports/tables/pdf', [ReportController::class, 'tableReportsPdf']);
    });

    // Admin Cachier
    Route::middleware(['role:admin,cashier'])->group(function () {
        Route::put('/transactions/offline/confirm-payment/{transaction}', [TransactionController::class, 'confirmOfflinePayment']);

        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::get('/transactions/status/{id:order_id}', [TransactionController::class, 'transactionStatusMethod']);
    });

    // Admin Waiter
    Route::middleware(['role:admin,waiter'])->group(function () {
        Route::put('/table-statuses/{table}', [TableController::class, 'updateStatus']);
    });
});

// Guest
Route::middleware('guest')->group(function () {

    // Guest Login
    Route::post('/login', [AuthController::class, 'login']);

    // Guest Tables
    Route::get('/tables/{table:url}', [TableController::class, 'show']);

    // Guest Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    // Guest Transaction
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::post('/transactions/offline', [TransactionController::class, 'storeOffline']);
});

// Tables
Route::get('/tables', [TableController::class, 'index']);

// TableStatuses
Route::get('/table-statuses', [TableStatusController::class, 'index']);

// Menus
Route::get('/menus', [MenuController::class, 'index']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);

// XENDIT Notification Callback
Route::post('/transactions/callback', [TransactionController::class, 'notificationCallback']);

// QrCode Generate
Route::post('/generate-qrcode', [QrCodeController::class, 'generateQrCode']);
