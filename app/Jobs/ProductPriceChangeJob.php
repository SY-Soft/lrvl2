<?php

namespace App\Jobs;

use App\Models\ProductPriceChangeOperation;
use App\Models\ProductPriceChangeOperationItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProductPriceChangeJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $itemId,
        public int $cents,
    ) {}

    public function backoff(): array
    {
        return [2, 5, 10];
    }

    public function handle(): void
    {
        $item = ProductPriceChangeOperationItem::with('product')
            ->find($this->itemId);

        if (! $item) {
            return;
        }

        /*
         * Если Job уже был назначен на ошибку,
         * ошибка должна сохраняться и при retry.
         */
        if (! $item->force_fail) {
            $this->claimErrorRequest($item);
        }

        /*
         * Проверяем после claim.
         */
        if ($item->force_fail) {
            throw new \RuntimeException(
                'Demo queue failure for product #' . $item->product_id
            );
        }

        $product = $item->product;

        if (! $product) {
            $item->update([
                'status' => 'failed',
                'error' => 'Product not found',
            ]);

            $this->finishOperation($item);

            return;
        }

        /*
         * Искусственная задержка для демонстрации.
         */
        sleep(2);

        /*
         * Пользователь мог создать ошибку
         * пока Job находился в sleep().
         */
        $item->refresh();

        if ($item->force_fail) {
            throw new \RuntimeException(
                'Demo queue failure for product #' . $item->product_id
            );
        }

        $product->refresh();

        $whole = floor((float) $product->price);

        $newPrice = $whole + ($this->cents / 100);

        $product->update([
            'price' => $newPrice,
        ]);

        $item->update([
            'status' => 'processed',
            'error' => null,
        ]);

        $operation = $item->operation;

        if ($operation) {
            $operation->increment('processed');

            $operation->refresh();

            $this->checkOperationCompleted($operation);
        }

        Log::info('PRODUCT PRICE CHANGE JOB COMPLETED', [
            'item' => $item->id,
            'product' => $product->id,
            'operation' => $operation?->id,
        ]);
    }

    private function claimErrorRequest(
        ProductPriceChangeOperationItem $item
    ): void {
        $operationId = $item->operation_id;

        /*
         * Атомарно забираем одну заявку на ошибку.
         *
         * Если errors_to_create = 0,
         * UPDATE ничего не изменит.
         */
        $claimed = ProductPriceChangeOperation::query()
            ->whereKey($operationId)
            ->where('errors_to_create', '>', 0)
            ->decrement('errors_to_create');

        if ($claimed === 1) {
            $item->update([
                'force_fail' => true,
            ]);

            $item->refresh();
        }
    }

    public function failed(?\Throwable $e): void
    {
        $item = ProductPriceChangeOperationItem::with('product')
            ->find($this->itemId);

        if (! $item) {
            return;
        }

        $item->update([
            'status' => 'failed',
            'error' => $e?->getMessage() ?? 'Unknown queue error',
        ]);

        $operation = $item->operation;

        if (! $operation) {
            return;
        }

        $operation->increment('failed');

        $operation->refresh();

        $this->checkOperationCompleted($operation);

        Log::warning('PRODUCT PRICE CHANGE JOB FAILED', [
            'item' => $item->id,
            'product' => $item->product_id,
            'operation' => $operation->id,
            'error' => $e?->getMessage(),
        ]);
    }

    private function finishOperation(
        ProductPriceChangeOperationItem $item
    ): void {
        $operation = $item->operation;

        if (! $operation) {
            return;
        }

        $operation->increment('failed');

        $operation->refresh();

        $this->checkOperationCompleted($operation);
    }

    private function checkOperationCompleted(
        ProductPriceChangeOperation $operation
    ): void {
        if (($operation->processed + $operation->failed) >= $operation->total) {
            $operation->update([
                'completed' => true,
            ]);
        }
    }
}
