<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\SubmissionsRelationManager;
use App\Filament\Resources\TaskResource\Schemas\TaskForm;
use App\Filament\Resources\TaskResource\Tables\TasksTable;
use App\Models\Task;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isCurator();
    }

    public static function canCreate(): bool
    {
        return ! auth()->user()?->isCurator();
    }

    protected static ?string $model = Task::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Vazifa';

    protected static ?string $pluralModelLabel = 'Vazifalar';

    protected static string | \UnitEnum | null $navigationGroup = 'Ta\'lim';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
