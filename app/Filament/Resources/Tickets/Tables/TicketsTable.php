<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    private const PRIORITIES = [
        'low' => 'Низкий',
        'medium' => 'Средний',
        'high' => 'Высокий',
        'urgent' => 'Срочный',
    ];

    public static function configure(Table $table): Table
    {
        \Debugbar::info(self::PRIORITIES);
        \Debugbar::error('Error!');
        \Debugbar::warning('Watch out…');

        $statusOptions = Status::query()
            ->orderBy('order')
            ->pluck('label', 'id')
            ->all();

        $userOptions = User::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('status.label')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (Ticket $record) => $record->status?->color ?? 'gray')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Приоритет')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::PRIORITIES[$state] ?? (string) $state)
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Исполнитель')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('deadline')
                    ->label('Дедлайн')
                    ->dateTime('d.m.Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters(
                [
                    SelectFilter::make('status_id')
                        ->label('Статус')
                        ->options($statusOptions)
                        ->native(),
                    SelectFilter::make('priority')
                        ->label('Приоритет')
                        ->options(self::PRIORITIES)
                        ->native(),
                    SelectFilter::make('assigned_to')
                        ->label('Исполнитель')
                        ->options($userOptions)
                        ->native(),
                ],
                layout: FiltersLayout::AboveContent,
            )
            ->filtersFormColumns(3)
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
