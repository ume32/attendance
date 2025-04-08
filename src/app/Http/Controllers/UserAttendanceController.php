<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class UserAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with('breaks')
            ->first();

        // 勤務状態を判定
        $status = '勤務外';
        if ($attendance) {
            if ($attendance->end_time) {
                $status = '退勤済';
            } elseif ($attendance->breaks->last() && !$attendance->breaks->last()->break_end) {
                $status = '休憩中';
            } elseif ($attendance->start_time) {
                $status = '出勤中';
            }
        }

        return view('attendance.index', compact('status'));
    }

    public function start()
    {
        $user = Auth::user();

        Attendance::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => Carbon::today()
            ],
            [
                'start_time' => Carbon::now()
            ]
        );

        return redirect()->route('attendance.index')->with('message', '出勤打刻しました。');
    }
    // 勤怠一覧表示
    public function list(Request $request)
    {
        $user = Auth::user();

        // URLパラメータから対象月を取得（例: 2025-04）
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        // Carbonで月初・月末を取得
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // 表示する月のラベル（例: 2025年4月）
        $currentMonth = $startOfMonth->format('Y年n月');

        // 前月・翌月（URLリンク用）
        $prevMonth = $startOfMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $startOfMonth->copy()->addMonth()->format('Y-m');

        // 当該ユーザーの当月の勤怠データを取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('breaks')
            ->orderBy('date')
            ->get();

        return view('attendance.list', compact(
            'attendances',
            'currentMonth',
            'prevMonth',
            'nextMonth'
        ));
    }
    public function show($id)
    {
        $attendance = Attendance::with(['breaks', 'user', 'correctionRequests'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('attendance.show', compact('attendance'));
    }

    public function edit($id)
    {
        $attendance = Attendance::with('breaks')->findOrFail($id);

        return view('attendance.edit', compact('attendance'));
    }
    public function breakIn()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance) {
            $attendance->breaks()->create([
                'break_start' => Carbon::now()
            ]);
        }

        return redirect()->route('attendance.index')->with('message', '休憩開始を打刻しました。');
    }
    public function breakOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->with('breaks')
            ->first();

        if ($attendance && $attendance->breaks->last() && !$attendance->breaks->last()->break_end) {
            $attendance->breaks->last()->update([
                'break_end' => Carbon::now()
            ]);
        }

        return redirect()->route('attendance.index');
    }
}
