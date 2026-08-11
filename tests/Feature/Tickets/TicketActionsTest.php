<?php

namespace Tests\Feature\Tickets;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Database\Seeders\RoleSeeder;

class TicketActionsTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = RoleSeeder::class;
    private function user(): User
    {
        $user = User::factory()->create();

        $user->assignRole('user');

        return $user;
    }

    private function support(): User
    {
        $user = User::factory()->create();

        $user->assignRole('support');

        return $user;
    }
    private function newStatus(): Status
    {
        return Status::firstOrCreate(
            ['name' => 'new'],
            [
                'label' => 'Новая',
                'color' => 'secondary',
                'order' => 1,
                'is_final' => false,
            ]
        );
    }

    private function inProgressStatus(): Status
    {
        return Status::firstOrCreate(
            ['name' => 'in_progress'],
            [
                'label' => 'В работе',
                'color' => 'warning',
                'order' => 2,
                'is_final' => false,
            ]
        );
    }

    public function test_user_can_create_ticket(): void
    {
        $user = $this->user();

        $status = $this->newStatus();

        $response = $this
            ->actingAs($user)
            ->post(route('dashboard.tickets.store'), [
                'title' => 'Тестовый тикет',
                'description' => 'Описание тестового тикета',
                'priority' => 'high',
            ]);

        $ticket = Ticket::query()
            ->where('title', 'Тестовый тикет')
            ->first();

        $this->assertNotNull($ticket);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Тестовый тикет',
            'created_by' => $user->id,
            'status_id' => $status->id,
            'priority' => 'high',
        ]);

        $response->assertRedirect(
            route('dashboard.tickets.show', $ticket)
        );
    }

    public function test_user_cannot_create_ticket_without_title(): void
    {
        $user = $this->user();

        $response = $this
            ->actingAs($user)
            ->post(route('dashboard.tickets.store'), [
                'description' => 'Описание',
                'priority' => 'high',
            ]);

        $response->assertSessionHasErrors('title');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_user_can_add_comment_to_ticket(): void
    {
        $user = $this->user();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'status_id' => $this->newStatus()->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(
                route('dashboard.tickets.comments.store', $ticket),
                [
                    'content' => 'Тестовый комментарий',
                ]
            );

        $response->assertRedirect(
            route('dashboard.tickets.show', $ticket)
        );

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => 'Тестовый комментарий',
        ]);
    }

    public function test_user_cannot_add_empty_comment(): void
    {
        $user = $this->user();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'status_id' => $this->newStatus()->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(
                route('dashboard.tickets.comments.store', $ticket),
                [
                    'content' => '',
                ]
            );

        $response->assertSessionHasErrors('content');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_support_can_change_ticket_status(): void
    {
        $user = $this->user();
        $support = $this->support();

        $newStatus = $this->newStatus();
        $inProgressStatus = $this->inProgressStatus();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $support->id,
            'status_id' => $newStatus->id,
        ]);

        $response = $this
            ->actingAs($support)
            ->patch(
                route('dashboard.tickets.status.update', $ticket),
                [
                    'status_id' => $inProgressStatus->id,
                ]
            );

        $response->assertRedirect(
            route('dashboard.tickets.show', $ticket)
        );

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status_id' => $inProgressStatus->id,
        ]);
    }
    public function test_user_cannot_change_ticket_status(): void
    {
        $user = $this->user();

        $newStatus = $this->newStatus();
        $inProgressStatus = $this->inProgressStatus();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'status_id' => $newStatus->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch(
                route('dashboard.tickets.status.update', $ticket),
                [
                    'status_id' => $inProgressStatus->id,
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status_id' => $newStatus->id,
        ]);
    }

}
