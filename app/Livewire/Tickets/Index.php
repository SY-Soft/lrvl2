<?php

namespace App\Livewire\Tickets;

use App\Models\Status;
use App\Services\Ticket\TicketQueryService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $status_id = '';
    public string $priority = '';
    public string $search = '';

    public string $field = 'created_at';
    public string $direction = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_id' => ['except' => ''],
        'priority' => ['except' => ''],
        'field' => ['except' => 'created_at'],
        'direction' => ['except' => 'desc'],
    ];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $filters = [
            'status_id' => $this->status_id,
            'priority' => $this->priority,
            'search' => $this->search,
        ];

        $sort = [
            'field' => $this->field,
            'direction' => $this->direction,
        ];

        $tickets = app(TicketQueryService::class)
            ->getVisibleTicketsQuery(
                request(),
                $filters,
                $sort
            )
            ->with([
                'status',
                'createdBy',
                'assignedTo',
            ])
            ->paginate(12);

        $statuses = \App\Models\Status::query()
            ->orderBy('order')
            ->get();

        return view('livewire.tickets.index', [
            'tickets' => $tickets,
            'statuses' => $statuses,
            'direction' => $this->direction,
            ])
            ->layout('layouts.app')
            ->title(__('Заявки'));
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status_id = '';
        $this->priority = '';
        $this->field = 'created_at';
        $this->direction = 'desc';

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusId(): void
    {
        $this->resetPage();
    }

    public function updatedPriority(): void
    {
        $this->resetPage();
    }

    public function updatedField(): void
    {
        $this->resetPage();
    }

    public function updatedDirection(): void
    {
        $this->resetPage();
    }
}
