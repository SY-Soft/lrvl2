<?php

namespace App\Filament\Pages;

use App\Models\Comment;
use App\Models\RequestHistory;
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
use Spatie\Permission\Models\Role;
use UnitEnum;

class Devel extends Page implements HasForms
{
    use InteractsWithForms;

    private const BATCH_SIZE = 5;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-wrench';

    protected static string|UnitEnum|null $navigationGroup = 'Разработка';

    protected static ?string $navigationLabel = 'Devel Tools';

    protected static ?int $navigationSort = 999;

    protected string $view = 'filament.pages.devel';

    public ?array $data = [];

    public bool $batchModalVisible = false;

    public bool $batchConfirming = false;

    public bool $batchRunning = false;

    public string $batchType = '';

    public string $batchTitle = '';

    public string $batchQuestion = '';

    public string $batchStatus = '';

    public int $batchTotal = 0;

    public int $batchProcessed = 0;

    public int $batchDone = 0;

    public int $batchProgress = 0;

    public array $batchPayload = [];

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
                    ->description('Создание и удаление тестовых пользователей (При повторном создании – может не создать, т.к. E-Mail’s уже существует)')
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
                            ->disabled(fn (): bool => $this->batchModalVisible)
                            ->action('confirmCreateUsers'),
                        Action::make('deleteUsers')
                            ->label('Удалить пользователей')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->disabled(fn (): bool => $this->batchModalVisible)
                            ->action('confirmDeleteUsers'),
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
                            ->disabled(fn (): bool => $this->batchModalVisible)
                            ->action('confirmCreateTickets'),
                        Action::make('deleteTickets')
                            ->label('Удалить все тикеты')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->disabled(fn (): bool => $this->batchModalVisible)
                            ->action('confirmDeleteTickets'),
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

    public function confirmCreateUsers(): void
    {
        $count = (int) ($this->data['users_count'] ?? 10);

        $this->showBatchConfirmation(
            type: 'createUsers',
            title: 'Создание пользователей',
            question: "Создать {$count} тестовых пользователей пакетами?",
            total: $count,
        );
    }

    public function confirmDeleteUsers(): void
    {
        $count = User::where('id', '>', 1)->count();

        $this->showBatchConfirmation(
            type: 'deleteUsers',
            title: 'Удаление пользователей',
            question: "Удалить {$count} пользователей кроме администратора пакетами?",
            total: $count,
        );
    }

    public function confirmCreateTickets(): void
    {
        $count = (int) ($this->data['tickets_count'] ?? 30);

        $this->showBatchConfirmation(
            type: 'createTickets',
            title: 'Создание тикетов',
            question: "Создать {$count} тестовых тикетов пакетами?",
            total: $count,
        );
    }

    public function confirmDeleteTickets(): void
    {
        $count = Ticket::count();

        if ($count === 0) {
            Notification::make()->title('Тикетов для удаления нет')->info()->send();

            return;
        }

        $this->showBatchConfirmation(
            type: 'deleteTickets',
            title: 'Удаление тикетов',
            question: "Удалить {$count} тикетов пакетами: комментарии, историю, затем тикеты?",
            total: $count,
        );
    }

    public function startBatch(): void
    {
        if (! $this->batchConfirming) {
            return;
        }

        if ($this->batchTotal === 0) {
            Notification::make()->title('Нет данных для обработки')->info()->send();
            $this->resetBatch();

            return;
        }

        if (! $this->prepareBatchPayload()) {
            $this->resetBatch();

            return;
        }

        $this->batchConfirming = false;
        $this->batchRunning = true;
        $this->batchStatus = "Запущено: {$this->batchTitle}.";
    }

    public function cancelBatch(): void
    {
        if (! $this->batchModalVisible) {
            return;
        }

        $done = $this->batchDone;
        $title = $this->batchTitle;

        $this->resetBatch();

        Notification::make()
            ->title('Операция отменена')
            ->body("{$title}: обработано {$done}.")
            ->warning()
            ->send();
    }

    public function processBatch(): void
    {
        if (! $this->batchRunning) {
            return;
        }

        match ($this->batchType) {
            'createUsers' => $this->processCreateUsersBatch(),
            'deleteUsers' => $this->processDeleteUsersBatch(),
            'createTickets' => $this->processCreateTicketsBatch(),
            'deleteTickets' => $this->processDeleteTicketsBatch(),
            default => $this->resetBatch(),
        };

        $this->refreshBatchProgress();

        if ($this->batchProcessed >= $this->batchTotal) {
            $this->finishBatch();
        }
    }

    private function showBatchConfirmation(string $type, string $title, string $question, int $total): void
    {
        $this->batchModalVisible = true;
        $this->batchConfirming = true;
        $this->batchRunning = false;
        $this->batchType = $type;
        $this->batchTitle = $title;
        $this->batchQuestion = $question;
        $this->batchStatus = '';
        $this->batchTotal = $total;
        $this->batchProcessed = 0;
        $this->batchDone = 0;
        $this->batchProgress = 0;
        $this->batchPayload = [];
    }

