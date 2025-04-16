@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/show.css') }}">
@endsection

@section('content')
<div class="attendance-detail__wrapper">
    <h2 class="page-title">勤怠詳細</h2>

    <form id="edit-form" method="POST" action="{{ route('attendance.note.update', $attendance->id) }}">
        @csrf
        @method('PUT')

        @php
            $updated = session('updated_data');
            $isPending = optional($attendance->correctionRequests->last())->status === '承認待ち';
        @endphp

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
                    <input type="time" name="start_time"
                        value="{{ $updated['start_time'] ?? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}"
                        class="time-input" @if($isPending) disabled @endif>
                    <span class="time-separator">〜</span>
                    <input type="time" name="end_time"
                        value="{{ $updated['end_time'] ?? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}"
                        class="time-input" @if($isPending) disabled @endif>
                </td>
            </tr>

            @foreach ($attendance->breaks as $index => $break)
                @php
                    $breakUpdated = $updated['breaks'][$index] ?? null;
                @endphp
                <tr>
                    <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                    <td colspan="2">
                        <input type="time" name="breaks[{{ $index }}][start]"
                            value="{{ $breakUpdated['start'] ?? \Carbon\Carbon::parse($break->break_start)->format('H:i') }}"
                            class="time-input" @if($isPending) disabled @endif>
                        <span class="time-separator">〜</span>
                        <input type="time" name="breaks[{{ $index }}][end]"
                            value="{{ $breakUpdated['end'] ?? \Carbon\Carbon::parse($break->break_end)->format('H:i') }}"
                            class="time-input" @if($isPending) disabled @endif>
                    </td>
                </tr>
            @endforeach

            <tr>
                <th>備考</th>
                <td colspan="2">
                    <textarea name="note" class="note-textarea" @if($isPending) disabled @endif>{{ $updated['note'] ?? $attendance->note }}</textarea>
                </td>
            </tr>
        </table>

        @if(optional($attendance->correctionRequests->last())->status === '承認済み')
            <p class="approved-label">※ この申請は承認済みです。</p>
        @endif

        <div class="btn-wrapper">
            @if(!$isPending)
                <button type="submit" class="btn-edit">修正</button>
            @else
                <p class="wait-message">※ 承認待ちのため修正はできません。</p>
            @endif
        </div>

        @if(session('message'))
            <p class="success-message">{{ session('message') }}</p>
        @endif
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
