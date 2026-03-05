<?php

namespace App\Filament\Resources\GroupResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GroupsTable
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

                TextColumn::make('name')
                    ->label('Guruh nomi')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->alignCenter(),

                TextColumn::make('faculty.name')
                    ->label('Fakultet')
                    ->sortable()
                    ->limit(25)
                    ->alignCenter(),

                TextColumn::make('curator.name')
                    ->label('Kurator')
                    ->limit(25)
                    ->placeholder('—')
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Faol')
                    ->alignCenter()
                    ->disabled($isCurator),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('faculty_id')
                    ->label('Fakultet')
                    ->relationship('faculty', 'name')
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
