<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $createdCount = Ticket::where('created_by', $user->id)->count();
        $assignedCount = Ticket::where('assigned_to', $user->id)->count();

        $createdTickets = Ticket::query()
            ->with(['status', 'assignedTo'])
            ->where('created_by', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $assignedTickets = Ticket::query()
            ->with(['status', 'createdBy'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'created' => $createdCount,
            'assigned' => $assignedCount,
            'active' => Ticket::where('created_by', $user->id)
                ->whereHas('status', fn ($query) => $query->where('is_final', false))
                ->count(),
        ];

        $hasMoreCreated = $createdCount > 5;
        $hasMoreAssigned = $assignedCount > 5;

        return view('pages.dashboard', compact(
            'user',
            'createdTickets',
            'assignedTickets',
            'stats',
            'hasMoreCreated',
            'hasMoreAssigned',
        ));
    }
}
