<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacultyResource\Pages;
use App\Filament\Resources\FacultyResource\RelationManagers\GroupsRelationManager;
use App\Filament\Resources\FacultyResource\Schemas\FacultyForm;
use App\Filament\Resources\FacultyResource\Tables\FacultiesTable;
use App\Models\Faculty;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FacultyResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected static ?string $model = Faculty::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $modelLabel = 'Fakultet';

    protected static ?string $pluralModelLabel = 'Fakultetlar';

    protected static string | \UnitEnum | null $navigationGroup = 'Boshqaruv';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return FacultyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacultiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            GroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaculties::route('/'),
            'create' => Pages\CreateFaculty::route('/create'),
            'edit' => Pages\EditFaculty::route('/{record}/edit'),
        ];
    }
}
