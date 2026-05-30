@extends('layouts.app')

@section('title', '#' . $ticket->id . ' - ' . $ticket->title)

@section('content')
    <section class="dashboard-head compact-head">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <div class="eyebrow">Ticket #{{ $ticket->id }}</div>
                    <h1>{{ $ticket->title }}</h1>
                    <p>Автор: {{ $ticket->createdBy?->name ?? 'не указан' }} · Исполнитель: {{ $ticket->assignedTo?->name ?? 'не назначен' }}</p>
                </div>
                <div class="align-self-lg-end">
                    <a class="btn btn-light" href="{{ route('dashboard.tickets.index') }}">
                        <i class="bi bi-arrow-left"></i>
                        К списку
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="ticket-detail-panel">
                        <div class="ticket-detail-head">
                            <h2>Описание</h2>
                            <span class="priority-badge priority-{{ $ticket->priority }}">{{ ['low' => 'Низкий', 'medium' => 'Средний', 'high' => 'Высокий', 'urgent' => 'Срочный'][$ticket->priority] ?? $ticket->priority }}</span>
                        </div>
                        <p>{{ $ticket->description ?: 'Описание не указано.' }}</p>
                    </article>

                    <section class="ticket-detail-panel mt-4">
                        <div class="ticket-detail-head">
                            <h2>Комментарии</h2>
                            <i class="bi bi-chat-dots"></i>
                        </div>

                        <form class="comment-form" method="post" action="{{ route('dashboard.tickets.comments.store', $ticket) }}">
                            @csrf
                            <label class="form-label" for="content">Новый комментарий</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="4" required>{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <button class="btn btn-primary mt-3" type="submit">
                                <i class="bi bi-send"></i>
                                Добавить
                            </button>
                        </form>

                        <div class="comments-list">
                            @forelse ($ticket->comments->sortByDesc('created_at') as $comment)
                                <article class="comment-item">
                                    <div class="comment-meta">
                                        <strong>{{ $comment->user?->name ?? 'Пользователь' }}</strong>
                                        <span>{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p>{{ $comment->content }}</p>
                                </article>
                            @empty
                                <div class="empty-panel">Комментариев пока нет.</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="ticket-detail-panel mt-4">
                        <div class="ticket-detail-head">
                            <h2>История</h2>
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="history-list">
                            @forelse ($ticket->histories->sortByDesc('created_at') as $history)
                                <article class="history-item">
                                    <div>
                                        <strong>{{ $history->user?->name ?? 'Система' }}</strong>
                                        <span>{{ $history->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p>
                                        {{ [
                                            'created' => 'создал тикет',
                                            'comment' => 'добавил комментарий',
                                            'status_id' => 'сменил статус',
                                            'updated' => 'изменил тикет',
                                        ][$history->field] ?? $history->field }}
                                        @if ($history->old_value || $history->new_value)
                                            <small>{{ $history->old_value ?? '-' }} → {{ $history->new_value ?? '-' }}</small>
                                        @endif
                                    </p>
                                </article>
                            @empty
                                <div class="empty-panel">История пока пустая.</div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div class="col-lg-4">
                    <aside class="ticket-detail-panel">
                        <h2>Статус</h2>
                        @if (auth()->user()->can('tickets.change-status') || auth()->user()->isGod())
                            <form method="post" action="{{ route('dashboard.tickets.status.update', $ticket) }}">
                                @csrf
                                @method('patch')
                                <select class="form-select @error('status_id') is-invalid @enderror" name="status_id" aria-label="Статус тикета">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" @selected($ticket->status_id === $status->id)>{{ $status->label }}</option>
                                    @endforeach
                                </select>
                                @error('status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <button class="btn btn-primary w-100 mt-3" type="submit">
                                    <i class="bi bi-check2-circle"></i>
                                    Обновить статус
                                </button>
                            </form>
                        @else
                            <div class="empty-panel">Статус меняет исполнитель или менеджер.</div>
                        @endif

                        <dl class="ticket-side-list">
                            <dt>Создано</dt>
                            <dd>{{ $ticket->created_at->format('d.m.Y H:i') }}</dd>
                            <dt>Срок</dt>
                            <dd>{{ $ticket->deadline?->format('d.m.Y') ?? 'не указан' }}</dd>
                            <dt>Текущий статус</dt>
                            <dd><span class="badge text-bg-{{ $ticket->status?->color ?? 'secondary' }}">{{ $ticket->status?->label ?? 'Без статуса' }}</span></dd>
                        </dl>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
