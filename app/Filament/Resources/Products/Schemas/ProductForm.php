<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название товара')
                    ->required()
                    ->maxLength(255),

                Select::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'name')   // используем name как title
                    ->searchable()
                    ->preload()
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull()
                    ->rows(4),

                TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->prefix('₴')
                    ->required()
                    ->minValue(0),

                TextInput::make('stock')
                    ->label('Остаток на складе')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Товар активен')
                    ->default(true),
            ]);
    }
}
