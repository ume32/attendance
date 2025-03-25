<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\AttendanceController;


Route::get('/register', [UserRegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [UserRegisterController::class, 'store'])
    ->middleware('guest');

Route::get('/attendance', [AttendanceController::class, 'index'])
