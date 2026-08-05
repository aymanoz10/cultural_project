<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AdminAuthController,
    CulturalCenterController,
    VenueController,
    VenueReservationController,
    LibraryController,
    ActivityController,
    ActivityTypeController,
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
    AiAssistantController,
    AiComparisonController,
    AiRecommendationController,
    DemoNotificationController,
};
use Illuminate\Http\Request;

Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp']);
Route::post('/register/resend-otp', [AuthController::class, 'resendRegisterOtp']);
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp']);

Route::post('/login/send-otp', [AuthController::class, 'sendLoginOtp']);
Route::post('/login/resend-otp', [AuthController::class, 'resendLoginOtp']);
Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);

Route::post('/admin/login', [AdminAuthController::class, 'login']);
// 🔒 أُزيل مسار /admin/register العلني (كان يسمح بإنشاء سوبر أدمن دون مصادقة).
// يُنشأ المشرفون عبر المسار المحمي POST /admin/admins (السوبر فقط) ضمن مجموعة auth:admin أدناه.

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'get']);
    Route::put('/profile', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/assistant/ask', [AiAssistantController::class, 'ask']);
    Route::get('/ai/for-you', [AiRecommendationController::class, 'forYou']);
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

    Route::post('/venue-reservations', [VenueReservationController::class, 'store']);

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
});

Route::post('/volunteerings', [VolunteeringController::class, 'add']);

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/profile', [AdminAuthController::class, 'get']);
    
    // مسار حفظ المستخدم الجديد من لوحة التحكم (يرتبط بدالة store)
    
    // تحديث الملف الشخصي للأدمن (يدعم POST و PUT معاً)
    Route::match(['post', 'put'], '/profile/update', [AdminAuthController::class, 'update'])->name('admin.profile.update');    
    
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/suggestions', [AdminSuggestionController::class, 'index']);
    Route::get('/volunteerings', [VolunteeringController::class, 'index']);
    Route::put('/volunteerings/{id}/status', [VolunteeringController::class, 'updateStatus']);

    Route::get('/venue-reservations', [VenueReservationController::class, 'index']);
    Route::put('/venue-reservations/{id}/status', [VenueReservationController::class, 'updateStatus']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);

    Route::post('/admins', [AdminAuthController::class, 'store']); // إنشاء مشرف جديد — يتحقق store من صلاحية السوبر
    Route::post('/admins/{id}', [AdminAuthController::class, 'edit']);
    Route::delete('/admins/{id}', [AdminAuthController::class, 'remove']);
});

Route::prefix('centers')->group(function () {
    Route::get('/', [CulturalCenterController::class, 'index']);
    Route::get('/{id}', [CulturalCenterController::class, 'show']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [CulturalCenterController::class, 'add']);
        Route::post('/{id}', [CulturalCenterController::class, 'edit']);
        Route::delete('/{id}', [CulturalCenterController::class, 'remove']);
        Route::post('/{id}/photos', [CulturalCenterController::class, 'addPhotos']);
        Route::delete('/photos/{photoId}', [CulturalCenterController::class, 'removePhoto']);
    });
});

Route::prefix('venues')->group(function () {
    Route::get('/', [VenueController::class, 'index']);
    Route::get('/{id}', [VenueController::class, 'show']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [VenueController::class, 'store']);
        Route::post('/{id}', [VenueController::class, 'update']);
        Route::delete('/{id}', [VenueController::class, 'destroy']);
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

Route::prefix('activity-types')->group(function () {
    Route::get('/', [ActivityTypeController::class, 'index']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('/', [ActivityTypeController::class, 'store']);
        Route::post('/{id}', [ActivityTypeController::class, 'update']);
        Route::delete('/{id}', [ActivityTypeController::class, 'destroy']);
    });
});

Route::prefix('activities')->group(function () {
    Route::get('/', [ActivityController::class, 'index']);
    Route::get('/finished', [ActivityController::class, 'finished']);
    Route::get('/coming', [ActivityController::class, 'coming']);
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
Route::post('/admin/register', [AdminAuthController::class, 'register']);
// مسارات الحجوزات (تتطلب تسجيل دخول)
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. عرض جميع حجوزات المستخدم
    Route::get('/reservations', [ReservationController::class, 'index']);

    // 2. إنشاء حجز جديد
    Route::post('/reservations', [ReservationController::class, 'add']);

    // 3. عرض تفاصيل حجز معين
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);

    // 4. إلغاء حجز
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    // 5. عرض قائمة الانتظار لفعالية محددة
    Route::get('/activities/{activityId}/waitlist', [ReservationController::class, 'waitList']);
});

Route::post('/ai/compare', [AiComparisonController::class, 'compare']);
