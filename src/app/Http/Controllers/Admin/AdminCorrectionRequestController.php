<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルトは「承認待ち」

        $corrections = CorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status === 'approved' ? '承認済み' : '承認待ち')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.corrections.index', compact('corrections', 'status'));
    }

    public function show($id)
    {
        $correction = CorrectionRequest::with(['user', 'attendance.breaks'])->findOrFail($id);

        return view('admin.corrections.show', compact('correction'));
    }

    public function approve($id)
    {
        $correction = CorrectionRequest::with(['attendance.breaks'])->findOrFail($id);
        $attendance = $correction->attendance;

        if ($correction->new_start_time) {
            $attendance->start_time = Carbon::parse($attendance->date . ' ' . $correction->new_start_time);
        }

        if ($correction->new_end_time) {
            $attendance->end_time = Carbon::parse($attendance->date . ' ' . $correction->new_end_time);
        }

        if ($correction->note) {
            $attendance->note = $correction->note;
        }

        if ($correction->new_breaks) {
            $newBreaks = json_decode($correction->new_breaks, true);

            foreach ($newBreaks as $index => $break) {
                if (isset($attendance->breaks[$index])) {
                    $attendance->breaks[$index]->update([
                        'break_start' => Carbon::parse($attendance->date . ' ' . $break['start']),
                        'break_end'   => Carbon::parse($attendance->date . ' ' . $break['end']),
                    ]);
                }
            }
        }

        $attendance->save();
        $correction->status = '承認済み';
        $correction->save();

        return redirect()
            ->route('admin.corrections.show', $correction->id)
            ->with('message', '申請を承認しました');
    }
}
