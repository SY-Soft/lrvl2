<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Электроника', 'Одежда', 'Книги', 'Дом и сад',
            'Спорт', 'Автотовары', 'Косметика'
        ]);

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
        ];
    }
}
