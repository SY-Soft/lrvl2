<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Support\TicketHistory;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    private array $beforeSave = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->beforeSave = $this->record->only([
            'title',
            'description',
            'status_id',
            'priority',
            'assigned_to',
            'deadline',
        ]);

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->beforeSave as $field => $oldValue) {
            $newValue = $this->record->{$field};

            if ((string) $oldValue !== (string) $newValue) {
                TicketHistory::record($this->record, Auth::user(), $field === 'status_id' ? 'status_id' : 'updated', $oldValue, $newValue);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        // После сохранения — переход на просмотр тикета
        return TicketResource::getUrl('view', ['record' => $this->record]);

        // Или если у тебя нет View страницы, можно на show:
        // return route('dashboard.tickets.show', $this->record);
    }
}
