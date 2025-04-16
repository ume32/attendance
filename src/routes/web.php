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

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [UserRegisterController::class, 'create'])->name('register');
    Route::post('/register', [UserRegisterController::class, 'store']);
    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserLoginController::class, 'login'])->name('login.post');
});

/**
|--------------------------------------------------------------------------
| 一般ユーザー：認証済みルート
|--------------------------------------------------------------------------
 */
Route::middleware('auth')->group(function () {
    // 勤務打刻
    Route::get('/attendance', [UserAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [UserAttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/break-in', [UserAttendanceController::class, 'breakIn'])->name('attendance.break_in');
    Route::post('/attendance/break-out', [UserAttendanceController::class, 'breakOut'])->name('attendance.break_out');
    Route::post('/attendance/end', [UserAttendanceController::class, 'end'])->name('attendance.end');

    // 勤怠一覧・詳細・編集
    Route::get('/attendance/list', [UserAttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{id}', [UserAttendanceController::class, 'show'])->name('attendance.show');
    Route::put('/attendance/{id}/note', [UserAttendanceController::class, 'updateNote'])->name('attendance.note.update');
    Route::get('/attendance/{id}/edit', [UserAttendanceController::class, 'edit'])->name('correction.edit');
    Route::post('/attendance/{id}/edit', [UserAttendanceController::class, 'update'])->name('correction.update');

    // 修正申請
    Route::get('/correction/{id}/edit', [UserCorrectionRequestController::class, 'edit'])->name('correction.edit');
    Route::post('/correction/{id}', [UserCorrectionRequestController::class, 'update'])->name('correction.update');
    Route::get('/stamp_correction_request/list', [UserCorrectionRequestController::class, 'index'])->name('correction.list');

    // ログアウト
    Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');
});

/**
|--------------------------------------------------------------------------
| 管理者：ログイン画面と認証処理
|--------------------------------------------------------------------------
 */
Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

/**
|--------------------------------------------------------------------------
| 管理者：認証済みルート
|--------------------------------------------------------------------------
 */
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    // 勤怠管理
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::put('/attendance/{id}/note', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

    // スタッフ管理
    Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/attendance/staff/{id}', [AdminStaffController::class, 'staffAttendance'])->name('admin.attendance.staff');

    // 修正申請管理
    Route::get('/stamp_correction_request/list', [AdminCorrectionRequestController::class, 'index'])->name('admin.corrections.index');
    Route::get('/stamp_correction_request/show/{id}', [AdminCorrectionRequestController::class, 'show'])->name('admin.corrections.show');
    Route::post('/stamp_correction_request/approve/{id}', [AdminCorrectionRequestController::class, 'approve'])->name('admin.corrections.approve');

    // ログアウト
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
Route::get('/admin/attendance/staff/{id}/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
