<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Status;
use App\Support\TicketHistory;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('comment')
                ->label('Комментарий')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->form([
                    Textarea::make('content')
                        ->label('Комментарий')
                        ->required()
                        ->rows(5),
                ])
                ->action(function (array $data): void {
                    $this->record->comments()->create([
                        'user_id' => Auth::id(),
                        'content' => $data['content'],
                    ]);

                    TicketHistory::record($this->record, Auth::user(), 'comment', null, $data['content']);
                }),
            Action::make('status')
                ->label('Сменить статус')
                ->icon('heroicon-o-check-circle')
                ->form([
                    Select::make('status_id')
                        ->label('Статус')
                        ->options(fn () => Status::query()->orderBy('order')->pluck('label', 'id')->all())
                        ->default(fn () => $this->record->status_id)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status?->label ?? $this->record->status_id;

                    $this->record->update([
                        'status_id' => $data['status_id'],
                    ]);

                    $this->record->load('status');

                    TicketHistory::record($this->record, Auth::user(), 'status_id', $oldStatus, $this->record->status?->label ?? $this->record->status_id);
                }),
            EditAction::make(),
        ];
    }
}
