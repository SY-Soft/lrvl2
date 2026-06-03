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
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status_id', 'priority', 'search']);
        $sort = $request->only(['field', 'direction']);

        $query = $this->ticketService->getVisibleTicketsQuery($request, $filters, $sort);

        $tickets = $query->paginate(15);

        return view('pages.tickets.index', compact('tickets'));
    }


    public function create(): View
    {
        return view('pages.tickets.create');
    }


    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('tickets.create') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:4000'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'deadline'    => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $ticket = $this->ticketService->createTicket($request, $validated);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Тикет создан.');
    }
    public function show(Request $request, Ticket $ticket): View
    {
        $this->ticketService->ensureUserCanViewTicket($request, $ticket);

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
        $this->ticketService->ensureUserCanViewTicket($request, $ticket);
        abort_unless($request->user()->can('tickets.comment') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $this->ticketService->addComment($request, $ticket, $validated);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Комментарий добавлен.');
    }

    public function status(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ticketService->ensureUserCanViewTicket($request, $ticket);
        abort_unless($request->user()->can('tickets.change-status') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'status_id' => ['required', 'exists:statuses,id'],
        ]);

        $this->ticketService->changeStatus($request, $ticket, $validated);

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
