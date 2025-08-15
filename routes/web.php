<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


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
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
