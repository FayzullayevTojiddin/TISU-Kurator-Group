<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeekResource\Pages;
use App\Filament\Resources\WeekResource\RelationManagers\TasksRelationManager;
use App\Filament\Resources\WeekResource\Schemas\WeekForm;
use App\Filament\Resources\WeekResource\Tables\WeeksTable;
use App\Models\Week;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WeekResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected static ?string $model = Week::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Hafta';

    protected static ?string $pluralModelLabel = 'Haftalar';

    protected static string | \UnitEnum | null $navigationGroup = 'Ta\'lim';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return WeekForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeeksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeeks::route('/'),
            'create' => Pages\CreateWeek::route('/create'),
            'edit' => Pages\EditWeek::route('/{record}/edit'),
        ];
    }
}
