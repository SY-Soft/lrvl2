<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'status_id' => Status::query()->value('id'),
            'priority' => fake()->randomElement([
                'low',
                'medium',
                'high',
                'urgent',
            ]),
            'created_by' => User::factory(),
            'assigned_to' => null,
            'deadline' => null,
        ];
    }
}
