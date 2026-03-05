<?php

namespace App\Filament\Resources\WeekResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WeekForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCurator = auth()->user()?->isCurator();

        $months = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart',
            4 => 'Aprel', 5 => 'May', 6 => 'Iyun',
            7 => 'Iyul', 8 => 'Avgust', 9 => 'Sentabr',
            10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        return $schema->components([
            Section::make('Sana ma\'lumotlari')
                ->columnSpan(1)
                ->schema([
                    TextInput::make('year')
                        ->label('Yil')
                        ->numeric()
                        ->required()
                        ->default(now()->year)
                        ->minValue(2020)
                        ->maxValue(2050)
                        ->disabled($isCurator),

                    Select::make('month')
                        ->label('Oy')
                        ->options($months)
                        ->required()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled($isCurator),

                    Select::make('week_number')
                        ->label('Hafta raqami')
                        ->options([
                            1 => '1-hafta',
                            2 => '2-hafta',
                            3 => '3-hafta',
                            4 => '4-hafta',
                        ])
                        ->required()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled($isCurator),
                ]),

            Section::make('Hafta ma\'lumotlari')
                ->columnSpan(1)
                ->schema([
                    TextInput::make('title')
                        ->label('Sarlavha')
                        ->required()
                        ->maxLength(255)
                        ->disabled($isCurator),

                    Textarea::make('description')
                        ->label('Tavsif')
                        ->rows(3)
                        ->maxLength(1000)
                        ->disabled($isCurator),
                ]),
        ])->columns(2);
    }
}
