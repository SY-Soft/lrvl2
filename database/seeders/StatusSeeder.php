<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [

            [
                'id' => 1,
                'name' => 'new',
                'label' => 'Новая',
                'color' => 'gray',
                'order' => 1,
            ],

            [
                'id' => 2,
                'name' => 'in_progress',
                'label' => 'В работе',
                'color' => 'warning',
                'order' => 2,
            ],

            [
                'id' => 3,
                'name' => 'testing',
                'label' => 'Тестирование',
                'color' => 'info',
                'order' => 3,
            ],

            [
                'id' => 4,
                'name' => 'done',
                'label' => 'Выполнено',
                'color' => 'success',
                'order' => 4,
                'is_final' => true,
            ],

            [
                'id' => 5,
                'name' => 'cancelled',
                'label' => 'Отменено',
                'color' => 'danger',
                'order' => 5,
                'is_final' => true,
            ],
        ];

        foreach ($statuses as $status) {

            Status::updateOrCreate(
                ['id' => $status['id']],
                $status
            );
        }
    }
}
