<x-filament-panels::page>
    {{ $this->form }}

    @if ($batchModalVisible)
        <div
            @if ($batchRunning)
                wire:poll.700ms="processBatch"
            @endif
            style="position: fixed; inset: 0; z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(3, 7, 18, .62);"
        >
            <div style="width: 100%; max-width: 560px; padding: 24px; color: #111827; background: #fff; border-radius: 12px; box-shadow: 0 24px 80px rgba(15, 23, 42, .35);">
                <div style="margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 18px; font-weight: 700;">
                        {{ $batchTitle }}
                    </h2>

                    @if ($batchConfirming)
                        <p style="margin: 8px 0 0; color: #4b5563; font-size: 14px;">
                            {{ $batchQuestion }}
                        </p>
                    @else
                        <p style="margin: 8px 0 0; color: #4b5563; font-size: 14px;">
                            {{ $batchStatus }}
                        </p>
                    @endif
                </div>

                @if ($batchRunning)
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px; font-size: 14px;">
                        <span style="color: #374151; font-weight: 600;">
                            Обработано: {{ $batchProcessed }} / {{ $batchTotal }}
                        </span>

                        <span style="font-weight: 700;">
                            {{ $batchProgress }}%
                        </span>
                    </div>

                    <div style="height: 12px; overflow: hidden; background: #e5e7eb; border-radius: 999px;">
                        <div
                            style="height: 100%; width: {{ $batchProgress }}%; background: #f59e0b; border-radius: 999px; transition: width .3s ease;"
                        ></div>
                    </div>
                @endif

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    @if ($batchConfirming)
                        <button
                            type="button"
                            wire:click.prevent="cancelBatch"
                            style="padding: 9px 14px; color: #374151; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 700;"
                        >
                            Отмена
                        </button>

                        <button
                            type="button"
                            wire:click.prevent="startBatch"
                            style="padding: 9px 14px; color: #111827; background: #f59e0b; border: 1px solid #d97706; border-radius: 8px; font-weight: 700;"
                        >
                            Запустить
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click.prevent="cancelBatch"
                            style="padding: 9px 14px; color: #fff; background: #ef4444; border: 1px solid #dc2626; border-radius: 8px; font-weight: 700;"
                        >
                            Отмена
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
