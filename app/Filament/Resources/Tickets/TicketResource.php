<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages;
use App\Filament\Resources\Tickets\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Tickets\RelationManagers\HistoriesRelationManager;
use App\Filament\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Ticket;

    protected static ?string $label = 'Заявка';

    protected static ?string $pluralLabel = 'Заявки';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Основная информация')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Заголовок')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label('Описание')
                        ->rows(6)
                        ->columnSpanFull(),
                ]),

            Section::make('Параметры заявки')
                ->schema([
                    Forms\Components\Select::make('status_id')
                        ->label('Статус')
                        ->relationship('status', 'label')
                        ->required(),

                    Forms\Components\Select::make('priority')
                        ->label('Приоритет')
                        ->options([
                            'low' => 'Низкий',
                            'medium' => 'Средний',
                            'high' => 'Высокий',
                            'urgent' => 'Срочный',
                        ])
                        ->default('medium'),

                    Forms\Components\Select::make('assigned_to')
                        ->label('Исполнитель')
                        ->relationship('assignedTo', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\DateTimePicker::make('deadline')
                        ->label('Дедлайн'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

}
