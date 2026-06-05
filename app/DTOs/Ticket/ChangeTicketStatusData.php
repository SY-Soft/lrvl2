<?php

namespace App\DTOs\Ticket;

class ChangeTicketStatusData
{
    public function __construct(
        public readonly int $status_id,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            status_id: $validated['status_id'],
        );
    }
}
