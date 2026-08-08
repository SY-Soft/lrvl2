<div class="container py-5 position-relative">

    <div
        wire:loading
        class="position-absolute top-0 start-0 w-100 h-100"
        style="background: rgba(255,255,255,.8); z-index:999;"
    >
        <div style="height:100vh;display:flex;align-items:center;justify-content:center;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2">Загрузка...</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Мои заявки</h1>

        <a href="{{ route('dashboard.tickets.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Создать заявку
        </a>
    </div>

    {{-- Фильтры --}}
    <div class="row mb-4 g-3">

        <div class="col-md-5">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="Поиск по заголовку или описанию..."
            >
        </div>

        <div class="col-md-2">
            <select wire:model.live="status_id" class="form-select">
                <option value="">Все статусы</option>

                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">
                        {{ $status->label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select wire:model.live="priority" class="form-select">
                <option value="">Все приоритеты</option>
                <option value="low">Низкий</option>
                <option value="medium">Средний</option>
                <option value="high">Высокий</option>
                <option value="urgent">Срочный</option>
            </select>
        </div>

        <div class="col-md-2">
            <select wire:model.live="field" class="form-select">
                <option value="created_at">По дате создания</option>
                <option value="updated_at">По дате изменения</option>
                <option value="deadline">По дедлайну</option>
                <option value="title">По заголовку</option>
                <option value="priority">По приоритету</option>
            </select>
        </div>

        <div class="col-md-1">
            <button
                wire:click="resetFilters"
                class="btn btn-outline-secondary w-100"
                title="Сбросить фильтры"
            >
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
        </div>

    </div>

    {{-- Направление сортировки --}}
    <div class="mb-3">

        <button
            wire:click="$set('direction', 'desc')"
            class="btn btn-sm {{ $direction === 'desc' ? 'btn-primary' : 'btn-outline-primary' }}"
        >
            Сначала новые
        </button>

        <button
            wire:click="$set('direction', 'asc')"
            class="btn btn-sm {{ $direction === 'asc' ? 'btn-primary' : 'btn-outline-primary' }}"
        >
            Сначала старые
        </button>

    </div>

    {{-- Заявки --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        @forelse($tickets as $ticket)

            <div class="col">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-2">

                            <span class="text-muted small">
                                #{{ $ticket->id }}
                            </span>

                            @if($ticket->status)
                                <span class="badge bg-{{ $ticket->status->color ?? 'secondary' }}">
                                    {{ $ticket->status->label }}
                                </span>
                            @endif

                        </div>

                        <h5 class="card-title">
                            {{ $ticket->title }}
                        </h5>

                        @if($ticket->description)
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit($ticket->description, 140) }}
                            </p>
                        @endif

                        <div class="mb-2">

                            @php
                                $priorityLabels = [
                                    'low' => 'Низкий',
                                    'medium' => 'Средний',
                                    'high' => 'Высокий',
                                    'urgent' => 'Срочный',
                                ];
                            @endphp

                            <span class="badge bg-secondary">
                                {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                            </span>

                        </div>

                        @if($ticket->assignedTo)
                            <div class="small text-muted">
                                Исполнитель:
                                {{ $ticket->assignedTo->name }}
                            </div>
                        @endif

                        @if($ticket->deadline)
                            <div class="small text-muted">
                                Дедлайн:
                                {{ $ticket->deadline->format('d.m.Y H:i') }}
                            </div>
                        @endif

                        <div class="small text-muted mt-2">
                            Создано:
                            {{ $ticket->created_at->format('d.m.Y H:i') }}
                        </div>

                    </div>

                    <div class="card-footer bg-transparent">

                        <a
                            href="{{ route('dashboard.tickets.show', $ticket) }}"
                            class="btn btn-outline-primary btn-sm"
                        >
                            Открыть
                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-info">
                    Заявок не найдено.
                </div>

            </div>

        @endforelse

    </div>

    {{-- Пагинация --}}
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>

</div>
