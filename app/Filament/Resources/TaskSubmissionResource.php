<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskSubmissionResource\Pages;
use App\Filament\Resources\TaskSubmissionResource\Schemas\TaskSubmissionForm;
use App\Filament\Resources\TaskSubmissionResource\Tables\TaskSubmissionsTable;
use App\Models\TaskSubmission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskSubmissionResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return ! auth()->user()?->isCurator();
    }

    protected static ?string $model = TaskSubmission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $modelLabel = 'Topshirish';

    protected static ?string $pluralModelLabel = 'Topshirishlar';

    protected static string | \UnitEnum | null $navigationGroup = 'Ta\'lim';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return TaskSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskSubmissionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isDean()) {
            return $query->whereHas('group', fn (Builder $q) => $q->where('faculty_id', $user->faculty_id));
        }

        if ($user->isCurator()) {
            return $query->whereHas('group', fn (Builder $q) => $q->where('curator_id', $user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskSubmissions::route('/'),
            'create' => Pages\CreateTaskSubmission::route('/create'),
            'edit' => Pages\EditTaskSubmission::route('/{record}/edit'),
        ];
    }
}
