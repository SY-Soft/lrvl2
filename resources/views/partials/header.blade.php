<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-mark">SY</span>
                <span class="brand-text">SY Soft</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Открыть меню">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#process">Процесс</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Возможности</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-dark btn-sm px-3" href="{{ url('/admin') }}">
                            <i class="bi bi-shield-lock"></i>
                            Админ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
