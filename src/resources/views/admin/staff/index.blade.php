@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/index.css') }}">
@endsection

@section('content')
<div class="staff-list__wrapper">
    <h2 class="page-title">スタッフ一覧</h2>

    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><a href="{{ url('/admin/attendance/staff/' . $user->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
