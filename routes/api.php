<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Http\Request;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/salaries', [SalaryController::class, 'storeOrUpdateByEmail']);
    Route::get('/salaries/{email}', [SalaryController::class, 'showByEmail']);
});

// Admin-only routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/salaries', [SalaryController::class, 'index']);
    Route::put('/salaries/{id}', [SalaryController::class, 'update']);
    Route::delete('/salaries/{id}', [SalaryController::class, 'destroy']);
    Route::get('/admin/verify-token', fn() => response()->json(['valid' => true]));
});
