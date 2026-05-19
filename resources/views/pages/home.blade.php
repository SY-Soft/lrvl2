@extends('layouts.app')

@section('title', 'SY Soft - сервис заявок')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="eyebrow">Service Desk</div>
                    <h1>Портал заявок для команды, клиентов и аккуратного контроля работ.</h1>
                    <p class="hero-copy">Чистый публичный интерфейс на Bootstrap, админка на Filament, Laravel-логика остается на месте и готова развиваться без хаоса в шаблонах.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary btn-lg" href="{{ url('/admin') }}">
                            <i class="bi bi-kanban"></i>
                            Открыть админку
                        </a>
                        <a class="btn btn-light btn-lg" href="#process">
                            <i class="bi bi-arrow-down-circle"></i>
                            Как работает
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
                            <span>Сущности</span>
                            <strong>Tickets / Statuses</strong>
                        </div>
                        <div class="summary-row">
                            <span>Деплой</span>
                            <strong>Vite build</strong>
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
                <h2>Каркас без лишнего шума</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="bi bi-layout-text-window"></i></span>
                        <h3>Header и footer</h3>
                        <p>Общий Blade layout, частичные шаблоны и понятная навигация вместо огромного welcome-файла.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="bi bi-bootstrap"></i></span>
                        <h3>Bootstrap через Vite</h3>
                        <p>Bootstrap и иконки установлены npm-пакетами, собираются вместе с фронтом и не зависят от CDN.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="bi bi-shield-check"></i></span>
                        <h3>Готово к развитию</h3>
                        <p>Можно добавлять публичные формы заявок, страницы статусов и клиентский кабинет поверх существующих моделей.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-0" id="process">
        <div class="container">
            <div class="section-title">
                <div class="eyebrow">Процесс</div>
                <h2>Как дальше наращивать</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">1</span>
                        <h3>Заявка</h3>
                        <p>Публичная форма создает Ticket и привязывает автора.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">2</span>
                        <h3>Обработка</h3>
                        <p>Команда меняет статус, приоритет и исполнителя в Filament.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="step-card">
                        <span class="step-number">3</span>
                        <h3>Контроль</h3>
                        <p>Клиент видит состояние работы, а история остается в системе.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
