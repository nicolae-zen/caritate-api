<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Donation\DonationController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Cause\CauseController;
use App\Http\Controllers\Achievement\AchievementController;
use App\Http\Controllers\Report\ReportController;

// Admin Controller
use App\Http\Controllers\Admin\Cause\CauseController as AdminCauseController;
use App\Http\Controllers\Admin\Report\ReportController as AdminReportController;
use App\Http\Controllers\Admin\User\UserController as AdminUserController;
use App\Http\Controllers\Admin\Donation\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\Subscription\SubscriptionController as AdminSubscriptionController;

// Autentificare
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

// Cauze (public)
Route::get('/causes', [CauseController::class, 'index']);
Route::get('/causes/{id}', [CauseController::class, 'show']);

// Achievements (Realizari)
Route::get('/achievements', [AchievementController::class, 'index']);

// Rapoarte
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/{id}', [ReportController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    // Detalii profil
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

    // Donatii
    Route::get('/donations', [DonationController::class, 'index']);
    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/donations/{id}', [DonationController::class, 'show']);

    // Subscriptions (Abonamente)
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::put('/subscriptions{id}', [SubscriptionController::class, 'update']);
    Route::post('/subscriptions{id}/resume', [SubscriptionController::class, 'resume']);

    // Cauze
    

    // Achievements (Realizari)
    Route::get('/user/achievements', [AchievementController::class, 'userAchievements']);
});

// Rute Admin
Route::prefix('admin')->middleware(['auth:api', 'is_admin'])->group(function () {
    // Cauze
    Route::get('/causes', [AdminCauseController::class, 'index']);
    Route::post('/causes', [AdminCauseController::class, 'store']);
    Route::put('/causes/{id}', [AdminCauseController::class, 'update']);
    Route::put('/causes/{id}/disable', [AdminCauseController::class, 'disable']);
    Route::put('/causes/{id}/activate', [AdminCauseController::class, 'activate']);
    Route::delete('/causes/{id}', [AdminCauseController::class, 'destroy']);

    // Rapoarte
    Route::get('/reports', [AdminReportController::class, 'index']);
    Route::post('/reports', [AdminReportController::class, 'store']);
    Route::post('/reports/{id}/publish', [AdminReportController::class, 'publish']);
    Route::post('/reports/{id}/unpublish', [AdminReportController::class, 'unpublish']);
    Route::put('/reports/{id}', [AdminReportController::class, 'update']);
    Route::delete('/reports/{id}', [AdminReportController::class, 'destroy']);

    // Users (Utilizatori)
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    // Donatii
    Route::get('/donations', [AdminDonationController::class, 'index']);

    // Subscriptions (Abonamente)
    Route::get('/donations', [AdminSubscriptionController::class, 'index']);
    Route::put('/donations/{id}/cancel', [AdminSubscriptionController::class, 'cancel']);
});