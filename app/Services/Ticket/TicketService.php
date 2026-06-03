<?php

namespace App\Services\Ticket;

use App\Models\Status;
use App\Models\Ticket;
use App\Support\TicketHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TicketService
{
    /**
     * Получаем список тикетов с учётом прав пользователя
     */
    public function getVisibleTicketsQuery(Request $request, array $filters = [], array $sort = []): Builder
    {
        $user = $request->user();
        $query = $this->visibleTickets($request);

        // Фильтры
        if (!empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Сортировка
        if (!empty($sort['field']) && !empty($sort['direction'])) {
            $query->orderBy($sort['field'], $sort['direction']);
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Создание тикета
     */
    public function createTicket(Request $request, array $validated): Ticket
    {
        $status = Status::query()
            ->where('name', 'new')
            ->orWhere('order', 1)
            ->orderBy('order')
            ->firstOrFail();

        $ticket = Ticket::create([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'status_id'    => $status->id,
            'priority'     => $validated['priority'],
            'created_by'   => $request->user()->id,
            'deadline'     => $validated['deadline'] ?? null,
        ]);

        TicketHistory::record($ticket, $request->user(), 'created', null, 'Тикет создан');

        return $ticket;
    }

    /**
     * Базовый запрос с видимостью тикетов (используется в index и в ensureVisible)
     */
    private function visibleTickets(Request $request): Builder
    {
        $user = $request->user();

        if ($user->isAdmin() || $user->can('tickets.view-all')) {
            return Ticket::query();
        }

        if ($user->isSupport()) {
            return Ticket::query()->where('assigned_to', $user->id);
        }

        return Ticket::query()
            ->where(function (Builder $query) use ($user) {
                $query
                    ->where('created_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
    }
    /**
     * Проверка, может ли пользователь видеть тикет
     */
    public function ensureUserCanViewTicket(Request $request, Ticket $ticket): void
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
            || $user->can('tickets.view-all')
            || ($user->can('tickets.view-own') && $ticket->created_by === $user->id)
            || ($user->can('tickets.view-assigned') && $ticket->assigned_to === $user->id),
            403,
        );
    }

    /**
     * Добавление комментария
     */
    public function addComment(Request $request, Ticket $ticket, array $validated): void
    {
        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        TicketHistory::record($ticket, $request->user(), 'comment', null, $validated['content']);
    }

    /**
     * Изменение статуса тикета
     */
    public function changeStatus(Request $request, Ticket $ticket, array $validated): void
    {
        $oldStatus = $ticket->status?->label ?? $ticket->status_id;

        $ticket->update([
            'status_id' => $validated['status_id'],
        ]);

        $ticket->load('status');

        TicketHistory::record(
            $ticket,
            $request->user(),
            'status_id',
            $oldStatus,
            $ticket->status?->label ?? $ticket->status_id
        );
    }
}
