<?php

namespace App\Livewire\Modules\ProductPriceChange\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Jobs\ProductPriceChangeJob;
use App\Models\ProductPriceChangeOperation;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

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
        if (! $this->operation) {
            return false;
        }

        return ! $this->operation->fresh()->completed;
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

        return (int) round(
            $operation->processed / $operation->total * 100
        );
    }

    public function render()
    {
        return view(
            'livewire.modules.product-price-change.livewire.bulk-cents-panel'
        );
    }
    public function testFailure()
    {
        $product = Product::query()->first();

        if (! $product) {
            return;
        }

        $this->operation = ProductPriceChangeOperation::create([
            'cents' => 1,
            'total' => 1,
            'processed' => 0,
            'failed' => 0,
            'completed' => false,
        ]);

        ProductPriceChangeJob::dispatch(
            $product->id,
            1,
            $this->operation->id,
            true, // force fail
        );
    }
    public function retryFailed(): void
    {
        $failedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', '%"operationId":' . $this->operation?->id . '%')
            ->pluck('uuid');

        foreach ($failedJobs as $uuid) {
            Artisan::call('queue:retry', ['id' => $uuid]);
        }
    }
    public function getRecentOperationsProperty()
    {
        return ProductPriceChangeOperation::latest()
            ->limit(10)
            ->get();
    }
    public function getFailedJobsCountProperty(): int
    {
        return DB::table('failed_jobs')->count();
    }
}
