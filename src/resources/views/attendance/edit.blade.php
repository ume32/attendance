@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/attendance_edit.css') }}">
@endsection

@section('content')
<h2 class="page-title">修正申請</h2>

<form method="POST" action="{{ route('correction.update', $attendance->id) }}">
    @csrf

    <div class="form-group">
        <label>出勤時間</label>
        <input type="time" name="start_time" value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}">
    </div>

    <div class="form-group">
        <label>退勤時間</label>
        <input type="time" name="end_time" value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}">
    </div>

    <div class="form-group">
        <label>休憩時間（分）</label>
        <input type="number" name="break_minutes" value="{{ old('break_minutes', $attendance->breaks->sum(function($break) {
            return $break->break_end && $break->break_start ? $break->break_end->diffInMinutes($break->break_start) : 0;
        })) }}">
    </div>

    <div class="form-group">
        <label>申請理由</label>
        <textarea name="reason" rows="3" required>{{ old('reason') }}</textarea>
    </div>

    <button type="submit" class="btn-submit">申請する</button>
</form>
@endsection
