<?php

namespace App\Livewire\Modules\ProductPriceChange\Livewire;

use App\Jobs\ProductPriceChangeJob;
use App\Models\Product;
use App\Models\ProductPriceChangeOperation;
use App\Models\ProductPriceChangeOperationItem;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class BulkCentsPanel extends Component
{
    public int $cents = 25;

    public ?ProductPriceChangeOperation $operation = null;

    #[Reactive]
    public string $sort = 'name';

    public function apply(): void
    {
        if ($this->isRunning()) {
            return;
        }

        $query = Product::query();

        match ($this->sort) {
            'name' => $query->orderBy('name'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderBy('name'),
        };

        $products = $query->get();

        $this->operation = ProductPriceChangeOperation::create([
            'cents' => $this->cents,
            'total' => $products->count(),
            'processed' => 0,
            'failed' => 0,
            'errors_to_create' => 0,
            'completed' => false,
        ]);

        foreach ($products as $product) {
            $item = ProductPriceChangeOperationItem::create([
                'operation_id' => $this->operation->id,
                'product_id' => $product->id,
                'status' => 'pending',
                'force_fail' => false,
            ]);

            ProductPriceChangeJob::dispatch(
                $item->id,
                $this->cents,
            );
        }
    }

    public function createError(): void
    {
        if (! $this->operation || ! $this->isRunning()) {
            return;
        }

        /*
         * Каждое нажатие создаёт одну заявку
         * на будущую ошибку.
         */
        $this->operation->increment('errors_to_create');

        $this->operation->refresh();
    }

    public function isRunning(): bool
    {
        if (! $this->operation) {
            return ProductPriceChangeOperation::where(
                'completed',
                false
            )->exists();
        }

        return ! $this->operation->fresh()?->completed;
    }

    public function getProgressProperty(): int
    {
        if (! $this->operation) {
            return 0;
        }

        $operation = $this->operation->fresh();

        if (! $operation || $operation->total === 0) {
            return 0;
        }

        $done = $operation->processed + $operation->failed;

        return (int) round(
            $done / $operation->total * 100
        );
    }

    public function getFailedItemsProperty()
    {
        if (! $this->operation) {
            return collect();
        }

        return ProductPriceChangeOperationItem::query()
            ->with('product:id,name')
            ->where('operation_id', $this->operation->id)
            ->where('status', 'failed')
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        return view(
            'livewire.modules.product-price-change.livewire.bulk-cents-panel'
        );
    }
}
