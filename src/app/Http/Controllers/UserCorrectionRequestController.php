<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class UserCorrectionRequestController extends Controller
{
    /**
     * 修正申請一覧画面の表示
     */
    public function index()
    {
        $user = Auth::user();

        $pendingRequests = CorrectionRequest::where('user_id', $user->id)
            ->where('status', '承認待ち')
            ->with(['user', 'attendance'])
            ->get();

        $approvedRequests = CorrectionRequest::where('user_id', $user->id)
            ->where('status', '承認済み')
            ->with(['user', 'attendance'])
            ->get();

        return view('correction_request.list', compact('pendingRequests', 'approvedRequests'));
    }

    /**
     * 修正申請フォーム表示
     */
    public function edit($id)
    {
        $attendance = Attendance::with(['breaks', 'correctionRequests'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('correction_request.edit', compact('attendance'));
    }

    /**
     * 修正申請の保存処理
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'new_start_time' => ['required', 'date_format:H:i'],
            'new_end_time' => ['required', 'date_format:H:i', 'after:new_start_time'],
            'note' => ['required', 'string', 'max:255'],
        ]);

        $attendance = Attendance::where('user_id', Auth::id())->findOrFail($id);

        CorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'new_start_time' => Carbon::parse($request->new_start_time),
            'new_end_time' => Carbon::parse($request->new_end_time),
            'note' => $request->note,
            'status' => '承認待ち',
        ]);

        return redirect()->route('correction.list')->with('message', '修正申請を送信しました。');
    }
}
