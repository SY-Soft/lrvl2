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
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Кабинет</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.tickets.*') ? 'active' : '' }}" href="{{ route('dashboard.tickets.index') }}">Тикеты</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm px-3" href="{{ route('dashboard.tickets.create') }}">
                                <i class="bi bi-plus-lg"></i>
                                Создать
                            </a>
                        </li>
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="btn btn-outline-dark btn-sm px-3" href="{{ url('/admin') }}">
                                    <i class="bi bi-shield-lock"></i>
                                    Админ
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="post" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="bi bi-box-arrow-right"></i>
                                            Выйти
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="#process">Процесс</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Возможности</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-dark btn-sm px-3" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
