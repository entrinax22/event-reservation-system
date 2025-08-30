<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MaterialController;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/business', function () {
        return Inertia::render('Business');
    })->name('business');

    Route::get('/notifications', function () {
        return Inertia::render('Notifications');
    })->name('notifications');

    Route::get('/mybookings', function () {
        return Inertia::render('MyBookings');
    })->name('mybookings');


});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return Inertia::render('admin/AdminDashboard');
    })->name('admin');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.table');
    Route::get('/admin/users/list', [UserController::class, 'list'])->name('admin.users.list');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');
    Route::post('/admin/users/update', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/destroy', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/events', [EventController::class, 'index'])->name('admin.events.table');
    Route::get('/admin/events/create', [EventController::class, 'create'])->name('admin.events.create');
    Route::get('/admin/events/list', [EventController::class, 'list'])->name('admin.events.list');
    Route::post('/admin/events/store', [EventController::class, 'store'])->name('admin.events.store');
    Route::post('/admin/events/update', [EventController::class, 'update'])->name('admin.events.update');
    Route::post('/admin/events/destroy', [EventController::class, 'destroy'])->name('admin.events.destroy');

    Route::get('/admin/materials', [MaterialController::class, 'index'])->name('admin.materials.table');
    Route::get('/admin/materials/create', [MaterialController::class, 'create'])->name('admin.materials.create');
    Route::get('/admin/materials/list', [MaterialController::class, 'list'])->name('admin.materials.list');
    Route::post('/admin/materials/store', [MaterialController::class, 'store'])->name('admin.materials.store');
    Route::post('/admin/materials/update', [MaterialController::class, 'update'])->name('admin.materials.update');
    Route::post('/admin/materials/destroy', [MaterialController::class, 'destroy'])->name('admin.materials.destroy');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
