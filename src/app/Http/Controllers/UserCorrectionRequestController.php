<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class UserCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // デフォルトは「承認待ち」
        $user = Auth::user();

        $corrections = CorrectionRequest::with(['user', 'attendance'])
            ->where('user_id', $user->id)
            ->where('status', $status === 'approved' ? '承認済み' : '承認待ち')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('corrections.index', compact('corrections'));
    }

    public function edit($id)
    {
        $attendance = Attendance::with('breaks', 'correctionRequests')->findOrFail($id);

        return view('correction_request.edit', compact('attendance'));
    }

    /**
     * 修正申請の保存
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'new_start_time' => 'required|date_format:H:i',
            'new_end_time' => 'required|date_format:H:i|after:new_start_time',
            'note' => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);

        // すでに申請中がある場合は2重登録を防ぐ
        if ($attendance->correctionRequests()->where('status', 0)->exists()) {
            return redirect()->route('attendance.show', $id)->with('error', 'すでに承認待ちの申請があります。');
        }

        CorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'new_start_time' => Carbon::parse($request->new_start_time),
            'new_end_time' => Carbon::parse($request->new_end_time),
            'note' => $request->note,
            'status' => 0, // 承認待ち
        ]);

        return redirect()->route('attendance.show', $id)->with('message', '修正申請を送信しました。');
    }
}
