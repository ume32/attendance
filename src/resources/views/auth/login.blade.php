@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h1 class="form-title">ログイン</h1>

    <form method="POST" action="/login">
        @csrf

        {{-- メールアドレス --}}
        <div class="form-group">
            <x-label for="email">メールアドレス</x-label>
            <x-input id="email" type="email" name="email" :value="old('email')" class="form-input" autofocus />
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <x-label for="password">パスワード</x-label>
            <x-input id="password" type="password" name="password" class="form-input" />
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- ログインボタン --}}
        <div class="form-button">
            <button type="submit" class="button">ログインする</button>
        </div>
    </form>

    <div class="login-link-wrapper">
        <a href="/register" class="login-link">会員登録はこちら</a>
    </div>
</div>
@endsection
