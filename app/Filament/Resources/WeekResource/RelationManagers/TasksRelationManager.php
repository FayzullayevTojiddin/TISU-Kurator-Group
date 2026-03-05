<?php

namespace App\Filament\Resources\WeekResource\RelationManagers;

use App\Enums\DayOfWeek;
use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Vazifalar';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Sarlavha')
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('description')
                    ->label('Tavsif')
                    ->limit(50)
                    ->alignCenter(),

                TextColumn::make('day_of_week')
                    ->label('Kun')
                    ->formatStateUsing(fn (DayOfWeek $state) => $state->label())
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Tartib')
                    ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->recordUrl(fn (Task $record) => TaskResource::getUrl('edit', ['record' => $record]));
    }
}
