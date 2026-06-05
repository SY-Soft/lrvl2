<?php

namespace App\Http\Controllers;

use App\Actions\Ticket\AddCommentToTicketAction;
use App\Actions\Ticket\ChangeTicketStatusAction;
use App\Actions\Ticket\CreateTicketAction;
use App\Actions\Ticket\EnsureCanViewTicketAction;
use App\DTOs\Ticket\ChangeTicketStatusData;
use App\DTOs\Ticket\CreateTicketData;
use App\Models\Status;
use App\Models\Ticket;
use App\Services\Ticket\TicketQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserTicketController extends Controller
{
    public function __construct(
        protected CreateTicketAction $createTicketAction,
        protected AddCommentToTicketAction $addCommentAction,
        protected ChangeTicketStatusAction $changeStatusAction,
        protected EnsureCanViewTicketAction $ensureCanViewAction,
        protected TicketQueryService $ticketQueryService,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status_id', 'priority', 'search']);
        $sort = $request->only(['field', 'direction']);

        $query = $this->ticketQueryService->getVisibleTicketsQuery($request, $filters, $sort);

        $tickets = $query->paginate(15)->withQueryString();

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

        $data = CreateTicketData::fromRequest($validated, $request->user());
        $ticket = $this->createTicketAction->execute($data);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Тикет создан.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->ensureCanViewAction->execute($request, $ticket);

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
        $this->ensureCanViewAction->execute($request, $ticket);
        abort_unless($request->user()->can('tickets.comment') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
        ]);

        $this->addCommentAction->execute($request, $ticket, $validated['content']);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Комментарий добавлен.');
    }

    public function status(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->ensureCanViewAction->execute($request, $ticket);
        abort_unless($request->user()->can('tickets.change-status') || $request->user()->isGod(), 403);

        $validated = $request->validate([
            'status_id' => ['required', 'exists:statuses,id'],
        ]);

        $data = ChangeTicketStatusData::fromRequest($validated);
        $this->changeStatusAction->execute($ticket, $data);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('status', 'Статус обновлен.');
    }

}
