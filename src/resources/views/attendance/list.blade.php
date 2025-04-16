@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance-list__wrapper">
    <h2 class="page-title">勤怠一覧</h2>

    <div class="month-nav">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}">← 前月</a>
        <span>{{ $currentMonth }}</span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d(D)') }}</td>
                    <td>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->break_time }}</td>
                    <td>{{ $attendance->total_time }}</td>
                    <td><a href="{{ route('attendance.show', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
