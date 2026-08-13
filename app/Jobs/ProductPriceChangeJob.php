<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductPriceChangeOperation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProductPriceChangeJob implements ShouldQueue
{
    use Queueable;


    public function __construct(
        public int $productId,
        public int $cents,
        public string $operationId,
        public bool $forceFail = false,
        public bool $isRetry = false,
    ) {}

    public function handle(): void
    {
/*
        Log::info('PRODUCT JOB START', [
            'product' => $this->productId,
            'operation' => $this->operationId,
            'pid' => getmypid(),
            'time' => now()->toDateTimeString(),
        ]);
*/
        if ($this->forceFail) {
            throw new \RuntimeException('Demo queue failure');
        }

        $product = Product::find($this->productId);

        if (! $product) {
            return;
        }

        sleep(2);

        $whole = floor((float) $product->price);

        $newPrice = $whole + ($this->cents / 100);

        $product->update([
            'price' => $newPrice,
        ]);

        $operation = ProductPriceChangeOperation::where('uuid', $this->operationUuid)->first();

        if ($operation) {
            $operation->increment('processed');

            $operation->refresh();
            if (($operation->processed + $operation->failed) >= $operation->total) {
                $operation->update(['completed' => true]);
            }
        }
    }


    public function failed(?\Throwable $e): void
    {
        $operation = ProductPriceChangeOperation::find($this->operationId);

        if (! $operation) {
            return;
        }
        $operation->increment('failed');

        $operation->refresh();

        if (($operation->processed + $operation->failed) >= $operation->total) {
            $operation->update(['completed' => true]);
        }
    }
}
