<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <a href="/" class="header__logo">
                    <img src="{{ asset('images/logo.png') }}" alt="COACHTECH">
                </a>
        </div>
        @auth
        <nav class="header__nav">
            <ul>
                <li><a href="{{ route('attendance.index') }}">勤務</a></li>
                <li><a href="{{ url('/attendance/list') }}">勤怠一覧</a></li>
                <li><a href="{{ url('/stamp_correction_request/list') }}">申請</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
        @endauth
    </div>
</header>

<main class="main">
    @yield('content')
</main>
</body>
</html>
