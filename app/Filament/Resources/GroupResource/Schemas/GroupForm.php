<?php

namespace App\Filament\Resources\GroupResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCurator = auth()->user()?->isCurator();

        return $schema->components([
            Section::make('Guruh ma\'lumotlari')
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label('Guruh nomi')
                        ->required()
                        ->maxLength(255)
                        ->disabled($isCurator),

                    Select::make('faculty_id')
                        ->label('Fakultet')
                        ->relationship('faculty', 'name')
                        ->required()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled($isCurator),

                    Select::make('curator_id')
                        ->label('Kurator')
                        ->relationship('curator', 'name')
                        ->searchable()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled($isCurator),
                ]),
        ]);
    }
}
