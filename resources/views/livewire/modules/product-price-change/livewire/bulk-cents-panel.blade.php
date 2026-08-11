<div class="p-4 mb-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
    <div class="font-semibold mb-2">
        ProductPriceChange
    </div>

    <div class="flex items-center gap-2">
        <select {{-- wire:model.live="cents" --}} class="border rounded px-2 py-1">
            <option value="0">.00</option>
            <option value="25">.25</option>
            <option value="50">.50</option>
            <option value="75">.75</option>
        </select>

        <button
            wire:click="apply"
            wire:loading.attr="disabled"
            class="px-3 py-1 btn-primary"
        >
            <span wire:loading.remove>Применить ко всем</span>
            <span wire:loading>Обновляю...</span>
        </button>
    </div>

</div>
