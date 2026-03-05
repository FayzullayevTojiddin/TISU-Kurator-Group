<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskSubmissionResource;
use App\Models\TaskSubmission;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Topshiriqlar';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('task.week.title')
                    ->label('Hafta')
                    ->alignCenter(),

                TextColumn::make('task.title')
                    ->label('Vazifa')
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('task.day_of_week')
                    ->label('Kun')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Holat')
                    ->formatStateUsing(fn (TaskStatus $state) => $state->label())
                    ->badge()
                    ->color(fn (TaskStatus $state) => $state->color())
                    ->alignCenter(),

                TextColumn::make('submitted_at')
                    ->label('Topshirilgan sana')
                    ->dateTime('d.m.Y H:i')
                    ->alignCenter(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->recordUrl(fn (TaskSubmission $record) => TaskSubmissionResource::getUrl('edit', ['record' => $record]));
    }
}
