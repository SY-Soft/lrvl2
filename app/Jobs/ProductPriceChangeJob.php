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
        public int $operationId,
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
        $product = Product::find($this->productId);

        if (! $product) {
            return;
        }

        sleep(2);

        $whole = (int) $product->price;

        $newPrice = $whole + ($this->cents / 100);

        $product->update([
            'price' => $newPrice,
        ]);

        $operation = ProductPriceChangeOperation::find($this->operationId);

        if ($operation) {
            $operation->increment('processed');

            $operation->refresh();

            if ($operation->processed >= $operation->total) {
                $operation->update([
                    'completed' => true,
                ]);
            }
        }
    }

}
