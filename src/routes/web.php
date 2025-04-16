<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\UserCorrectionRequestController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminCorrectionRequestController;

Route::get('/', fn() => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/register', [UserRegisterController::class, 'create'])->name('register');
    Route::post('/register', [UserRegisterController::class, 'store']);
    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserLoginController::class, 'login'])->name('login.post');

    Route::prefix('admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('attendance')->group(function () {
        Route::get('/', [UserAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/start', [UserAttendanceController::class, 'start'])->name('attendance.start');
        Route::post('/break-in', [UserAttendanceController::class, 'breakIn'])->name('attendance.break_in');
        Route::post('/break-out', [UserAttendanceController::class, 'breakOut'])->name('attendance.break_out');
        Route::post('/end', [UserAttendanceController::class, 'end'])->name('attendance.end');

        Route::get('/list', [UserAttendanceController::class, 'list'])->name('attendance.list');
        Route::get('/{id}', [UserAttendanceController::class, 'show'])->name('attendance.show');
        Route::put('/{id}/note', [UserAttendanceController::class, 'updateNote'])->name('attendance.note.update');
        Route::get('/{id}/edit', [UserAttendanceController::class, 'edit'])->name('correction.edit');
        Route::post('/{id}/edit', [UserAttendanceController::class, 'update'])->name('correction.update');
    });

    Route::prefix('correction')->group(function () {
        Route::get('/{id}/edit', [UserCorrectionRequestController::class, 'edit'])->name('correction.edit');
        Route::post('/{id}', [UserCorrectionRequestController::class, 'update'])->name('correction.update');
    });

    Route::get('/stamp_correction_request/list', [UserCorrectionRequestController::class, 'index'])->name('correction.list');

    Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::prefix('attendance')->group(function () {
        Route::get('/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
        Route::get('/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
        Route::put('/{id}/note', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
        Route::get('/staff/{id}', [AdminStaffController::class, 'staffAttendance'])->name('admin.attendance.staff');
        Route::get('/staff/{id}/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
    });

    Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');

    Route::prefix('stamp_correction_request')->group(function () {
        Route::get('/list', [AdminCorrectionRequestController::class, 'index'])->name('admin.corrections.index');
        Route::get('/show/{id}', [AdminCorrectionRequestController::class, 'show'])->name('admin.corrections.show');
        Route::post('/approve/{id}', [AdminCorrectionRequestController::class, 'approve'])->name('admin.corrections.approve');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
