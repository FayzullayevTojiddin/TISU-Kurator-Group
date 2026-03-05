<?php

namespace App\Filament\Resources\UserResource\Tables;

use App\Enums\UserRole;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('name')
                    ->label('Ism')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->alignCenter(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(30)
                    ->alignCenter(),

                TextColumn::make('role')
                    ->label('Rol')
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->badge()
                    ->color(fn (UserRole $state): string => match ($state) {
                        UserRole::SuperAdmin => 'danger',
                        UserRole::Dean => 'warning',
                        UserRole::Curator => 'info',
                    })
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Faol')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options(collect(UserRole::cases())->mapWithKeys(
                        fn (UserRole $role) => [$role->value => $role->label()]
                    ))
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
