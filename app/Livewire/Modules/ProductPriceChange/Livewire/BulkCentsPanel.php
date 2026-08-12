<?php

namespace App\Livewire\Modules\ProductPriceChange\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Jobs\ProductPriceChangeJob;
use App\Models\ProductPriceChangeOperation;
use Livewire\Attributes\Reactive;


class BulkCentsPanel extends Component
{
    public int $cents = 25;
    public ?ProductPriceChangeOperation $operation = null;

    #[Reactive]
    public string $sort = 'name';

    public function apply()
    {
        if ($this->isRunning()) {
            return;
        }


        $query = Product::query()->select('id');



        $query = Product::query()->select('id');

        match ($this->sort) {
            'name' => $query->orderBy('name'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderBy('name'),
        };

        $ids = $query->pluck('id');

        $this->operation = ProductPriceChangeOperation::create([
            'cents' => $this->cents,
            'total' => $ids->count(),
            'processed' => 0,
            'completed' => false,
        ]);

        foreach ($ids as $id) {
            ProductPriceChangeJob::dispatch(
                $id,
                $this->cents,
                $this->operation->id,
            );
        }
    }
    public function isRunning(): bool
    {
        return ProductPriceChangeOperation::where('completed', false)->exists();
    }

    public function getProgressProperty(): int
    {
        $operation = ProductPriceChangeOperation::latest()->first();

        if (! $operation || $operation->total === 0) {
            return 0;
        }

        return (int) round($operation->processed / $operation->total * 100);
    }

    public function render()
    {
        return view('livewire.modules.product-price-change.livewire.bulk-cents-panel');
    }
}
