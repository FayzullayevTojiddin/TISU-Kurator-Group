<?php

namespace App\Filament\Resources\TaskResource\Schemas;

use App\Enums\DayOfWeek;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCurator = auth()->user()?->isCurator();

        return $schema->components([
            Section::make('Vaqt ma\'lumotlari')
                ->columnSpan(1)
                ->schema([
                    Select::make('week_id')
                        ->label('Hafta')
                        ->relationship('week', 'title')
                        ->required()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled($isCurator),

                    Select::make('day_of_week')
                        ->label('Hafta kuni')
                        ->options(collect(DayOfWeek::cases())->mapWithKeys(
                            fn (DayOfWeek $day) => [$day->value => $day->label()]
                        ))
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled($isCurator),

                    TextInput::make('sort_order')
                        ->label('Tartib raqami')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->disabled($isCurator),
                ]),

            Section::make('Vazifa ma\'lumotlari')
                ->columnSpan(1)
                ->schema([
                    TextInput::make('title')
                        ->label('Vazifa nomi')
                        ->required()
                        ->maxLength(255)
                        ->disabled($isCurator),

                    Textarea::make('description')
                        ->label('Tavsif')
                        ->rows(3)
                        ->maxLength(2000)
                        ->disabled($isCurator),
                ]),
        ])->columns(2);
    }
}
