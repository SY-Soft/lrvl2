@extends('layouts.app')

@section('title', 'Вход - SY Soft')

@section('content')
    <section class="auth-shell">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="eyebrow">Secure desk</div>
                    <h1>Вход в портал заявок</h1>
                    <p class="auth-copy">Один вход для портала и админки: сначала email и пароль, затем можно подключить Google account и второй фактор через Google Authenticator.</p>
                    <div class="auth-points">
                        <div>
                            <i class="bi bi-person-check"></i>
                            <span>Логин/пароль через Laravel session</span>
                        </div>
                        <div>
                            <i class="bi bi-google"></i>
                            <span>Место под Google OAuth</span>
                        </div>
                        <div>
                            <i class="bi bi-shield-lock"></i>
                            <span>Место под GA/TOTP-код</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 offset-lg-1">
                    <div class="auth-panel">
                        <div class="auth-panel-head">
                            <div>
                                <span class="eyebrow">Account</span>
                                <h2>Войти</h2>
                            </div>
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <form method="post" action="{{ route('login.store') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input
                                    id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    autofocus
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Пароль</label>
                                <input
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="otp_code">Google Authenticator</label>
                                <input
                                    id="otp_code"
                                    class="form-control"
                                    name="otp_code"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    placeholder="6 цифр, когда включим 2FA"
                                    disabled
                                >
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Запомнить</label>
                                </div>
                                <span class="text-secondary small">admin@test.com / admin</span>
                            </div>

                            <button class="btn btn-primary btn-lg w-100" type="submit">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти
                            </button>
                        </form>

                        <div class="auth-divider"><span>или</span></div>

                        <a class="btn btn-outline-dark btn-lg w-100" href="{{ route('login.google') }}">
                            <i class="bi bi-google"></i>
                            Войти через Google
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
