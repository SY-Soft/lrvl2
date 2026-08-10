<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->required(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Select::make('assigned_to')
                    ->label('Исполнитель')
                    ->relationship(
                        'assignedTo',
                        'name',
                        modifyQueryUsing: fn (Builder $query) =>
                        $query->whereHas(
                            'roles',
                            fn (Builder $query) =>
                            $query->where('name', 'support')
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->default('medium')
                    ->required(),
                DateTimePicker::make('deadline'),
            ]);
    }
}
