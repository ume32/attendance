@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-detail__wrapper">
    <h2 class="page-title">勤怠詳細</h2>

    <table class="attendance-detail__table">
        <tr>
            <th>名前</th>
            <td colspan="2">{{ $attendance->user->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</td>
            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}</td>
        </tr>
        @foreach ($attendance->breaks as $index => $break)
        <tr>
            <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
            <td>{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}</td>
        </tr>
        @endforeach
        <tr>
            <th>備考</th>
            <td colspan="2">
                {{ optional($attendance->correctionRequests->first())->reason ?? 'なし' }}
            </td>
        </tr>
    </table>

    @if(optional($attendance->correctionRequests->first())->status !== '承認待ち')
        <div class="text-right mt-4">
            <a href="{{ route('correction.edit', ['id' => $attendance->id]) }}" class="btn">修正</a>
        </div>
    @else
        <p class="attention">＊ 承認待ちのため修正はできません。</p>
    @endif
</div>
@endsection
