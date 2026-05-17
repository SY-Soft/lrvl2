<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class DevelController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'tickets' => Ticket::count(),
            'statuses' => Status::count(),
        ];

        return view('devel.index', compact('stats'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'users_count' => 'required|integer|min:1|max:50',
        ]);

        // Создаём пользователей
        $usersCount = $request->users_count;

        for ($i = 1; $i <= $usersCount; $i++) {
            User::create([
                'name' => "Тестовый Юзер $i",
                'email' => "test{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        // Можно добавить создание тикетов и т.д.
        $this->generateTickets(20);

        return redirect()->route('devel.index')
            ->with('success', "Создано {$usersCount} пользователей + тестовые тикеты");
    }

    private function generateTickets(int $count)
    {
        $users = User::pluck('id');
        $statuses = Status::pluck('id');

        for ($i = 1; $i <= $count; $i++) {
            Ticket::create([
                'title' => "Тестовая заявка #$i",
                'description' => "Описание тестовой заявки номер $i. Сгенерировано автоматически.",
                'status_id' => $statuses->random(),
                'priority' => ['low', 'medium', 'high', 'urgent'][rand(0, 3)],
                'assigned_to' => $users->random(),
                'deadline' => now()->addDays(rand(1, 30)),
            ]);
        }
    }
}
