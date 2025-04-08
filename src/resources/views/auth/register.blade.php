@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<h2 class="form-title">会員登録</h2>

<form method="POST" action="{{ route('register') }}" class="form">
  @csrf

  <label for="name" class="form-label">名前</label>
  <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
  @error('name')
    <p class="form-error">{{ $message }}</p>
  @enderror

  <label for="email" class="form-label">メールアドレス</label>
  <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
  @error('email')
    <p class="form-error">{{ $message }}</p>
  @enderror

  <label for="password" class="form-label">パスワード</label>
  <input type="password" name="password" id="password" class="form-input" required>
  @error('password')
    <p class="form-error">{{ $message }}</p>
  @enderror

  <label for="password_confirmation" class="form-label">パスワード確認</label>
  <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required>

  <button type="submit" class="btn-submit">登録する</button>

  <p class="link">
    <a href="{{ route('login') }}">ログインはこちら</a>
  </p>
</form>
@endsection
