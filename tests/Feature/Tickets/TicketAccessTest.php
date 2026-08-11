<?php

namespace Tests\Feature\Tickets;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Concerns\SeedsRoles;

class TicketAccessTest extends TestCase
{
    use RefreshDatabase, SeedsRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpSeedsRoles();
    }

    private function statusId(): int
    {
        return Status::factory()->create()->id;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard/tickets');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_tickets_page(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard/tickets');

        $response->assertOk();
    }

    public function test_user_can_see_his_own_ticket(): void
    {
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'status_id' => $this->statusId(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard/tickets');

        $response->assertOk();
        $response->assertSee($ticket->title);
    }

    public function test_user_cannot_open_another_users_ticket(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
            'status_id' => $this->statusId(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get("/dashboard/tickets/{$ticket->id}");

        $response->assertForbidden();
    }
}
