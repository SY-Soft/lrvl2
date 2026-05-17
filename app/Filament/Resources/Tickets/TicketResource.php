<?php

namespace App\Filament\Resources\Tickets;

use App\Models\Ticket;
use BackedEnum;                          // ← обязательно
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;



class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::Ticket;   // ← вот так

    protected static ?string $label = 'Заявка';
    protected static ?string $pluralLabel = 'Заявки';
    protected static ?string $recordTitleAttribute = 'title';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(5),

                Forms\Components\Select::make('status_id')
                    ->label('Статус')
                    ->relationship('status', 'label')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->label('Приоритет')
                    ->options([
                        'low'    => 'Низкий',
                        'medium' => 'Средний',
                        'high'   => 'Высокий',
                        'urgent' => 'Срочный',
                    ])
                    ->default('medium'),

                Forms\Components\Select::make('assigned_to')
                    ->label('Исполнитель')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->nullable(),

                Forms\Components\DateTimePicker::make('deadline')
                    ->label('Дедлайн'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status.label')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (Ticket $record) => $record->status?->color ?? 'gray'),

                Tables\Columns\TextColumn::make('priority')
                    ->badge(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Исполнитель'),

                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime('d.m.Y H:i'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                ActionGroup::make([
                    DeleteBulkAction::make(),   // ← вот правильный
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages1\ListTickets::route('/'),
            'create' => Pages1\CreateTicket::route('/create'),
            'view'   => Pages1\ViewTicket::route('/{record}'),
            'edit'   => Pages1\EditTicket::route('/{record}/edit'),
        ];
    }
}
