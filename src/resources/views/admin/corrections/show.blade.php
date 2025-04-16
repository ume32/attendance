@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/corrections/show.css') }}">
@endsection

@section('content')
<div class="attendance-detail__wrapper">
    <h2 class="page-title">勤怠詳細</h2>

    <table class="attendance-detail__table">
        <tr>
            <th>名前</th>
            <td colspan="2">{{ $correction->user->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td colspan="2">{{ \Carbon\Carbon::parse($correction->attendance->date)->format('Y年n月j日') }}</td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td colspan="2">
                {{ $correction->new_start_time
                    ? \Carbon\Carbon::parse($correction->new_start_time)->format('H:i')
                    : ($correction->attendance->start_time ? \Carbon\Carbon::parse($correction->attendance->start_time)->format('H:i') : '-') }}
                〜
                {{ $correction->new_end_time
                    ? \Carbon\Carbon::parse($correction->new_end_time)->format('H:i')
                    : ($correction->attendance->end_time ? \Carbon\Carbon::parse($correction->attendance->end_time)->format('H:i') : '-') }}
            </td>
        </tr>

        @foreach ($correction->attendance->breaks as $index => $break)
        <tr>
            <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
            <td colspan="2">
                {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}
                〜
                {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}
            </td>
        </tr>
        @endforeach

        <tr>
            <th>備考</th>
            <td colspan="2">{{ $correction->note ?? $correction->attendance->note ?? '-' }}</td>
        </tr>
    </table>

    <div class="btn-wrapper">
        @if ($correction->status === '承認待ち')
            <form method="POST" action="{{ route('admin.corrections.approve', $correction->id) }}">
                @csrf
                <button type="submit" class="btn-approve">承認</button>
            </form>
        @else
            <p class="approved-label">承認済み</p>
        @endif
    </div>
</div>
@endsection
