<?php

namespace App\Filament\Resources\FacultyResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Fakultet ma\'lumotlari')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label('Nomi')
                        ->required()
                        ->maxLength(255),

                    Select::make('dean_id')
                        ->label('Dekan')
                        ->relationship(
                            'dean',
                            'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('role', 'dean'),
                        )
                        ->nullable()
                        ->native(false)
                        ->placeholder('Tanlang'),
                ]),
        ]);
    }
}
