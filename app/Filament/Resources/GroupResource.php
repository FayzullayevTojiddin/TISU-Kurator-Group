<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\SubmissionsRelationManager;
use App\Filament\Resources\GroupResource\Schemas\GroupForm;
use App\Filament\Resources\GroupResource\Tables\GroupsTable;
use App\Models\Group;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GroupResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isCurator();
    }

    public static function canCreate(): bool
    {
        return ! auth()->user()?->isCurator();
    }

    protected static ?string $model = Group::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Guruh';

    protected static ?string $pluralModelLabel = 'Guruhlar';

    protected static string | \UnitEnum | null $navigationGroup = 'Ta\'lim';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return GroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GroupsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return $query->visibleTo($user);
    }

    public static function getRelations(): array
    {
        return [
            SubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
