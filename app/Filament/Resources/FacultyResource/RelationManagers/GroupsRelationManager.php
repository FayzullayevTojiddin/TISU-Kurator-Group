<?php

namespace App\Filament\Resources\FacultyResource\RelationManagers;

use App\Filament\Resources\GroupResource;
use App\Models\Group;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'Guruhlar';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('curator.name')
                    ->label('Kurator')
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Faol')
                    ->alignCenter(),
            ])
            ->defaultSort('name')
            ->recordUrl(fn (Group $record) => GroupResource::getUrl('edit', ['record' => $record]));
    }
}
