@extends('layouts.app')

@section('title', 'Кабинет - SY Soft')

@section('content')
    <section class="dashboard-head">
        <div class="container">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <div class="eyebrow">User dashboard</div>
                    <h1>Кабинет пользователя</h1>
                    <p>Здравствуйте, {{ $user->name }}. Здесь ваши заявки и задачи, связанные с аккаунтом.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="btn btn-light me-lg-2" href="{{ route('dashboard.tickets.create') }}">
                        <i class="bi bi-plus-lg"></i>
                        Создать тикет
                    </a>
                    @if ($user->isAdmin())
                        <a class="btn btn-primary" href="{{ url('/admin') }}">
                            <i class="bi bi-shield-lock"></i>
                            Перейти в админ-панель
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="dashboard-metrics">
                <article>
                    <span>Мои заявки</span>
                    <strong>{{ $stats['created'] }}</strong>
                </article>
                <article>
                    <span>Назначено мне</span>
                    <strong>{{ $stats['assigned'] }}</strong>
                </article>
                <article>
                    <span>Активные</span>
                    <strong>{{ $stats['active'] }}</strong>
                </article>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-lg-6">
                    <section class="dashboard-panel">
                        <div class="dashboard-panel-head">
                            <h2>Созданные мной</h2>
                            @if ($hasMoreCreated)
                                <a href="{{ route('dashboard.tickets.index', ['scope' => 'created']) }}">Еще...</a>
                            @else
                                <i class="bi bi-send"></i>
                            @endif
                        </div>
                        <div class="ticket-list">
                            @forelse ($createdTickets as $ticket)
                                <a class="ticket-item" href="{{ route('dashboard.tickets.show', $ticket) }}">
                                    <div>
                                        <span>#{{ $ticket->id }}</span>
                                        <strong>{{ $ticket->title }}</strong>
                                        <small>{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                    <span class="badge text-bg-{{ $ticket->status?->color ?? 'secondary' }}">
                                        {{ $ticket->status?->label ?? 'Без статуса' }}
                                    </span>
                                </a>
                            @empty
                                <div class="empty-panel">Вы еще не создавали заявки.</div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div class="col-lg-6">
                    <section class="dashboard-panel">
                        <div class="dashboard-panel-head">
                            <h2>Назначено мне</h2>
                            @if ($hasMoreAssigned)
                                <a href="{{ route('dashboard.tickets.index', ['scope' => 'assigned']) }}">Еще...</a>
                            @else
                                <i class="bi bi-person-check"></i>
                            @endif
                        </div>
                        <div class="ticket-list">
                            @forelse ($assignedTickets as $ticket)
                                <a class="ticket-item" href="{{ route('dashboard.tickets.show', $ticket) }}">
                                    <div>
                                        <span>#{{ $ticket->id }}</span>
                                        <strong>{{ $ticket->title }}</strong>
                                        <small>{{ $ticket->createdBy?->name ?? 'Автор не указан' }}</small>
                                    </div>
                                    <span class="badge text-bg-{{ $ticket->status?->color ?? 'secondary' }}">
                                        {{ $ticket->status?->label ?? 'Без статуса' }}
                                    </span>
                                </a>
                            @empty
                                <div class="empty-panel">На вас пока нет назначенных заявок.</div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
