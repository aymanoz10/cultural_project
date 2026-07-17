<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AdminAuthController,
    CulturalCenterController,
    HallController,
    TheaterController,
    LibraryController,
    ActivityController,
    ReservationController,
    RatingController,
    SuggestionController,
    VolunteeringActivityController,
    VolunteeringController,
    AdController,
    DashboardController,
    AdminSuggestionController,
    NotificationController,
    DeviceTokenController,
    DemoNotificationController,
};
use Illuminate\Http\Request;

// ═══════════════════════════════════════
// Public Auth Routes (بدون توكن)
// ═══════════════════════════════════════
Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp']);
Route::post('/register/resend-otp', [AuthController::class, 'resendRegisterOtp']);
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp']);

Route::post('/login/send-otp', [AuthController::class, 'sendLoginOtp']);
Route::post('/login/resend-otp', [AuthController::class, 'resendLoginOtp']);
Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/register', [AdminAuthController::class, 'register']);

// ═══════════════════════════════════════
// Protected User Routes (يحتاج Bearer Token)
// ═══════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'get']);
    Route::put('/profile', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);
    Route::post('/notifications/test', [DemoNotificationController::class, 'sendTest']);

    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index']);
        Route::post('/', [ReservationController::class, 'add']);
        Route::get('/{id}', [ReservationController::class, 'show']);
        Route::post('/{id}/cancel', [ReservationController::class, 'cancel']);
    });

    Route::prefix('ratings')->group(function () {
        Route::post('/', [RatingController::class, 'add']);
        Route::put('/{id}', [RatingController::class, 'edit']);
        Route::delete('/{id}', [RatingController::class, 'remove']);
    });

    Route::prefix('suggestions')->group(function () {
        Route::get('/', [SuggestionController::class, 'index']);
        Route::post('/', [SuggestionController::class, 'add']);
        Route::put('/{id}', [SuggestionController::class, 'edit']);
        Route::delete('/{id}', [SuggestionController::class, 'remove']);
    });

    Route::prefix('volunteerings')->group(function () {
        Route::get('/', [VolunteeringController::class, 'index']);
        Route::post('/', [VolunteeringController::class, 'add']);
    });
});

// ═══════════════════════════════════════
// Protected Admin Routes
// ═══════════════════════════════════════
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/profile', [AdminAuthController::class, 'get']);
    Route::put('/profile', [AdminAuthController::class, 'update']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/suggestions', [AdminSuggestionController::class, 'index']);
    Route::put('/volunteerings/{id}/status', [VolunteeringController::class, 'updateStatus']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);

    Route::post('/admins/{id}', [AdminAuthController::class, 'edit']);
    Route::delete('/admins/{id}', [AdminAuthController::class, 'remove']);
});

// ═══════════════════════════════════════
// Public Centers/Theaters/Halls/Libraries
// ═══════════════════════════════════════
Route::prefix('centers')->group(function () {
    Route::get('/', [CulturalCenterController::class, 'index']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [CulturalCenterController::class, 'add']);
        Route::post('/{id}', [CulturalCenterController::class, 'edit']);
        Route::delete('/{id}', [CulturalCenterController::class, 'remove']);
    });
});

Route::prefix('theaters')->group(function () {
    Route::get('/', [TheaterController::class, 'index']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [TheaterController::class, 'add']);
        Route::post('/{id}', [TheaterController::class, 'edit']);
        Route::delete('/{id}', [TheaterController::class, 'remove']);
    });
});

Route::prefix('halls')->group(function () {
    Route::get('/', [HallController::class, 'index']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [HallController::class, 'add']);
        Route::post('/{id}', [HallController::class, 'edit']);
        Route::delete('/{id}', [HallController::class, 'remove']);
    });
});

Route::prefix('libraries')->group(function () {
    Route::get('/', [LibraryController::class, 'index']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [LibraryController::class, 'add']);
        Route::post('/{id}', [LibraryController::class, 'edit']);
        Route::delete('/{id}', [LibraryController::class, 'remove']);
    });
});

Route::prefix('activities')->group(function () {
    Route::get('/', [ActivityController::class, 'index']);
    Route::get('/{activityId}/wait-list', [ReservationController::class, 'waitList']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [ActivityController::class, 'add']);
        Route::post('/{id}', [ActivityController::class, 'edit']);
        Route::delete('/{id}', [ActivityController::class, 'remove']);
    });
});

Route::prefix('volunteering-activities')->group(function () {
    Route::get('/', [VolunteeringActivityController::class, 'index']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [VolunteeringActivityController::class, 'add']);
        Route::post('/{id}', [VolunteeringActivityController::class, 'edit']);
        Route::delete('/{id}', [VolunteeringActivityController::class, 'remove']);
    });
});

Route::get('/ads', [AdController::class, 'index']);

Route::middleware('auth:admin')->prefix('ads')->group(function () {
    Route::post('/add', [AdController::class, 'add']);
    Route::post('/{id}', [AdController::class, 'edit']);
    Route::delete('/{id}', [AdController::class, 'remove']);
});

Route::get('/ratings', [RatingController::class, 'index']);