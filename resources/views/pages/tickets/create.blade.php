@extends('layouts.app')

@section('title', 'Новый тикет - SY Soft')

@section('content')
    <section class="dashboard-head compact-head">
        <div class="container">
            <div class="eyebrow">New ticket</div>
            <h1>Создать тикет</h1>
            <p>Опишите задачу или проблему, чтобы команда могла взять ее в работу.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <form class="ticket-detail-panel" method="post" action="{{ route('dashboard.tickets.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="title">Тема</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Описание</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="7">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="priority">Приоритет</label>
                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                            @foreach (['low' => 'Низкий', 'medium' => 'Средний', 'high' => 'Высокий', 'urgent' => 'Срочный'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'medium') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="deadline">Желаемый срок</label>
                        <input class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" type="date" value="{{ old('deadline') }}">
                        @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-send"></i>
                            Создать
                        </button>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('dashboard.tickets.index') }}">Отмена</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
