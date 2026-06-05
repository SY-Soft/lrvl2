<?php

namespace App\Actions\Ticket;

use App\DTOs\Ticket\ChangeTicketStatusData;
use App\Models\Ticket;
use App\Support\TicketHistory;

class ChangeTicketStatusAction
{
    public function execute(Ticket $ticket, ChangeTicketStatusData $data): void
    {
        $oldStatus = $ticket->status?->label ?? $ticket->status_id;

        $ticket->update([
            'status_id' => $data->status_id,
        ]);

        $ticket->load('status');

        TicketHistory::record(
            $ticket,
            auth()->user(),
            'status_id',
            $oldStatus,
            $ticket->status?->label ?? $ticket->status_id
        );
    }
}
