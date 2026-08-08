<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $sort = 'name';

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'sort' => ['except' => 'name'],
    ];
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->sort === 'price_asc', fn($q) => $q->orderBy('price'))
            ->when($this->sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
            ->when($this->sort === 'name', fn($q) => $q->orderBy('name'))
            ->paginate(12);

        $categories = Category::orderBy('name')->get();

        return view('livewire.products.index', [
            'products' => $products,
            'categories' => $categories,
            ])
            ->layout('layouts.app')
            ->title(__('Товары'));
    }
    public function resetFilters()
    {
        $this->search = '';
        $this->category_id = '';
        $this->sort = 'name';
        $this->resetPage();
    }
    public function updatedSearch() { $this->resetPage(); }
    public function updatedCategoryId() { $this->resetPage(); }
    public function updatedSort() { $this->resetPage(); }
}
