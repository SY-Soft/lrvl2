<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Ticket;
use App\Support\TicketHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserTicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tickets = $this->visibleTickets($request)
            ->with(['status', 'createdBy', 'assignedTo'])
            ->when($request->filled('scope'), function (Builder $query) use ($request, $user) {
                match ($request->string('scope')->toString()) {
                    'created' => $query->where('created_by', $user->id),
                    'assigned' => $query->where('assigned_to', $user->id),
                    default => null,
                };
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status_id', $request->integer('status')))
            ->when($request->filled('priority'), fn (Builder $query) => $query->where('priority', $request->string('priority')->toString()))
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = '%' . $request->string('search')->trim()->toString() . '%';

                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('title', 'like', $search)
                        ->orWhere('description', 'like', $search);
                });
            });

        match ($request->string('sort')->toString()) {
            'oldest' => $tickets->oldest(),
            'deadline' => $tickets->orderByRaw('deadline is null')->orderBy('deadline'),
            'priority' => $tickets->orderByRaw("case priority when 'urgent' then 1 when 'high' then 2 when 'medium' then 3 else 4 end"),
            default => $tickets->latest(),
        };

        return view('pages.tickets.index', [
            'tickets' => $tickets->paginate(12)->withQueryString(),
            'statuses' => Status::query()->orderBy('order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.create') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:4000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $status = Status::query()
            ->where('name', 'new')
            ->orWhere('order', 1)
            ->orderBy('order')
            ->firstOrFail();

        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status_id' => $status->id,
            'priority' => $validated['priority'],
            'created_by' => $request->user()->id,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        TicketHistory::record($ticket, $request->user(), 'created', null, 'Тикет создан');

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Тикет создан.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->ensureVisible($request, $ticket);

        $ticket->load([
            'status',
            'createdBy',
            'assignedTo',
            'comments.user',
            'histories.user',
        ]);

        return view('pages.tickets.show', [
            'ticket' => $ticket,
            'statuses' => Status::query()->orderBy('order')->get(),
        ]);
    }

    public function comment(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureVisible($request, $ticket);
        abort_unless($request->user()->can('tickets.comment') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        TicketHistory::record($ticket, $request->user(), 'comment', null, $validated['content']);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Комментарий добавлен.');
    }

    public function status(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureVisible($request, $ticket);
        abort_unless($request->user()->can('tickets.change-status') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'status_id' => ['required', 'exists:statuses,id'],
        ]);

        $oldStatus = $ticket->status?->label ?? $ticket->status_id;

        $ticket->update([
            'status_id' => $validated['status_id'],
        ]);

        $ticket->load('status');

        TicketHistory::record($ticket, $request->user(), 'status_id', $oldStatus, $ticket->status?->label ?? $ticket->status_id);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Статус обновлен.');
    }

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

    private function ensureVisible(Request $request, Ticket $ticket): void
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
}
