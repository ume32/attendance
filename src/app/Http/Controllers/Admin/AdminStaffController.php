<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;


class AdminStaffController extends Controller
{
    public function index()
    {
        $users = User::all(); // 一般ユーザーすべてを取得
        return view('admin.staff.index', compact('users'));
    }
    public function staffAttendance($id, Request $request)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', now()->format('Y-m'));
        $date = \Carbon\Carbon::parse($month . '-01');

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            ])
            ->orderBy('date')
            ->get()
            ->map(function ($attendance) {
                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                    return $break->break_start && $break->break_end
                        ? \Carbon\Carbon::parse($break->break_end)->diffInMinutes($break->break_start)
                        : 0;
                });

                $workMinutes = 0;
                if ($attendance->start_time && $attendance->end_time) {
                    $total = \Carbon\Carbon::parse($attendance->end_time)->diffInMinutes($attendance->start_time);
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

        return view('admin.attendances.staff', [
            'user' => $user,
            'attendances' => $attendances,
            'currentMonth' => $date->format('Y/m'),
            'prevMonth' => $date->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $date->copy()->addMonth()->format('Y-m'),
        ]);
    }
}
