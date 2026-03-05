<?php

namespace App\Filament\Resources\TaskResource\Tables;

use App\Enums\DayOfWeek;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        $isCurator = auth()->user()?->isCurator();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('week.title')
                    ->label('Hafta')
                    ->limit(20)
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label('Vazifa')
                    ->searchable()
                    ->limit(35)
                    ->alignCenter(),

                TextColumn::make('day_of_week')
                    ->label('Kun')
                    ->formatStateUsing(fn (?DayOfWeek $state): string => $state?->label() ?? '—')
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Tartib')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('submissions_count')
                    ->label('Topshirishlar')
                    ->counts('submissions')
                    ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('week_id')
                    ->label('Hafta')
                    ->relationship('week', 'title')
                    ->placeholder('Barchasi')
                    ->native(false),

                SelectFilter::make('day_of_week')
                    ->label('Hafta kuni')
                    ->options(collect(DayOfWeek::cases())->mapWithKeys(
                        fn (DayOfWeek $day) => [$day->value => $day->label()]
                    ))
                    ->placeholder('Barchasi')
                    ->native(false),
            ])
            ->recordActions(
                $isCurator
                    ? [EditAction::make()->iconButton()]
                    : [EditAction::make()->iconButton(), DeleteAction::make()->iconButton()]
            )
            ->bulkActions(
                $isCurator ? [] : [DeleteBulkAction::make()]
            );
    }
}
