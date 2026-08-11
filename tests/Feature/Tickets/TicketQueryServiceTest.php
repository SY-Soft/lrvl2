<?php

namespace Tests\Feature\Tickets;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Ticket\TicketQueryService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketQueryServiceTest extends TestCase
{
    use RefreshDatabase;

    private TicketQueryService $service;
    protected $seed = RoleSeeder::class;

    private function user(): User
    {
        $user = User::factory()->create();

        $user->assignRole('user');

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TicketQueryService();

        Status::factory()->create();

    }


    public function test_admin_can_see_all_tickets(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->role('admin'));

        $ownTicket = Ticket::factory()->create([
            'created_by' => $admin->id,
        ]);

        $otherUser = User::factory()->create();

        $otherTicket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $admin);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertTrue($tickets->contains($ownTicket->id));
        $this->assertTrue($tickets->contains($otherTicket->id));
    }

     public function test_manager_can_see_all_tickets(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole($this->role('manager'));

        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $manager);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertTrue($tickets->contains($ticket->id));
    }

    public function test_user_with_view_all_permission_can_see_all_tickets(): void
    {
        $user = User::factory()->create();

        $permission = Permission::findOrCreate('tickets.view-all');
        $user->givePermissionTo($permission);

        $otherUser = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertTrue($tickets->contains($ticket->id));
    }

    public function test_support_user_sees_only_assigned_tickets(): void
    {
        $support = User::factory()->create();
        $support->assignRole($this->role('support'));

        $assignedTicket = Ticket::factory()->create([
            'assigned_to' => $support->id,
        ]);

        $createdBySupportTicket = Ticket::factory()->create([
            'created_by' => $support->id,
        ]);

        $otherUser = User::factory()->create();

        $otherTicket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
            'assigned_to' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $support);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertTrue($tickets->contains($assignedTicket->id));
        $this->assertFalse($tickets->contains($createdBySupportTicket->id));
        $this->assertFalse($tickets->contains($otherTicket->id));
    }

    public function test_regular_user_sees_tickets_created_by_themselves(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $otherUser->id,
        ]);

        $otherTicket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
            'assigned_to' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertTrue($tickets->contains($ownTicket->id));
        $this->assertFalse($tickets->contains($otherTicket->id));
    }

    public function test_regular_user_sees_tickets_assigned_to_themselves(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $assignedTicket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
            'assigned_to' => $user->id,
        ]);

        $otherTicket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
            'assigned_to' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertTrue($tickets->contains($assignedTicket->id));
        $this->assertFalse($tickets->contains($otherTicket->id));
    }

    public function test_regular_user_does_not_see_unrelated_tickets(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $unrelatedTicket = Ticket::factory()->create([
            'created_by' => $otherUser->id,
            'assigned_to' => $otherUser->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->pluck('id');

        $this->assertFalse($tickets->contains($unrelatedTicket->id));
    }

    public function test_it_filters_tickets_by_status(): void
    {
        $user = $this->user();

        $newStatus = $this->newStatus();
        $inProgressStatus = $this->inProgressStatus();

        $ticket1 = Ticket::factory()->create([
            'created_by' => $user->id,
            'status_id' => $newStatus->id,
        ]);

        $ticket2 = Ticket::factory()->create([
            'created_by' => $user->id,
            'status_id' => $inProgressStatus->id,
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery(
                $request,
                ['status_id' => $newStatus->id]
            )
            ->pluck('id');

        $this->assertTrue($tickets->contains($ticket1->id));
        $this->assertFalse($tickets->contains($ticket2->id));
    }

    public function test_it_filters_tickets_by_priority(): void
    {
        $user = User::factory()->create();

        $highPriorityTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'priority' => 'high',
        ]);

        $lowPriorityTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'priority' => 'low',
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery(
                $request,
                ['priority' => 'high']
            )
            ->pluck('id');

        $this->assertTrue($tickets->contains($highPriorityTicket->id));
        $this->assertFalse($tickets->contains($lowPriorityTicket->id));
    }

    public function test_it_searches_in_title_or_description(): void
    {
        $user = User::factory()->create();

        $titleTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'title' => 'Laravel integration problem',
            'description' => 'Something else',
        ]);

        $descriptionTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'title' => 'Another ticket',
            'description' => 'Laravel integration is broken',
        ]);

        $unrelatedTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'title' => 'Redis problem',
            'description' => 'Cache issue',
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery(
                $request,
                ['search' => 'Laravel']
            )
            ->pluck('id');

        $this->assertTrue($tickets->contains($titleTicket->id));
        $this->assertTrue($tickets->contains($descriptionTicket->id));
        $this->assertFalse($tickets->contains($unrelatedTicket->id));
    }

    public function test_it_sorts_by_requested_field_and_direction(): void
    {
        $user = User::factory()->create();

        $low = Ticket::factory()->create([
            'created_by' => $user->id,
            'priority' => 'low',
        ]);

        $urgent = Ticket::factory()->create([
            'created_by' => $user->id,
            'priority' => 'urgent',
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery(
                $request,
                [],
                [
                    'field' => 'priority',
                    'direction' => 'asc',
                ]
            )
            ->get();

        $this->assertSame($low->id, $tickets->first()->id);
        $this->assertSame($urgent->id, $tickets->last()->id);
    }

    public function test_it_sorts_by_created_at_desc_by_default(): void
    {
        $user = User::factory()->create();

        $oldTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'created_at' => now()->subMinutes(10),
        ]);

        $newTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $tickets = $this->service
            ->getVisibleTicketsQuery($request)
            ->get();

        $this->assertSame($newTicket->id, $tickets->first()->id);
        $this->assertSame($oldTicket->id, $tickets->last()->id);
    }

    private function role(string $name): Role
    {
        return Role::findOrCreate($name);
    }
}
