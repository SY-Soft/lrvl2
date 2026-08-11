<?php

namespace App\Livewire\Modules\ProductPriceChange\Livewire;

use App\Models\Product;
use Livewire\Component;

class BulkCentsPanel extends Component
{
    public int $cents = 25;

    public function apply()
    {
        Product::query()
            ->select('id', 'price')
            ->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    $newPrice = floor($product->price) + ($this->cents / 100);

                    $product->update([
                        'price' => $newPrice,
                    ]);
                }
            });

        $this->dispatch('prices-updated');
    }

    public function render()
    {
        return view('livewire.modules.product-price-change.livewire.bulk-cents-panel');
    }
}
