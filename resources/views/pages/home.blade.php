@extends('layouts.app')

@section('title', 'SY Soft - сервис заявок')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="eyebrow">Service Desk</div>
                    <h1>Портал заявок для клиентов и команды поддержки.</h1>
                    <p class="hero-copy">Публичная часть собирает обращения. Администрирование, статусы и работа с заявками остаются в Filament.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary btn-lg" href="{{ url('/admin') }}">
                            <i class="bi bi-kanban"></i>
                            Открыть админку
                        </a>
                        <a class="btn btn-light btn-lg" href="#process">
                            <i class="bi bi-arrow-down-circle"></i>
                            Как устроено
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="summary-panel">
                        <div class="summary-row">
                            <span>Фронт</span>
                            <strong>Bootstrap 5</strong>
                        </div>
                        <div class="summary-row">
                            <span>Админка</span>
                            <strong>Filament</strong>
                        </div>
                        <div class="summary-row">
                            <span>Модели</span>
                            <strong>Tickets / Statuses</strong>
                        </div>
                        <div class="summary-row">
                            <span>Сборка</span>
                            <strong>Vite</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <div class="container">
            <div class="section-title">
                <div class="eyebrow">Основа</div>
                <h2>Структура фронта</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="bi bi-layout-text-window"></i></span>
                        <h3>Общий layout</h3>
                        <p>Базовый Blade-шаблон подключает стили, скрипты, header и footer.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="bi bi-bootstrap"></i></span>
                        <h3>Bootstrap через npm</h3>
                        <p>Bootstrap, Popper и иконки установлены пакетами и собираются через Vite.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="bi bi-shield-lock"></i></span>
                        <h3>Админ-раздел</h3>
                        <p>Ссылка на Filament остается в навигации, публичный интерфейс отделен от панели управления.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0" id="process">
        <div class="container">
            <div class="section-title">
                <div class="eyebrow">Процесс</div>
                <h2>Базовая логика</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">1</span>
                        <h3>Заявка</h3>
                        <p>Публичная форма будет создавать Ticket и сохранять контакт автора.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">2</span>
                        <h3>Обработка</h3>
                        <p>Оператор меняет статус, приоритет и исполнителя в Filament.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">3</span>
                        <h3>Контроль</h3>
                        <p>Статус заявки можно вывести в публичной части или в отдельном кабинете.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
