<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\UserCorrectionRequestController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminStaffController;

/**
 * 一般ユーザー認証不要ルート
 */
Route::get('/register', [UserRegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [UserRegisterController::class, 'store'])->middleware('guest');
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login'])->name('login.post');
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

/**
 * 一般ユーザー認証後ルート
 */
Route::middleware(['auth'])->group(function () {
    // 勤務登録系
    Route::get('/attendance', [UserAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [UserAttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/break-in', [UserAttendanceController::class, 'breakIn'])->name('attendance.break_in');
    Route::post('/attendance/break-out', [UserAttendanceController::class, 'breakOut'])->name('attendance.break_out');
    Route::post('/attendance/end', [UserAttendanceController::class, 'end'])->name('attendance.end');

    // 勤怠詳細・一覧など
    Route::get('/attendance/list', [UserAttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{id}', [UserAttendanceController::class, 'show'])->name('attendance.show');
    Route::put('/attendance/{id}/note', [UserAttendanceController::class, 'updateNote'])->name('attendance.note.update');
    Route::get('/attendance/{id}/edit', [UserAttendanceController::class, 'edit'])->name('correction.edit');
    Route::post('/attendance/{id}/edit', [UserAttendanceController::class, 'update'])->name('correction.update');

    // 修正申請
    Route::get('/correction/{id}/edit', [UserCorrectionRequestController::class, 'edit'])->name('correction.edit');
    Route::post('/correction/{id}', [UserCorrectionRequestController::class, 'update'])->name('correction.update');
    Route::get('/stamp_correction_request/list', [UserCorrectionRequestController::class, 'index'])->name('correction.list');
});

/**
 * 管理者ログイン
 */
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

/**
 * 管理者認証後ルート
 */
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::put('/attendance/{id}/note', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
});
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.list');
});
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/attendance/staff/{id}', [AdminStaffController::class, 'staffAttendance'])
        ->name('admin.attendance.staff');
});
