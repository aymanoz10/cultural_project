<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CulturalCenterController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\TheaterController;
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
    
    // 🎫 1. مسارات مسؤول التذاكر (TicketsAdmin)
    Route::middleware(['admin.role:super,admin,ticketsAdmin'])->group(function () {
        Route::view('/barcode/scan', 'admin.barcode.scan')->name('barcode.scan');
    });

    // 🛡️ 2. مسارات المشرف العام والأدمن (Super & Admin)
    Route::middleware(['admin.role:super,admin'])->group(function () {
        
        // اللوحة الرئيسية
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

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

        // قسم القاعات
        Route::get('/halls', [HallController::class, 'index'])->name('halls.index');
        Route::get('/halls/create', [HallController::class, 'create'])->name('halls.create');
        Route::post('/halls', [HallController::class, 'store'])->name('halls.store');
        Route::get('/halls/{id}/edit', [HallController::class, 'editView'])->name('halls.edit');
        Route::put('/halls/{id}', [HallController::class, 'update'])->name('halls.update');
        Route::delete('/halls/{id}', [HallController::class, 'destroy'])->name('halls.destroy');

        // قسم المسارح
        Route::get('/theaters', [TheaterController::class, 'index'])->name('theaters.index');
        Route::get('/theaters/create', [TheaterController::class, 'create'])->name('theaters.create');
        Route::post('/theaters', [TheaterController::class, 'store'])->name('theaters.store');
        Route::get('/theaters/{id}/edit', [TheaterController::class, 'editView'])->name('theaters.edit');
        Route::put('/theaters/{id}', [TheaterController::class, 'update'])->name('theaters.update');
        Route::delete('/theaters/{id}', [TheaterController::class, 'destroy'])->name('theaters.destroy');

        // قسم المكتبات والكتب
        Route::get('/libraries', [LibraryController::class, 'index'])->name('libraries.index');
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

Route::post('/test-profile', [AdminAuthController::class, 'update'])->middleware(['auth:admin'])->name('test.profile');