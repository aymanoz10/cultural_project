<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookFileController;
use App\Http\Controllers\CulturalCenterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketScanController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\VenueReservationController;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------------------------------
// 1️⃣ مسارات المصادقة والضيوف
// --------------------------------------------------------------------------
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AdminAuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::redirect('/', '/login');

// --------------------------------------------------------------------------
// 2️⃣ مسارات لوحة التحكم المحمية والمقسمة حسب الصلاحيات
// --------------------------------------------------------------------------
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {

    // 🔔 إشعارات لوحة التحكم (متاحة لكل المشرفين المسجّلين)
    Route::get('/notifications',            [AdminNotificationController::class, 'page'])->name('notifications.index');
    Route::get('/notifications/feed',       [AdminNotificationController::class, 'feed'])->name('notifications.feed');
    Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',  [AdminNotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // 🎫 1. مسارات مسؤول التذاكر (TicketsAdmin)
    Route::middleware(['admin.role:super,admin,ticketsAdmin'])->group(function () {
        Route::view('/barcode/scan', 'admin.barcode.scan')->name('barcode.scan');
        Route::post('/barcode/verify', [TicketScanController::class, 'verify'])->name('barcode.verify');
    });

    // 🛡️ 2. مسارات المشرف العام والأدمن (Super & Admin)
    Route::middleware(['admin.role:super,admin'])->group(function () {
        
        // اللوحة الرئيسية
        Route::get('/dashboard', [DashboardController::class, 'web'])->name('dashboard');

        // الملف الشخصي
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [AdminAuthController::class, 'update'])->name('profile.update');

        // قسم الفعاليات
        Route::get('/events', [ActivityController::class, 'index'])->name('events.index');
        Route::get('/events/create', [ActivityController::class, 'create'])->name('events.create');
        Route::post('/events', [ActivityController::class, 'add'])->name('events.store');
        Route::get('/events/{id}', [ActivityController::class, 'show'])->name('events.show');
        Route::get('/events/{id}/edit', [ActivityController::class, 'editView'])->name('events.edit');
        Route::put('/events/{id}', [ActivityController::class, 'edit'])->name('events.update');
        Route::delete('/events/{id}', [ActivityController::class, 'remove'])->name('events.destroy');        

        // قسم المراكز الثقافية
        Route::get('/cultural-centers', [CulturalCenterController::class, 'index'])->name('cultural_centers.index');
        Route::get('/cultural-centers/create', [CulturalCenterController::class, 'create'])->name('cultural_centers.create');
        Route::post('/cultural-centers', [CulturalCenterController::class, 'add'])->name('cultural_centers.store');
        Route::get('/cultural-centers/{id}/edit', [CulturalCenterController::class, 'editView'])->name('cultural_centers.edit');
        Route::put('/cultural-centers/{id}', [CulturalCenterController::class, 'edit'])->name('cultural_centers.update');
        Route::delete('/cultural-centers/{id}', [CulturalCenterController::class, 'remove'])->name('cultural_centers.destroy');
        Route::post('/cultural-centers/{id}/photos', [CulturalCenterController::class, 'addPhotos'])->name('cultural_centers.photos.store');
        Route::delete('/cultural-centers/photos/{id}', [CulturalCenterController::class, 'removePhoto'])->name('cultural_centers.photos.destroy');

        // 🏛️ قسم القاعات والمرافق (Venues الموحد بدلاً من القاعات والمسارح القديمة)
        Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
        Route::get('/venues/create', [VenueController::class, 'create'])->name('venues.create');
        Route::post('/venues', [VenueController::class, 'store'])->name('venues.store');
        Route::get('/venues/{id}/edit', [VenueController::class, 'edit'])->name('venues.edit');
        Route::put('/venues/{id}', [VenueController::class, 'update'])->name('venues.update');
        Route::delete('/venues/{id}', [VenueController::class, 'destroy'])->name('venues.destroy');

        // 📖 قسم المكتبات (CRUD كامل عبر AJAX / JSON — واجهة إدارة)
        Route::get('/libraries',            [LibraryController::class, 'webIndex'])->name('libraries.index');
        Route::get('/libraries/create',     [LibraryController::class, 'create'])->name('libraries.create');
        Route::post('/libraries',           [LibraryController::class, 'store'])->name('libraries.store');
        Route::get('/libraries/{id}',       [LibraryController::class, 'show'])->name('libraries.show');
        Route::get('/libraries/{id}/edit',  [LibraryController::class, 'editView'])->name('libraries.edit');
        Route::put('/libraries/{id}',       [LibraryController::class, 'update'])->name('libraries.update');
        Route::delete('/libraries/{id}',    [LibraryController::class, 'destroy'])->name('libraries.destroy');

        // 📚 قسم الكتب (CRUD كامل عبر AJAX / JSON)
        Route::get('/books',            [BookController::class, 'index'])->name('books.index');
        Route::get('/books/create',     [BookController::class, 'create'])->name('books.create');
        Route::post('/books',           [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{id}',       [BookController::class, 'show'])->name('books.show');
        Route::get('/books/{id}/edit',  [BookController::class, 'editView'])->name('books.edit');
        Route::put('/books/{id}',       [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{id}',    [BookController::class, 'destroy'])->name('books.destroy');

        // 📄 ملف الكتاب (PDF): قراءة داخل المتصفح / تحميل — يُقدَّم من قرص خاص عبر متحكّم
        Route::get('/books/{book}/read',     [BookFileController::class, 'read'])->name('books.read');
        Route::get('/books/{book}/download', [BookFileController::class, 'download'])->name('books.download');

        // 🗓️ قسم حجوزات القاعات (إدارة آلة الحالات)
        Route::get('/venue-reservations',             [VenueReservationController::class, 'webIndex'])->name('venue_reservations.index');
        Route::get('/venue-reservations/{id}',        [VenueReservationController::class, 'showWeb'])->name('venue_reservations.show');
        Route::put('/venue-reservations/{id}/status', [VenueReservationController::class, 'updateStatus'])->name('venue_reservations.status');

        // 📊 قسم التقارير وتصدير البيانات (super = كل المراكز، admin = مركزه فقط)
        Route::get('/reports',        [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // 👑 3. مسارات المشرف العام فقط (Super Admin) - إدارة المستخدمين والصلاحيات
    Route::middleware(['admin.role:super'])->group(function () {
        Route::get('/users', [AdminAuthController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminAuthController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminAuthController::class, 'store'])->name('users.store'); 
        
        Route::get('/users/{id}/edit', function ($id) {
            return view('admin.users.edit', compact('id'));
        })->name('users.edit');
            
        Route::put('/users/{id}', [AdminAuthController::class, 'edit'])->name('users.update');
        Route::delete('/users/{id}', [AdminAuthController::class, 'remove'])->name('users.destroy');
    });

}); // 🟢 نهاية مجموعة admin الرئيسية
use App\Http\Controllers\AdminReservationController;

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{id}/cancel', [AdminReservationController::class, 'cancel'])->name('reservations.cancel');
});