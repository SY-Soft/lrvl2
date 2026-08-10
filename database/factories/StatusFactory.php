<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'name' => 'test_status',
            'label' => 'Тестовый статус',
            'color' => 'secondary',
            'order' => 99,
            'is_final' => false,
        ];
    }
/*
    public function the_new(): static
    {
        return $this->state([
            'name' => 'new',
            'label' => 'Новая',
            'color' => 'secondary',
            'order' => 1,
            'is_final' => false,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state([
            'name' => 'in_progress',
            'label' => 'В работе',
            'color' => 'warning',
            'order' => 2,
            'is_final' => false,
        ]);
    }

    public function testing(): static
    {
        return $this->state([
            'name' => 'testing',
            'label' => 'Проверка',
            'color' => 'info',
            'order' => 3,
            'is_final' => false,
        ]);
    }

    public function done(): static
    {
        return $this->state([
            'name' => 'done',
            'label' => 'Выполнено',
            'color' => 'success',
            'order' => 4,
            'is_final' => true,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'name' => 'cancelled',
            'label' => 'Отменено',
            'color' => 'danger',
            'order' => 5,
            'is_final' => true,
        ]);
    }
    */
}
