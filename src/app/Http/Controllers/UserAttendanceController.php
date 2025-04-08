<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakModel;
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

        // ステータス判定
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

    public function start(Request $request)
    {
        $user = Auth::user();
        Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date' => Carbon::today()
        ], [
            'start_time' => Carbon::now()
        ]);

        return redirect()->route('attendance.index');
    }

    public function breakIn()
    {
        $attendance = Attendance::where('user_id', Auth::id())
                                ->whereDate('date', Carbon::today())
                                ->first();

        if ($attendance) {
            $attendance->breaks()->create([
                'break_start' => Carbon::now()
            ]);
        }

        return redirect()->route('attendance.index');
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

    public function end()
    {
        $attendance = Attendance::where('user_id', Auth::id())
                                ->whereDate('date', Carbon::today())
                                ->first();

        if ($attendance && !$attendance->end_time) {
            $attendance->update([
                'end_time' => Carbon::now()
            ]);
        }

        return redirect()->route('attendance.index');
    }
}
