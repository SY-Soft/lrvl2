<?php

namespace App\Actions\Ticket;

use App\DTOs\Ticket\CreateTicketData;
use App\Models\Status;
use App\Models\Ticket;
use App\Support\TicketHistory;

class CreateTicketAction
{
    public function execute(CreateTicketData $data): Ticket
    {
        $status = Status::query()
            ->where('name', 'new')
            ->orWhere('order', 1)
            ->orderBy('order')
            ->firstOrFail();

        $ticket = Ticket::create([
            'title'        => $data->title,
            'description'  => $data->description,
            'status_id'    => $status->id,
            'priority'     => $data->priority,
            'created_by'   => $data->user->id,
            'deadline'     => $data->deadline,
        ]);

        TicketHistory::record($ticket, $data->user, 'created', null, 'Тикет создан');

        return $ticket;
    }
}
