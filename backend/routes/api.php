<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AssistantController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/categories', [MetaController::class, 'categories']);
Route::get('/areas', [MetaController::class, 'areas']);
Route::post('/assistant/guest-chat', [AssistantController::class, 'guestChat']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/area/{id}', [ServiceController::class, 'byArea']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/services/{id}/available-slots', [ServiceController::class, 'availableSlots']);
Route::get('/services/{id}/reviews', [ReviewController::class, 'forService']);
Route::get('/providers/top', [ProviderController::class, 'top']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::patch('/me', [AuthController::class, 'updateProfile']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/assistant/chat', [AssistantController::class, 'chat']);

    Route::middleware('role:client')->group(function () {
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
        Route::get('/client/dashboard-analytics', [BookingController::class, 'clientAnalytics']);
        Route::get('/favorites', [ServiceController::class, 'myFavorites']);
        Route::post('/services/{id}/favorite', [ServiceController::class, 'toggleFavorite']);
    });

    Route::middleware('role:provider')->group(function () {
        Route::get('/provider/services', [ServiceController::class, 'providerIndex']);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{id}', [ServiceController::class, 'update']);
        Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

        Route::get('/provider/bookings', [BookingController::class, 'providerBookings']);
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
        Route::patch('/bookings/{id}/provider-complete', [BookingController::class, 'providerMarkCompleted']);
        Route::get('/provider/dashboard-analytics', [BookingController::class, 'providerAnalytics']);
    });

    
    Route::middleware('role:client')->group(function () {
        Route::patch('/bookings/{id}/client-approve', [BookingController::class, 'clientApproveCompleted']);
        Route::post('/bookings/{id}/review', [ReviewController::class, 'store']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/services', [AdminController::class, 'services']);
        Route::get('/bookings', [AdminController::class, 'bookings']);
        Route::get('/reviews', [AdminController::class, 'reviews']);
        Route::patch('/users/{id}/role', [AdminController::class, 'updateUserRole']);
        Route::patch('/users/{id}/active', [AdminController::class, 'setUserActive']);
        Route::patch('/services/{id}/availability', [AdminController::class, 'toggleServiceAvailability']);
        Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview']);
    });
});

