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

        $status = '勤務外';
        if ($attendance) {
            if ($attendance->end_time) {
                $status = '勤務外';
            } elseif ($attendance->breaks->last() && !$attendance->breaks->last()->break_end) {
                $status = '休憩中';
            } else {
                $status = '出勤中';
            }
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
        $targetDate = $request->query('month')
            ? Carbon::parse($request->query('month') . '-01')
            : Carbon::now();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereMonth('date', $targetDate->month)
            ->whereYear('date', $targetDate->year)
            ->orderBy('date')
            ->get()
            ->map(function ($attendance) {
                $totalBreak = $attendance->breaks->sum(function ($break) {
                    return $break->break_end && $break->break_start
                        ? Carbon::parse($break->break_end)->diffInMinutes($break->break_start)
                        : 0;
                });

                $workMinutes = 0;
                if ($attendance->start_time && $attendance->end_time) {
                    $total = Carbon::parse($attendance->end_time)->diffInMinutes($attendance->start_time);
                    $workMinutes = $total - $totalBreak;
                }

                $attendance->break_time = $totalBreak > 0
                    ? floor($totalBreak / 60) . ':' . str_pad($totalBreak % 60, 2, '0', STR_PAD_LEFT)
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
        Attendance::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()],
            ['start_time' => Carbon::now()]
        );

        return redirect()->route('attendance.index');
    }

    public function breakIn()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance) {
            $attendance->breaks()->create(['break_start' => Carbon::now()]);
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
            $attendance->breaks->last()->update(['break_end' => Carbon::now()]);
        }

        return redirect()->route('attendance.index');
    }

    public function end()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance && !$attendance->end_time) {
            $attendance->update(['end_time' => Carbon::now()]);
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

        CorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'new_start_time' => $request->start_time,
            'new_end_time' => $request->end_time,
            'note' => $request->note,
            'status' => '承認待ち',
            'new_breaks' => json_encode($request->breaks),
        ]);

        session()->flash('updated_data', [
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'note' => $request->note,
            'breaks' => $request->breaks,
        ]);

        return redirect()->route('attendance.show', $attendance->id)
            ->with('message', '修正申請を送信しました。');
    }
}
