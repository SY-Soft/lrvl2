<?php

namespace App\DTOs\Ticket;

use App\Models\User;

class CreateTicketData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $priority,
        public readonly ?string $deadline,
        public readonly User $user,
    ) {}

    public static function fromRequest(array $validated, User $user): self
    {
        return new self(
            title: $validated['title'],
            description: $validated['description'] ?? null,
            priority: $validated['priority'],
            deadline: $validated['deadline'] ?? null,
            user: $user,
        );
    }
}
