@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h1 class="form-title">会員登録</h1>

    <form method="POST" action="/register">
        @csrf

        {{-- 名前 --}}
        <div class="form-group">
            <x-label for="name">名前</x-label>
            <x-input id="name" type="text" name="name" :value="old('name')" class="form-input" autofocus />
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- メールアドレス --}}
        <div class="form-group">
            <x-label for="name">メールアドレス</x-label>
            <x-input id="email" type="email" name="email" :value="old('email')" class="form-input" />
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <x-label for="name">パスワード</x-label>
            <x-input id="password" type="password" name="password" class="form-input" />
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード確認 --}}
        <div class="form-group">
            <x-label for="name">パスワード確認</x-label>
            <x-input id="password_confirmation" type="password" name="password_confirmation" class="form-input" />
            @error('password_confirmation')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-button">
            <button type="submit" class="button">登録する</button>
        </div>
    </form>

    <div class="login-link-wrapper">
        <a href="/login" class="login-link">ログインはこちら</a>
    </div>
</div>
@endsection
