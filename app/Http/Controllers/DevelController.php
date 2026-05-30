<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DevelController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'tickets' => Ticket::count(),
            'statuses' => Status::count(),
        ];

        return view('devel.index', compact('stats'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'users_count' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $usersCount = (int) $request->integer('users_count');
        $roles = Role::query()
            ->where('name', '!=', 'admin')
            ->orderBy('id')
            ->pluck('name')
            ->all();

        if (empty($roles)) {
            return redirect()
                ->route('devel.index')
                ->with('error', 'Сначала создайте роли Spatie Permission');
        }

        for ($i = 1; $i <= $usersCount; $i++) {
            $role = $roles[($i - 1) % count($roles)];

            User::updateOrCreate([
                'email' => "test{$i}@example.com",
            ], [
                'name' => "Test {$role} {$i}",
                'password' => bcrypt('password'),
            ])->syncRoles([$role]);
        }

        $this->generateTickets(20);

        return redirect()
            ->route('devel.index')
            ->with('success', "Создано {$usersCount} пользователей + тестовые тикеты");
    }

    private function generateTickets(int $count): void
    {
        $authors = User::query()->role('user')->pluck('id');
        $supportUsers = User::query()->role('support')->pluck('id');
        $statuses = Status::query()->pluck('id');
        $priorities = ['low', 'medium', 'high', 'urgent'];

        if ($authors->isEmpty() || $supportUsers->isEmpty() || $statuses->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= $count; $i++) {
            Ticket::create([
                'title' => "Тестовая заявка #{$i}",
                'description' => "Автогенерированное описание заявки #{$i}.",
                'status_id' => $statuses->random(),
                'priority' => $priorities[array_rand($priorities)],
                'assigned_to' => $supportUsers->random(),
                'created_by' => $authors->random(),
                'deadline' => now()->addDays(rand(1, 30)),
            ]);
        }
    }
}
