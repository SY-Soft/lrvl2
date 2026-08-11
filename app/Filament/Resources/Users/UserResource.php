<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $label = 'Пользователь';

    protected static ?string $pluralLabel = 'Пользователи';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return (bool) ($user?->isGod() || $user?->can('users.manage'));
    }

    public static function canCreate(): bool
    {
        return static::currentUserCanManageUsers();
    }
    public static function canView(Model $record): bool
    {
        return static::currentUserCanManageUsers()
            && static::canManageTargetUser($record);
    }
    public static function canEdit(Model $record): bool
    {
        return static::currentUserCanManageUsers()
            && static::canManageTargetUser($record)
            && ! static::isProtectedGodUser($record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::currentUserCanManageUsers()
            && static::canManageTargetUser($record)
            && ! static::isProtectedGodUser($record);
    }

    private static function canManageTargetUser(Model $record): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->isGod() || $user->isAdmin()) {
            return true;
        }

        if ($user->isManager()) {
            return $record instanceof User
                && $record->hasAnyRole(['user', 'support'])
                && !$record->hasAnyRole(['admin', 'manager']);
        }

        return false;
    }
    public static function canDeleteAny(): bool
    {
        return static::currentUserCanManageUsers();
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user?->isManager()) {
            $query
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['user', 'support']);
                })
                ->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'manager']);
                });
        }

        return $query;
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Профиль')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Имя')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->label('Пароль')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->maxLength(255),
                    Forms\Components\Select::make('roles')
                        ->label('Роли')
                        ->relationship(
                            'roles',
                            'name',
                            fn ($query) => Auth::user()?->isManager()
                                ? $query->whereIn('name', ['user', 'support'])
                                : $query
                        )
                        ->multiple()
                        ->preload()
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Роли')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(null)
            ->defaultSort('created_at', 'desc')
            ->checkIfRecordIsSelectableUsing(fn (User $record): bool => ! static::isProtectedGodUser($record))
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (User $record): bool => static::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords(fn (User $record): bool => static::canDelete($record)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    private static function currentUserCanManageUsers(): bool
    {
        $user = Auth::user();

        return (bool) ($user?->isGod() || $user?->can('users.manage'));
    }

    private static function isProtectedGodUser(Model $record): bool
    {
        return $record instanceof User && $record->isGod();
    }
}
