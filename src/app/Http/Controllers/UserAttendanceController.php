<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakModel;
use App\Models\CorrectionRequest;
use Carbon\Carbon;

class UserAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $status = '勤務外';
        } elseif ($attendance->end_time) {
            $status = '勤務外';
        } elseif ($attendance->breaks->last() && !$attendance->breaks->last()->break_end) {
            $status = '休憩中';
        } else {
            $status = '出勤中';
        }

        return view('attendance.index', [
            'attendance' => $attendance,
            'now' => Carbon::now(),
            'status' => $status,
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breaks', 'correctionRequests'])->findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        $month = $request->query('month');
        $targetDate = $month ? Carbon::parse($month . '-01') : Carbon::now();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereMonth('date', $targetDate->month)
            ->whereYear('date', $targetDate->year)
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                    return $break->break_end && $break->break_start
                        ? Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start))
                        : 0;
                });

                $workMinutes = 0;
                if ($attendance->start_time && $attendance->end_time) {
                    $totalMinutes = Carbon::parse($attendance->end_time)->diffInMinutes(Carbon::parse($attendance->start_time));
                    $workMinutes = $totalMinutes - $totalBreakMinutes;
                }

                $attendance->break_time = $totalBreakMinutes > 0
                    ? floor($totalBreakMinutes / 60) . ':' . str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT)
                    : '-';

                $attendance->total_time = $workMinutes > 0
                    ? floor($workMinutes / 60) . ':' . str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT)
                    : '-';

                return $attendance;
            });

        return view('attendance.list', [
            'attendances' => $attendances,
            'currentMonth' => $targetDate->format('Y年n月'),
            'prevMonth' => $targetDate->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $targetDate->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function start()
    {
        Attendance::firstOrCreate([
            'user_id' => Auth::id(),
            'date' => Carbon::today()
        ], [
            'start_time' => Carbon::now(),
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
                'break_start' => Carbon::now(),
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
                'break_end' => Carbon::now(),
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
                'end_time' => Carbon::now(),
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i',
        ]);

        $attendance = Attendance::with('breaks')->findOrFail($id);

        // 修正申請として保存
        CorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'new_start_time' => $request->start_time,
            'new_end_time' => $request->end_time,
            'note' => $request->note,
            'status' => '承認待ち',
        ]);

        return redirect()->route('attendance.show', $id)->with('message', '修正申請を送信しました。');
    }
}
