<?php

namespace App\Services\Ticket;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TicketQueryService
{
    /**
     * Получаем Query Builder с тикетами, которые пользователь может видеть
     */
    public function getVisibleTicketsQuery(Request $request, array $filters = [], array $sort = []): Builder
    {
        $query = $this->getBaseVisibleQuery($request);

        // Фильтры
        if (!empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        // Сортировка
        if (!empty($sort['field']) && !empty($sort['direction'])) {
            $query->orderBy($sort['field'], $sort['direction']);
        } else {
            $query->latest('created_at');
        }

        return $query;
    }

    /**
     * Базовая видимость тикетов в зависимости от роли пользователя
     */
    private function getBaseVisibleQuery(Request $request): Builder
    {
        $user = $request->user();

        if ($user->isAdmin() || $user->can('tickets.view-all')) {
            return Ticket::query();
        }

        if ($user->isSupport()) {
            return Ticket::query()->where('assigned_to', $user->id);
        }

        // Обычный пользователь видит свои созданные или назначенные тикеты
        return Ticket::query()
            ->where(function (Builder $query) use ($user) {
                $query
                    ->where('created_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
    }
}
