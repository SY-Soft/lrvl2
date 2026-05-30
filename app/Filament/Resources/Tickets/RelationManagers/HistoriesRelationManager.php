<?php

namespace App\Filament\Resources\Tickets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $title = 'История';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Кто')
                    ->placeholder('Система'),
                TextColumn::make('field')
                    ->label('Событие')
                    ->formatStateUsing(fn (?string $state): string => [
                        'created' => 'Создание',
                        'comment' => 'Комментарий',
                        'status_id' => 'Смена статуса',
                        'updated' => 'Изменение',
                    ][$state] ?? (string) $state),
                TextColumn::make('old_value')
                    ->label('Было')
                    ->placeholder('-')
                    ->limit(40),
                TextColumn::make('new_value')
                    ->label('Стало')
                    ->placeholder('-')
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label('Когда')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
