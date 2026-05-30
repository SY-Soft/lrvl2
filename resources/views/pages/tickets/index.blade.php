@extends('layouts.app')

@section('title', 'Мои тикеты - SY Soft')

@section('content')
    <section class="dashboard-head compact-head">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <div class="eyebrow">Tickets</div>
                    <h1>Мои тикеты</h1>
                    <p>Заявки, которые вы создали или которые назначены на вас.</p>
                </div>
                <div class="align-self-lg-end">
                    <a class="btn btn-light" href="{{ route('dashboard.tickets.create') }}">
                        <i class="bi bi-plus-lg"></i>
                        Создать тикет
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <form class="ticket-filters" method="get">
                <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Поиск по теме или описанию">

                <select class="form-select" name="scope" aria-label="Тип заявок">
                    <option value="">Все связанные</option>
                    <option value="created" @selected(request('scope') === 'created')>Созданные мной</option>
                    <option value="assigned" @selected(request('scope') === 'assigned')>Назначенные мне</option>
                </select>

                <select class="form-select" name="status" aria-label="Статус">
                    <option value="">Все статусы</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) request('status') === (string) $status->id)>{{ $status->label }}</option>
                    @endforeach
                </select>

                <select class="form-select" name="priority" aria-label="Приоритет">
                    <option value="">Любой приоритет</option>
                    @foreach (['low' => 'Низкий', 'medium' => 'Средний', 'high' => 'Высокий', 'urgent' => 'Срочный'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select class="form-select" name="sort" aria-label="Сортировка">
                    <option value="">Новые сверху</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Старые сверху</option>
                    <option value="deadline" @selected(request('sort') === 'deadline')>По сроку</option>
                    <option value="priority" @selected(request('sort') === 'priority')>По приоритету</option>
                </select>

                <button class="btn btn-dark" type="submit">
                    <i class="bi bi-funnel"></i>
                    Фильтр
                </button>
                <a class="btn btn-outline-secondary" href="{{ route('dashboard.tickets.index') }}">
                    <i class="bi bi-x-lg"></i>
                </a>
            </form>

            <div class="dashboard-panel">
                <div class="ticket-list">
                    @forelse ($tickets as $ticket)
                        <a class="ticket-item" href="{{ route('dashboard.tickets.show', $ticket) }}">
                            <div>
                                <span>#{{ $ticket->id }} · {{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                                <strong>{{ $ticket->title }}</strong>
                                <small>
                                    Автор: {{ $ticket->createdBy?->name ?? 'не указан' }}
                                    · Исполнитель: {{ $ticket->assignedTo?->name ?? 'не назначен' }}
                                </small>
                            </div>
                            <div class="ticket-badges">
                                <span class="badge text-bg-{{ $ticket->status?->color ?? 'secondary' }}">{{ $ticket->status?->label ?? 'Без статуса' }}</span>
                                <span class="priority-badge priority-{{ $ticket->priority }}">{{ ['low' => 'Низкий', 'medium' => 'Средний', 'high' => 'Высокий', 'urgent' => 'Срочный'][$ticket->priority] ?? $ticket->priority }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="empty-panel">По этим условиям тикетов нет.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
        </div>
    </section>
@endsection
