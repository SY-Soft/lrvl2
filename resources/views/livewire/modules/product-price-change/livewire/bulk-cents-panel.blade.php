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

            <select wire:model="cents" class="form-select form-select-sm w-auto">
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

<div>Выбранное значение (.00, .25, .50 или .75) будет установлено для всех товаров. Обработка выполняется в очереди в фоновом режиме, а прогресс и обновление цен отображаются на этой странице в реальном времени.</div>
        </div>

        @if($this->isRunning())
            <div class="mt-3">

                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Обработано</span>
                    <span>{{ $this->progress }}%</span>
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


        <div class="accordion accordion-flush" id="accordionTestFailure">
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        Обработка ошибок
                    </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse " aria-labelledby="flush-headingOne" data-bs-parent="#accordionTestFailure">
                    <div class="accordion-body">
                        <div>

                            <button
                                wire:click="testFailure"
                                class="btn btn-danger btn-sm"
                                @disabled($this->isRunning())
                            >
                                @if($this->isRunning())
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Выполняется...
                                @else
                                    Тест ошибки
                                @endif
                            </button>

                            <button
                                wire:click="retryFailed"
                                class="btn btn-warning btn-sm"
                                @disabled($this->isRunning())
                            >
                                @if($this->isRunning())
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Выполняется...
                                @else
                                    Retry failed
                                @endif
                            </button>

                            Failed: {{ $operation?->failed ?? 0 }}<br>
                            Failed jobs: {{ $this->failedJobsCount }}
                        </div>

                        <div class="mt-3 border-top pt-3">
                            <div class="fw-semibold mb-2">Последние операции</div>

                            @foreach($this->recentOperations as $op)
                                <div class="d-flex justify-content-between small py-1 border-bottom">
                                    <span>#{{ $op->id }}</span>

                                    <span>
                                        {{ $op->processed }}/{{ $op->total }}
                                    </span>

                                    <span class="{{ $op->completed ? 'text-success' : 'text-warning' }}">
                                        {{ $op->completed ? 'DONE' : 'RUNNING' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
