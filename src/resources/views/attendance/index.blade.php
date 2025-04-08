@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-status">{{ $status }}</div>
    <div class="attendance-date">{{ \Carbon\Carbon::now()->format('Y年n月j日（D）') }}</div>
    <div class="attendance-time" id="clock">{{ \Carbon\Carbon::now()->format('H:i') }}</div>

    <div class="attendance-buttons">
        @if ($status === '勤務外')
            <form method="POST" action="{{ route('attendance.start') }}">@csrf
                <button type="submit" class="btn-black">出勤</button>
            </form>
        @elseif ($status === '出勤中')
            <form method="POST" action="{{ route('attendance.break_in') }}">@csrf
                <button type="submit" class="btn-gray">休憩開始</button>
            </form>
            <form method="POST" action="{{ route('attendance.end') }}">@csrf
                <button type="submit" class="btn-black">退勤</button>
            </form>
        @elseif ($status === '休憩中')
            <form method="POST" action="{{ route('attendance.break_out') }}">@csrf
                <button type="submit" class="btn-gray">休憩終了</button>
            </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/clock.js') }}"></script>
@endsection
