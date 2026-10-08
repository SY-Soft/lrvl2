<div class="card shadow-sm mb-4 lw-no-global-loader" wire:poll.1s>
    <div class="card-body">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="fw-semibold text-primary">
                Demo Redis Queue + Job + Progress bar
            </div>

            @if($this->isRunning())
                <span class="badge bg-warning text-dark">
                    Выполняется
                </span>
            @else
                <span class="badge bg-success">
                    Готово
                </span>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            <div>Цена</div>

            <select
                wire:model="cents"
                class="form-select form-select-sm w-auto"
                @disabled($this->isRunning())
            >
                <option value="0">.00</option>
                <option value="25">.25</option>
                <option value="50">.50</option>
                <option value="75">.75</option>
            </select>

            <div>коп.</div>

            <button
                wire:click="apply"
                wire:loading.attr="disabled"
                class="btn btn-primary btn-sm"
                @disabled($this->isRunning())
            >
                @if($this->isRunning())
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Выполняется...
                @else
                    Применить ко всем
                @endif
            </button>

            @if($this->isRunning())
                <button
                    wire:click="createError"
                    wire:loading.attr="disabled"
                    class="btn btn-danger btn-sm"
                >
                    Создать ошибку
                </button>
            @endif

        </div>

        <div class="mt-2 text-muted small">
            Выбранное значение (.00, .25, .50 или .75) будет установлено
            для всех товаров. Обработка выполняется в очереди в фоновом
            режиме, а прогресс и результат отображаются на этой странице
            в реальном времени.
        </div>

        @if($this->isRunning())
            <div class="mt-3">

                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>
                        Обработано:
                        {{ ($this->operation?->processed ?? 0) + ($this->operation?->failed ?? 0) }}
                        из
                        {{ $this->operation?->total ?? 0 }}
                    </span>

                    <span>
                        {{ $this->progress }}%
                    </span>
                </div>

                <div class="progress" style="height: 8px;">
                    <div
                        class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar"
                        style="width: {{ $this->progress }}%"
                    ></div>
                </div>

            </div>
        @endif

        @if($this->operation && ! $this->isRunning())
            @php
                $operation = $this->operation->fresh();
            @endphp

            <div class="mt-4 pt-3 border-top">

                <div class="fw-semibold mb-2">
                    Обновление завершено
                </div>

                <div>
                    Всего:
                    <strong>{{ $operation?->total ?? 0 }}</strong>
                </div>

                <div class="text-success">
                    Успешно:
                    <strong>{{ $operation?->processed ?? 0 }}</strong>
                </div>

                <div class="{{ ($operation?->failed ?? 0) > 0 ? 'text-danger' : 'text-muted' }}">
                    С ошибкой:
                    <strong>{{ $operation?->failed ?? 0 }}</strong>
                </div>

                @if($this->failedItems->isNotEmpty())
                    <div class="mt-3">

                        <div class="fw-semibold text-danger mb-2">
                            Товары с ошибкой:
                        </div>

                        <ul class="mb-0">
                            @foreach($this->failedItems as $item)
                                <li>
                                    #{{ $item->product_id }}
                                    — {{ $item->product?->name ?? 'Товар удалён' }}

                                    @if($item->error)
                                        <div class="small text-muted">
                                            {{ $item->error }}
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                    </div>
                @endif

            </div>
        @endif

    </div>
</div>
