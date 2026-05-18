<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages;
use App\Models\Ticket;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
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
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('status.label')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (Ticket $record) => $record->status?->color ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Приоритет')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Исполнитель')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deadline')
                    ->label('Дедлайн')
                    ->dateTime('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Статус')
                    ->relationship('status', 'label')
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Приоритет')
                    ->options([
                        'low' => 'Низкий',
                        'medium' => 'Средний',
                        'high' => 'Высокий',
                        'urgent' => 'Срочный',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
