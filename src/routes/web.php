<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\Auth\UserLoginController;

use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\UserCorrectionRequestController;


Route::get('/register', [UserRegisterController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [UserRegisterController::class, 'store'])
    ->middleware('guest');
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login'])->name('login.post');
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [UserAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [UserAttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/break-in', [UserAttendanceController::class, 'breakIn'])->name('attendance.break_in');
    Route::post('/attendance/break-out', [UserAttendanceController::class, 'breakOut'])->name('attendance.break_out');
    Route::post('/attendance/end', [UserAttendanceController::class, 'end'])->name('attendance.end');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/list', [UserAttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{id}', [UserAttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/correction/{id}/edit', [UserCorrectionRequestController::class, 'edit'])->name('correction.edit');
    Route::get('/attendance/{id}/edit', [UserAttendanceController::class, 'edit'])->name('correction.edit');
    Route::post('/attendance/{id}/edit', [UserAttendanceController::class, 'update'])->name('correction.update');
    Route::get('/stamp_correction_request/list', [UserCorrectionRequestController::class, 'index'])->name('correction.list');
});
