<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                    return ($break->break_start && $break->break_end)
                        ? Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start))
                        : 0;
                });

                $workMinutes = ($attendance->start_time && $attendance->end_time)
                    ? Carbon::parse($attendance->end_time)->diffInMinutes(Carbon::parse($attendance->start_time)) - $totalBreakMinutes
                    : 0;

                $attendance->break_time = $totalBreakMinutes
                    ? floor($totalBreakMinutes / 60) . ':' . str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT)
                    : '-';

                $attendance->total_time = $workMinutes
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

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breaks', 'correctionRequests'])->findOrFail($id);
        return view('admin.attendances.show', compact('attendance'));
    }

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

        if ($request->start_time) {
            $attendance->start_time = Carbon::parse($attendance->date . ' ' . $request->start_time);
        }
        if ($request->end_time) {
            $attendance->end_time = Carbon::parse($attendance->date . ' ' . $request->end_time);
        }

        $attendance->note = $request->note;
        $attendance->save();

        foreach ($request->breaks ?? [] as $index => $break) {
            if (isset($attendance->breaks[$index])) {
                $attendance->breaks[$index]->update([
                    'break_start' => Carbon::parse($attendance->date . ' ' . $break['start']),
                    'break_end'   => Carbon::parse($attendance->date . ' ' . $break['end']),
                ]);
            }
        }

        CorrectionRequest::create([
            'user_id' => $attendance->user_id,
            'attendance_id' => $attendance->id,
            'new_start_time' => $request->start_time,
            'new_end_time' => $request->end_time,
            'note' => $request->note,
            'status' => CorrectionRequest::STATUS_PENDING,
        ]);

        return redirect()
            ->route('admin.corrections.show', $attendance->correctionRequests->last()->id)
            ->with('message', '修正内容を保存しました（即時反映済み）');
    }

    public function export($id, Request $request)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $csvHeader = ['日付', '出勤', '退勤', '休憩', '合計'];

        $response = new StreamedResponse(function () use ($attendances, $csvHeader) {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

            fputcsv($handle, $csvHeader);

            foreach ($attendances as $attendance) {
                $row = [
                    Carbon::parse($attendance->date)->format('Y/m/d'),
                    optional($attendance->start_time)->format('H:i'),
                    optional($attendance->end_time)->format('H:i'),
                    $attendance->break_time ?? '-',
                    $attendance->total_time ?? '-',
                ];
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="attendance.csv"');

        return $response;
    }
}
