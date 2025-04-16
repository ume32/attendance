@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/corrections/index.css') }}">
@endsection

@section('content')
<div class="correction-list__wrapper">
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-menu">
        <a href="{{ route('correction.list', ['status' => 'pending']) }}" class="{{ request('status') !== 'approved' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('correction.list', ['status' => 'approved']) }}" class="{{ request('status') === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="correction-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($corrections as $correction)
                <tr>
                    <td>{{ $correction->status }}</td>
                    <td>{{ $correction->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($correction->attendance->date)->format('Y/m/d') }}</td>
                    <td>{{ $correction->note ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($correction->created_at)->format('Y/m/d') }}</td>
                    <td><a href="{{ route('attendance.show', $correction->attendance->id) }}" class="detail-link">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
