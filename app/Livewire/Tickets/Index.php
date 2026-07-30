<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Services\Ticket\TicketQueryService;

class Index extends Component
{
    public $status_id = '';
    public $priority = '';
    public $search = '';

    public $field = 'created_at';
    public $direction = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_id' => ['except' => ''],
        'priority' => ['except' => ''],
    ];

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

        $query = app(TicketQueryService::class)
            ->getVisibleTicketsQuery(
                request(),
                $filters,
                $sort
            );
        return view('livewire.tickets.index', [
            // 'products' => $products,
            // 'categories' => $categories,
        ])->layout('layouts.app');

        // return view('livewire.tickets.index');
    }
}
