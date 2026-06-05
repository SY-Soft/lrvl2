<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use Illuminate\Http\Request;

class EnsureCanViewTicketAction
{
    public function execute(Request $request, Ticket $ticket): void
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
            || $user->can('tickets.view-all')
            || ($user->can('tickets.view-own') && $ticket->created_by === $user->id)
            || ($user->can('tickets.view-assigned') && $ticket->assigned_to === $user->id),
            403,
            'У вас нет доступа к этому тикету.'
        );
    }
}
