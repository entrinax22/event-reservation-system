<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ReservedEventController;
use App\Http\Controllers\AdminDashboardController;


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

    Route::get('/admin/dashboard/stats', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard.stats');
    Route::get('/admin/dashboard/generatePDF', [AdminDashboardController::class, 'generatePDFData'])->name('admin.generatePDFData');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.table');
    Route::get('/admin/users/list', [UserController::class, 'list'])->name('admin.users.list');
    Route::get('/admin/users/select-list', [UserController::class, 'selectList'])->name('admin.users.selectList');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');
    Route::post('/admin/users/update', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/destroy', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/events', [EventController::class, 'index'])->name('admin.events.table');
    Route::get('/admin/events/create', [EventController::class, 'create'])->name('admin.events.create');
    Route::get('/admin/events/list', [EventController::class, 'list'])->name('admin.events.list');
    Route::get('/admin/events/select-list', [EventController::class, 'selectList'])->name('admin.events.selectList');
    Route::post('/admin/events/store', [EventController::class, 'store'])->name('admin.events.store');
    Route::post('/admin/events/update', [EventController::class, 'update'])->name('admin.events.update');
    Route::post('/admin/events/destroy', [EventController::class, 'destroy'])->name('admin.events.destroy');

    Route::get('/admin/materials', [MaterialController::class, 'index'])->name('admin.materials.table');
    Route::get('/admin/materials/create', [MaterialController::class, 'create'])->name('admin.materials.create');
    Route::get('/admin/materials/list', [MaterialController::class, 'list'])->name('admin.materials.list');
    Route::get('/admin/materials/select-list', [MaterialController::class, 'selectList'])->name('admin.materials.selectList');
    Route::post('/admin/materials/store', [MaterialController::class, 'store'])->name('admin.materials.store');
    Route::post('/admin/materials/update', [MaterialController::class, 'update'])->name('admin.materials.update');
    Route::post('/admin/materials/destroy', [MaterialController::class, 'destroy'])->name('admin.materials.destroy');

    Route::get('/admin/reserved-events', [ReservedEventController::class, 'index'])->name('admin.reserved-events.table');
    Route::get('/admin/reserved-events/create', [ReservedEventController::class, 'create'])->name('admin.reserved-events.create');
    Route::get('/admin/reserved-events/list', [ReservedEventController::class, 'list'])->name('admin.reserved-events.list');
    Route::post('/admin/reserved-events/store', [ReservedEventController::class, 'store'])->name('admin.reserved-events.store');
    Route::post('/admin/reserved-events/update', [ReservedEventController::class, 'update'])->name('admin.reserved-events.update');
    Route::post('/admin/reserved-events/destroy', [ReservedEventController::class, 'destroy'])->name('admin.reserved-events.destroy');
});

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
