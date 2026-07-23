<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CulturalCenterController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\TheaterController;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------------------------------
// 1️⃣ مسارات المصادقة (Auth Routes)
// --------------------------------------------------------------------------
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
// إعادة توجيه الصفحة الرئيسية إلى لوحة التحكم
Route::redirect('/', '/admin/dashboard');

// --------------------------------------------------------------------------
// 2️⃣ مسارات لوحة التحكم (Admin Panel Routes)
// --------------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    
    // اللوحة الرئيسية
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
        })->name('dashboard');
        
        // 🔹 قسم الفعاليات (Events)
        Route::get('/events', [ActivityController::class, 'index'])->name('events.index');
            Route::get('/events/create', [ActivityController::class, 'create'])->name('events.create');
            Route::post('/events', [ActivityController::class, 'add'])->name('events.store');
            Route::get('/events/{id}', [ActivityController::class, 'show'])->name('events.show');
            Route::get('/events/{id}/edit', [ActivityController::class, 'editView'])->name('events.edit');
            Route::put('/events/{id}', [ActivityController::class, 'edit'])->name('events.update');
            Route::delete('/events/{id}', [ActivityController::class, 'remove'])->name('events.destroy');        
        

Route::get('/cultural-centers', [CulturalCenterController::class, 'index'])->name('cultural_centers.index');
    Route::get('/cultural-centers/create', [CulturalCenterController::class, 'create'])->name('cultural_centers.create');
    Route::post('/cultural-centers', [CulturalCenterController::class, 'add'])->name('cultural_centers.store');
    Route::get('/cultural-centers/{id}/edit', [CulturalCenterController::class, 'editView'])->name('cultural_centers.edit');
    Route::put('/cultural-centers/{id}', [CulturalCenterController::class, 'edit'])->name('cultural_centers.update');
    Route::delete('/cultural-centers/{id}', [CulturalCenterController::class, 'remove'])->name('cultural_centers.destroy');
    Route::post('/cultural-centers/{id}/photos', [CulturalCenterController::class, 'addPhotos'])->name('cultural_centers.photos.store');
    Route::delete('/cultural-centers/photos/{id}', [CulturalCenterController::class, 'removePhoto'])->name('cultural_centers.photos.destroy');
    // 🔹 قسم القاعات (Halls)
    Route::get('/halls', [HallController::class, 'index'])->name('halls.index');
    Route::get('/halls/create', [HallController::class, 'create'])->name('halls.create');
    Route::post('/halls', [HallController::class, 'store'])->name('halls.store');
    Route::get('/halls/{id}/edit', [HallController::class, 'editView'])->name('halls.edit');
    Route::put('/halls/{id}', [HallController::class, 'update'])->name('halls.update');
    Route::delete('/halls/{id}', [HallController::class, 'destroy'])->name('halls.destroy');


    // 🔹 قسم المسارح (Theaters)
    Route::get('/theaters', [TheaterController::class, 'index'])->name('theaters.index');
    Route::get('/theaters/create', [TheaterController::class, 'create'])->name('theaters.create');
    Route::post('/theaters', [TheaterController::class, 'store'])->name('theaters.store');
    Route::get('/theaters/{id}/edit', [TheaterController::class, 'editView'])->name('theaters.edit');
    Route::put('/theaters/{id}', [TheaterController::class, 'update'])->name('theaters.update');
    Route::delete('/theaters/{id}', [TheaterController::class, 'destroy'])->name('theaters.destroy');


    // 🔹 قسم المستخدمين (Users)
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/users/create', function () {
        return view('admin.users.create');
    })->name('users.create');

    Route::post('/users', function () {
        return redirect()->route('admin.users.index');
    })->name('users.store');

    Route::get('/users/{id}/edit', function () {
        return view('admin.users.edit');
    })->name('users.edit');

    Route::put('/users/{id}', function () {
        return redirect()->route('admin.users.index');
    })->name('users.update');

    Route::delete('/users/{id}', function () {
        return redirect()->route('admin.users.index');
    })->name('users.destroy');

});