<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use App\Support\TicketHistory;
use Illuminate\Http\Request;

class AddCommentToTicketAction
{
    public function execute(Request $request, Ticket $ticket, string $content): void
    {
        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $content,
        ]);

        TicketHistory::record($ticket, $request->user(), 'comment', null, $content);
    }
}
