<?php

namespace App\Filament\Pages;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Статистика')
                    ->description('Текущее состояние тестовых данных')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('users_stats')
                                    ->label('Пользователей всего')
                                    ->content(fn () => User::count() . ' шт.'),
                                Placeholder::make('tickets_stats')
                                    ->label('Тикетов всего')
                                    ->content(fn () => Ticket::count() . ' шт.'),
                                Placeholder::make('statuses_stats')
                                    ->label('Статусов всего')
                                    ->content(fn () => Status::count() . ' шт.'),
                            ]),
                    ]),
                ...(method_exists($this, 'getFiltersForm') ? [$this->getFiltersFormContentComponent()] : []),
                $this->getWidgetsContentComponent(),
            ]);
    }
}
