<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    // 勤怠一覧（/admin/attendance/list）
    public function index(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();

        $attendances = Attendance::with(['user', 'breaks'])
            ->whereDate('date', $date)
            ->orderBy('user_id')
            ->get()
            ->map(function ($attendance) {
                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                    return $break->break_start && $break->break_end
                        ? Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start))
                        : 0;
                });

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

    // 勤怠詳細（/admin/attendance/{id}）
    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breaks', 'correctionRequests'])->findOrFail($id);

        return view('admin.attendances.show', compact('attendance'));
    }

    // 勤怠修正（PUT /admin/attendance/{id}/note）
    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end' => 'nullable|date_format:H:i',
        ]);

        $attendance = Attendance::with('breaks')->findOrFail($id);

        // 出退勤時間更新
        if ($request->start_time) {
            $attendance->start_time = Carbon::parse($attendance->date . ' ' . $request->start_time);
        }
        if ($request->end_time) {
            $attendance->end_time = Carbon::parse($attendance->date . ' ' . $request->end_time);
        }

        // 備考
        $attendance->note = $request->note;
        $attendance->save();

        // 休憩時間の更新（既存のみ）
        foreach ($request->breaks ?? [] as $index => $break) {
            if (isset($attendance->breaks[$index])) {
                $attendance->breaks[$index]->update([
                    'break_start' => Carbon::parse($attendance->date . ' ' . $break['start']),
                    'break_end' => Carbon::parse($attendance->date . ' ' . $break['end']),
                ]);
            }
        }

        // 修正申請として「承認済み」で記録
        $correction = CorrectionRequest::create([
            'user_id' => $attendance->user_id,
            'attendance_id' => $attendance->id,
            'new_start_time' => $request->start_time,
            'new_end_time' => $request->end_time,
            'note' => $request->note,
            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        // ✅ 修正後の確認画面（admin用：修正詳細表示）に遷移
        return redirect()->route('admin.corrections.show', $correction->id)
            ->with('message', '修正内容を保存しました（即時反映済み）');
    }
}
