<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Filters;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')           // вместо category_id
                ->label('Категория')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Цена')
                    ->money('UAH')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Остаток')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('price')
                    ->label('Цена')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('От')
                            ->numeric(),
                        Forms\Components\TextInput::make('price_to')
                            ->label('До')
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['price_from'] ?? null, fn ($q) => $q->where('price', '>=', $data['price_from']))
                            ->when($data['price_to'] ?? null, fn ($q) => $q->where('price', '<=', $data['price_to']));
                    }),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активен'),

                Tables\Filters\Filter::make('stock')
                    ->label('Наличие')
                    ->form([
                        Forms\Components\TextInput::make('stock_min')
                            ->label('Остаток от')
                            ->numeric()
                            ->default(1),
                    ])
                    ->query(fn ($query, array $data) =>
                    $query->when($data['stock_min'] ?? null, fn ($q) => $q->where('stock', '>=', $data['stock_min']))
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
