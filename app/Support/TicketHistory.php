<?php

namespace App\Support;

use App\Models\Ticket;
use App\Models\User;

class TicketHistory
{
    public static function record(Ticket $ticket, ?User $user, string $field, mixed $oldValue = null, mixed $newValue = null): void
    {
        $ticket->histories()->create([
            'user_id' => $user?->id ?? $ticket->created_by,
            'field' => $field,
            'old_value' => self::stringValue($oldValue),
            'new_value' => self::stringValue($newValue),
        ]);
    }

    private static function stringValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}
