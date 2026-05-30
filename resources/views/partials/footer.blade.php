<footer class="site-footer">
    <div class="container d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <strong>SY Soft</strong>
            <span class="ms-lg-2">Laravel + Filament + Bootstrap</span>
        </div>
        <div class="footer-links">
            <a href="{{ route('home') }}">Главная</a>
            @auth
                <a href="{{ route('dashboard') }}">Кабинет</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}">Админ-панель</a>
                @endif
            @else
                <a href="#process">Процесс</a>
                <a href="{{ route('login') }}">Войти</a>
            @endauth
        </div>
    </div>
</footer>
