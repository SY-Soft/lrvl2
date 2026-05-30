<?php

namespace App\Filament\Pages;

use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class Devel extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-wrench';

    protected static string|UnitEnum|null $navigationGroup = 'Разработка';

    protected static ?string $navigationLabel = 'Devel Tools';

    protected static ?int $navigationSort = 999;

    protected string $view = 'filament.pages.devel';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'users_count' => 10,
            'tickets_count' => 30,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Пользователи')
                    ->description('Создание и удаление тестовых пользователей')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                TextInput::make('users_count')
                                    ->label('Количество пользователей')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->default(10)
                                    ->required()
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 4,
                                    ]),
                            ]),
                    ])
                    ->footerActions([
                        Action::make('createUsers')
                            ->label('Создать пользователей')
                            ->icon('heroicon-o-plus')
                            ->color('success')
                            ->action('createUsers'),
                        Action::make('deleteUsers')
                            ->label('Удалить пользователей')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Удаление пользователей')
                            ->modalDescription('Удалить всех тестовых пользователей кроме администратора?')
                            ->action('deleteUsers'),
                    ]),

                Section::make('Тикеты')
                    ->description('Создание и удаление тестовых тикетов')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                TextInput::make('tickets_count')
                                    ->label('Количество тикетов')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(500)
                                    ->default(30)
                                    ->required()
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 4,
                                    ]),
                            ]),
                    ])
                    ->footerActions([
                        Action::make('createTickets')
                            ->label('Создать тикеты')
                            ->icon('heroicon-o-plus')
                            ->color('success')
                            ->action('createTickets'),
                        Action::make('deleteTickets')
                            ->label('Удалить все тикеты')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Удаление тикетов')
                            ->modalDescription('Удалить все тикеты?')
                            ->action('deleteTickets'),
                    ]),

                Section::make('Статистика')
                    ->description('Текущее состояние тестовых данных')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('users_stats')
                                    ->label('Пользователей всего')
                                    ->content(fn () => User::count() . ' шт.'),
                                Placeholder::make('tickets_stats')
                                    ->label('Тикетов всего')
                                    ->content(fn () => Ticket::count() . ' шт.'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function createUsers(): void
    {
        $count = $this->data['users_count'] ?? 10;
        $roles = ['user', 'support', 'manager'];

        for ($i = 1; $i <= $count; $i++) {
            $role = $roles[($i - 1) % count($roles)];

            User::updateOrCreate([
                'email' => "test{$i}@example.com",
            ], [
                'name' => "Test {$role} {$i}",
                'password' => bcrypt('password'),
            ])->syncRoles([$role]);
        }

        Notification::make()->title("Создано {$count} пользователей")->success()->send();
        $this->redirect(static::getUrl());
    }

    public function deleteUsers(): void
    {
        $count = User::where('id', '>', 1)->delete();

        Notification::make()->title("Удалено {$count} пользователей")->success()->send();
        $this->redirect(static::getUrl());
    }

    public function createTickets(): void
    {
        $count = $this->data['tickets_count'] ?? 30;
        $created = $this->generateTickets($count);

        Notification::make()->title("Создано {$created} тикетов")->success()->send();
        $this->redirect(static::getUrl());
    }

    public function deleteTickets(): void
    {
        $count = Ticket::count();
        Ticket::truncate();

        Notification::make()->title("Удалено {$count} тикетов")->success()->send();
        $this->redirect(static::getUrl());
    }

    private function generateTickets(int $count): int
    {
        $authors = User::query()->role('user')->pluck('id')->all();
        $supportUsers = User::query()->role('support')->pluck('id')->all();
        $statuses = Status::query()->pluck('id')->all();

        if (empty($authors) || empty($supportUsers) || empty($statuses)) {
            Notification::make()
                ->title('Нужны роли user/support и статусы')
                ->danger()
                ->send();

            return 0;
        }

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $created = 0;

        for ($i = 1; $i <= $count; $i++) {
            Ticket::create([
                'title' => "Тестовая заявка #{$i}",
                'description' => "Автогенерированное описание заявки #{$i}.",
                'status_id' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'assigned_to' => $supportUsers[array_rand($supportUsers)],
                'created_by' => $authors[array_rand($authors)],
                'deadline' => now()->addDays(rand(3, 45)),
            ]);

            $created++;
        }

        return $created;
    }
}
