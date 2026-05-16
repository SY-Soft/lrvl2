<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Ticket;

    protected static ?string $label = 'Заявка';
    protected static ?string $pluralLabel = 'Заявки';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view'   => Pages\ViewTicket::route('/{record}'),
            'edit'   => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
