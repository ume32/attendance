<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH 勤怠管理 - 管理者</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <a href="/admin/attendance/list" class="header__logo">
                    <img src="{{ asset('images/logo.png') }}" alt="COACHTECH">
                </a>
            </div>
            @auth('admin')
            <nav class="header__nav">
                <ul>
                    <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ url('/admin/staff/list') }}">スタッフ一覧</a></li>
                    <li><a href="{{ url('/admin/stamp_correction_request/list') }}">申請一覧</a></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
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
