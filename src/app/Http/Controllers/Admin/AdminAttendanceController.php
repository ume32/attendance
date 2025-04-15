<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();

        $attendances = Attendance::with(['user', 'breaks'])
            ->whereDate('date', $date)
            ->orderBy('user_id')
            ->get()
            ->map(function ($attendance) {
                // 休憩合計（分）
                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                    return $break->break_start && $break->break_end
                        ? Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start))
                        : 0;
                });

                // 合計労働時間（分）
                $workMinutes = 0;
                if ($attendance->start_time && $attendance->end_time) {
                    $total = Carbon::parse($attendance->end_time)->diffInMinutes(Carbon::parse($attendance->start_time));
                    $workMinutes = $total - $totalBreakMinutes;
                }

                $attendance->break_time = $totalBreakMinutes > 0
                    ? floor($totalBreakMinutes / 60) . ':' . str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT)
                    : '-';

                $attendance->total_time = $workMinutes > 0
                    ? floor($workMinutes / 60) . ':' . str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT)
                    : '-';

                return $attendance;
            });

        return view('admin.attendances.index', [
            'attendances' => $attendances,
            'date' => $date,
            'prevDate' => $date->copy()->subDay(),
            'nextDate' => $date->copy()->addDay(),
        ]);
    }
}
