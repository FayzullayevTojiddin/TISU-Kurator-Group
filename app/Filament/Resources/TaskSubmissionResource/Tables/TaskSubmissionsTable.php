<?php

namespace App\Filament\Resources\TaskSubmissionResource\Tables;

use App\Enums\TaskStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('task.title')
                    ->label('Vazifa')
                    ->limit(25)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('group.name')
                    ->label('Guruh')
                    ->limit(20)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Holat')
                    ->formatStateUsing(fn (TaskStatus $state): string => $state->label())
                    ->badge()
                    ->color(fn (TaskStatus $state): string => $state->color()),

                TextColumn::make('reviewer.name')
                    ->label('Tekshiruvchi')
                    ->limit(20)
                    ->placeholder('—'),

                TextColumn::make('submitted_at')
                    ->label('Topshirilgan')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('reviewed_at')
                    ->label('Tekshirilgan')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Holat')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(
                        fn (TaskStatus $s) => [$s->value => $s->label()]
                    ))
                    ->placeholder('Barchasi')
                    ->native(false),

                SelectFilter::make('group_id')
                    ->label('Guruh')
                    ->relationship('group', 'name')
                    ->placeholder('Barchasi')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
