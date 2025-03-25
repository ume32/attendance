@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
    <div class="verify-container">
        <div class="verify-message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </div>

        <div class="verify-actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="verify-resend">認証メールを再送する</button>
            </form>

            <a href="{{ route('verification.notice') }}" class="verify-button">認証はこちらから</a>
        </div>
    </div>
@endsection
