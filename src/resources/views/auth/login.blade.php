@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<h2 class="form-title">ログイン</h2>

<form method="POST" action="{{ route('login.post') }}" class="form">
  @csrf

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

  <button type="submit" class="btn-submit">ログインする</button>

  <p class="link">
    <a href="{{ route('register') }}">会員登録はこちら</a>
  </p>
</form>
@endsection
