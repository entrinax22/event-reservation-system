<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


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

    Route::get('/admin/users', function () {
        return Inertia::render('admin/tables/usersTable');
    })->name('admin.users.table');

    Route::get('/admin/users/list', [UserController::class, 'list'])->name('admin.users.list');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
