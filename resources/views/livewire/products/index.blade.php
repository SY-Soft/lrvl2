<div class="container py-5 position-relative">




    <h1 class="mb-4">Товары</h1>
    <div class="row mb-4 g-3">
        <div class="col-md-5">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   class="form-control"
                   placeholder="Поиск по названию или описанию...">
        </div>

        <div class="col-md-3">
            <select wire:model.live="category_id" class="form-select">
                <option value="">Все категории</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select wire:model.live="sort" class="form-select">
                <option value="name">По названию</option>
                <option value="price_asc">Цена: по возрастанию</option>
                <option value="price_desc">Цена: по убыванию</option>
            </select>
        </div>

        <div class="col-md-2">
            <button wire:click="resetFilters"
                    class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Сбросить
            </button>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($products as $product)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="text-muted small">{{ $product->category?->name ?? 'Без категории' }}</p>
                        <p class="card-text text-truncate">{{ \Illuminate\Support\Str::limit($product->description, 90) }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h4 class="text-primary mb-0">{{ number_format($product->price, 2) }} ₴</h4>
                            @if($product->stock > 0)
                                <span class="badge bg-success">В наличии: {{ $product->stock }}</span>
                            @else
                                <span class="badge bg-danger">Нет в наличии</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
