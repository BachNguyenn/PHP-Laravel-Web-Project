<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\SettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Member routes
    Route::apiResource('members', MemberController::class);

    // Expense routes
    Route::apiResource('expenses', ExpenseController::class);

    // Invoice routes
    Route::apiResource('invoices', InvoiceController::class)->only(['index', 'show', 'store']);

    // Settings routes
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);
});
