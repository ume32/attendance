<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminCorrectionRequestController extends Controller
{
    /**
     * 申請一覧表示（承認待ち／承認済み）
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルト: 承認待ち

        $corrections = CorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status === 'approved' ? '承認済み' : '承認待ち')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.corrections.index', compact('corrections', 'status'));
    }

    /**
     * 申請詳細表示
     */
    public function show($id)
    {
        $correction = CorrectionRequest::with(['user', 'attendance.breaks'])->findOrFail($id);
        $attendance = $correction->attendance;

        return view('admin.corrections.show', compact('correction', 'attendance'));
    }

    /**
     * 申請承認処理
     */
    public function approve($id)
    {
        $correction = CorrectionRequest::with('attendance')->findOrFail($id);

        // 勤怠情報に修正内容を反映
        $attendance = $correction->attendance;
        $attendance->start_time = $correction->new_start_time ?? $attendance->start_time;
        $attendance->end_time   = $correction->new_end_time ?? $attendance->end_time;
        $attendance->note       = $correction->note ?? $attendance->note;
        $attendance->save();

        // 修正申請を「承認済み」に更新
        $correction->status = '承認済み';
        $correction->save();

        return redirect()->route('admin.corrections.index')->with('status', '申請を承認しました');
    }
}
