<?php

namespace App\Filament\Resources\WeekResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WeeksTable
{
    private static array $months = [
        1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart',
        4 => 'Aprel', 5 => 'May', 6 => 'Iyun',
        7 => 'Iyul', 8 => 'Avgust', 9 => 'Sentabr',
        10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr',
    ];

    public static function configure(Table $table): Table
    {
        $isCurator = auth()->user()?->isCurator();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('year')
                    ->label('Yil')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('month')
                    ->label('Oy')
                    ->formatStateUsing(fn (int $state): string => self::$months[$state] ?? $state)
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('week_number')
                    ->label('Hafta')
                    ->formatStateUsing(fn (int $state): string => "{$state}-hafta")
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label('Sarlavha')
                    ->searchable()
                    ->limit(30)
                    ->alignCenter(),

                TextColumn::make('tasks_count')
                    ->label('Vazifalar')
                    ->counts('tasks')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('year')
                    ->label('Yil')
                    ->options(fn () => collect(range(now()->year - 2, now()->year + 1))
                        ->mapWithKeys(fn ($y) => [$y => $y])
                        ->toArray())
                    ->placeholder('Barchasi')
                    ->native(false),

                SelectFilter::make('month')
                    ->label('Oy')
                    ->options(self::$months)
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
