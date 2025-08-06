<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Font Awesome --}}
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header__logo">
                <a href="{{ route('items.index') }}">
                    <img src="{{ asset('img/logo.svg') }}" class="header__logo-image" alt="coachtech"/>
                </a>
            </div>

            @auth
            <div class="header__search">
                <form class="search-form" action="{{ route('items.index') }}" method="GET">
                    <input type="text" name="keyword" placeholder="なにをお探しですか" value="{{ request('keyword') }}" />
                        @if(request('tab'))
                            <input type="hidden" name="tab" value="{{ request('tab') }}" />
                        @endif
                        <button type="submit" class="search-form__button">検索</button>
                </form>
            </div>
            <div class="header__utilities">
                <ul class="header-nav">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="header-nav__link as-button">ログアウト</button>
                        </form>
                    </li>
                    <li>
                        <a class="header-nav__link" href="{{ route('users.show')}}">マイページ</a>
                    </li>
                    <li>
                        <a class="header-nav__button" href="{{ route('items.create') }}">出品</a>
                    </li>
                </ul>
            </div>
            @endauth
        </header>
        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>