@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendances/index.css') }}">
@endsection

@section('content')
<div class="attendance-list__wrapper">
    <h2 class="page-title">{{ $date->format('Y年n月j日') }}の勤怠</h2>

    <div class="day-nav">
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate->format('Y-m-d')]) }}">← 前日</a>
        <span class="current-date">{{ $date->format('Y/m/d') }}</span>
        <a href="{{ route('admin.attendance.list', ['date' => $nextDate->format('Y-m-d')]) }}">翌日 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
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
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}</td>
                <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</td>
                <td>{{ $attendance->break_time ?? '-' }}</td>
                <td>{{ $attendance->total_time ?? '-' }}</td>
                <td><a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
