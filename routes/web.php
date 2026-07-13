<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('demo.index'));

Route::prefix('demo')->name('demo.')->group(function () {
    Route::get('/', [DemoController::class, 'index'])->name('index');
    Route::get('/user', [DemoController::class, 'user'])->name('user');
    Route::get('/admin', [DemoController::class, 'admin'])->name('admin');
});
