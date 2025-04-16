@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendances/show.css') }}">
@endsection

@section('content')
<div class="attendance-detail__wrapper">
    <h2 class="page-title">勤怠詳細</h2>

    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf
        @method('PUT')

        <table class="attendance-detail__table">
            <tr>
                <th>名前</th>
                <td colspan="2">{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td colspan="2">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td colspan="2">
                    <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}" class="time-input">
                    <span class="time-separator">〜</span>
                    <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}" class="time-input">
                </td>
            </tr>

            @foreach ($attendance->breaks as $index => $break)
            <tr>
                <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                <td colspan="2">
                    <input type="time" name="breaks[{{ $index }}][start]" value="{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}" class="time-input">
                    <span class="time-separator">〜</span>
                    <input type="time" name="breaks[{{ $index }}][end]" value="{{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}" class="time-input">
                </td>
            </tr>
            @endforeach

            <tr>
                <th>備考</th>
                <td colspan="2">
                    <textarea name="note" class="note-textarea">{{ old('note', $attendance->note) }}</textarea>
                </td>
            </tr>
        </table>

        <div class="btn-wrapper">
            <button type="submit" class="btn-edit">修正</button>
        </div>
    </form>
    <script>
        document.getElementById('edit-form').addEventListener('keydown', function (e) {
          if (e.key === 'Enter') {
            e.preventDefault();
          }
        });
      </script>
</div>
@endsection
