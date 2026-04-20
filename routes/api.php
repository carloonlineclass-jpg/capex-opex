<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\RequisitionController;
use App\Http\Controllers\Api\IssuanceController;
use App\Http\Controllers\Api\AllocationController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\AcquisitionController;

Route::name('api.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::apiResource('items', ItemController::class);
    Route::get('/items/{id}/qr', [ItemController::class, 'qr'])->name('items.qr');
    Route::get('/scan/{code}', [ItemController::class, 'lookupByCode'])->name('items.scan');
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('acquisitions', AcquisitionController::class);
    Route::apiResource('allocations', AllocationController::class);

    Route::get('/requisitions', [RequisitionController::class, 'index'])->name('requisitions.index');
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::get('/requisitions/{id}', [RequisitionController::class, 'show'])->name('requisitions.show');
    Route::post('/requisitions/{id}/approve', [RequisitionController::class, 'approve'])->name('requisitions.approve');
    Route::post('/requisitions/{id}/reject', [RequisitionController::class, 'reject'])->name('requisitions.reject');

    Route::get('/issuances', [IssuanceController::class, 'index'])->name('issuances.index');
    Route::post('/issuances', [IssuanceController::class, 'store'])->name('issuances.store');
    Route::post('/issuances/{id}/return', [IssuanceController::class, 'returnItem'])->name('issuances.return');

    Route::middleware('auth.jwt')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    });
});