    private function prepareBatchPayload(): bool
    {
        if ($this->batchType === 'createUsers') {
            $roles = Role::query()
                ->where('name', '!=', 'admin')
                ->orderBy('id')
                ->pluck('name')
                ->all();

            if (empty($roles)) {
                Notification::make()->title('Сначала создайте роли Spatie Permission')->danger()->send();

                return false;
            }

            $this->batchPayload = ['roles' => $roles];
        }

        if ($this->batchType === 'createTickets') {
            $authors = User::query()->role('user')->pluck('id')->all();
            $supportUsers = User::query()->role('support')->pluck('id')->all();
            $statuses = Status::query()->pluck('id')->all();

            if (empty($authors) || empty($supportUsers) || empty($statuses)) {
                Notification::make()
                    ->title('Нужны роли user/support и статусы')
                    ->danger()
                    ->send();

                return false;
            }

            $this->batchPayload = [
                'authors' => $authors,
                'supportUsers' => $supportUsers,
                'statuses' => $statuses,
                'priorities' => ['low', 'medium', 'high', 'urgent'],
            ];
        }

        return true;
    }

    private function processCreateUsersBatch(): void
    {
        $roles = $this->batchPayload['roles'] ?? [];
        $limit = min(self::BATCH_SIZE, $this->batchTotal - $this->batchProcessed);

        for ($i = 0; $i < $limit; $i++) {
            $number = $this->batchProcessed + 1;
            $role = $roles[($number - 1) % count($roles)];

            User::updateOrCreate([
                'email' => "test{$number}@example.com",
            ], [
                'name' => "Test {$role} {$number}",
                'password' => bcrypt('password'),
            ])->syncRoles([$role]);

            $this->batchProcessed++;
            $this->batchDone++;
        }

        $this->batchStatus = "Создано {$this->batchDone} из {$this->batchTotal} пользователей...";
    }

    private function processDeleteUsersBatch(): void
    {
        $userIds = User::query()
            ->where('id', '>', 1)
            ->orderBy('id')
            ->limit(min(self::BATCH_SIZE, $this->batchTotal - $this->batchProcessed))
            ->pluck('id');

        if ($userIds->isEmpty()) {
            $this->batchProcessed = $this->batchTotal;

            return;
        }

        foreach ($userIds as $userId) {
            $this->batchDone += User::query()->whereKey($userId)->delete();
            $this->batchProcessed++;
        }

        $this->batchStatus = "Удалено {$this->batchDone} из {$this->batchTotal} пользователей...";
    }

    private function processCreateTicketsBatch(): void
    {
        $limit = min(self::BATCH_SIZE, $this->batchTotal - $this->batchProcessed);
        $authors = $this->batchPayload['authors'] ?? [];
        $supportUsers = $this->batchPayload['supportUsers'] ?? [];
        $statuses = $this->batchPayload['statuses'] ?? [];
        $priorities = $this->batchPayload['priorities'] ?? ['low', 'medium', 'high', 'urgent'];

        for ($i = 0; $i < $limit; $i++) {
            $number = $this->batchProcessed + 1;

            Ticket::create([
                'title' => "Тестовая заявка #{$number}",
                'description' => "Автогенерированное описание заявки #{$number}.",
                'status_id' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'assigned_to' => $supportUsers[array_rand($supportUsers)],
                'created_by' => $authors[array_rand($authors)],
                'deadline' => now()->addDays(rand(3, 45)),
            ]);

            $this->batchProcessed++;
            $this->batchDone++;
        }

        $this->batchStatus = "Создано {$this->batchDone} из {$this->batchTotal} тикетов...";
    }

    private function processDeleteTicketsBatch(): void
    {
        $ticketIds = Ticket::query()
            ->orderBy('id')
            ->limit(min(self::BATCH_SIZE, $this->batchTotal - $this->batchProcessed))
            ->pluck('id');

        if ($ticketIds->isEmpty()) {
            $this->batchProcessed = $this->batchTotal;

            return;
        }

        foreach ($ticketIds as $ticketId) {
            Comment::query()->where('ticket_id', $ticketId)->delete();
            RequestHistory::query()->where('ticket_id', $ticketId)->delete();

            $this->batchDone += Ticket::query()->whereKey($ticketId)->delete();
            $this->batchProcessed++;
        }

        $this->batchStatus = "Удалено {$this->batchDone} из {$this->batchTotal} тикетов...";
    }

    private function refreshBatchProgress(): void
    {
        if ($this->batchTotal === 0) {
            $this->batchProgress = 0;

            return;
        }

        $this->batchProgress = min(
            100,
            (int) round(($this->batchProcessed / $this->batchTotal) * 100),
        );
    }

    private function finishBatch(): void
    {
        $title = $this->batchTitle;
        $done = $this->batchDone;

        $this->resetBatch();

        Notification::make()
            ->title("{$title}: готово")
            ->body("Обработано {$done}.")
            ->success()
            ->send();
    }

    private function resetBatch(): void
    {
        $this->batchModalVisible = false;
        $this->batchConfirming = false;
        $this->batchRunning = false;
        $this->batchType = '';
        $this->batchTitle = '';
        $this->batchQuestion = '';
        $this->batchStatus = '';
        $this->batchTotal = 0;
        $this->batchProcessed = 0;
        $this->batchDone = 0;
        $this->batchProgress = 0;
        $this->batchPayload = [];
    }
}
