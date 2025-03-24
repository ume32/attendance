<header class="header">
    <div class="container">
        <div class="header__inner">
            <div class="header__logo">
                <img src="{{ asset('images/logo.png') }}" alt="COACHTECH ロゴ">
            </div>

            @auth
            <nav class="header__nav">
                <ul class="nav__list">
                    <li class="nav__item"><a href="/attendance">勤怠</a></li>
                    <li class="nav__item"><a href="/attendance/list">勤怠一覧</a></li>
                    <li class="nav__item"><a href="/stamp_correction_request/list">申請</a></li>
                    <li class="nav__item"><a href="/logout">ログアウト</a></li>
                </ul>
            </nav>
            @endauth
        </div>
    </div>
</header>
