<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskSubmissionResource;
use App\Models\TaskSubmission;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Topshiriqlar';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if ($user?->isCurator()) {
                    $groupIds = $user->curatedGroups()->pluck('id');
                    $query->whereIn('group_id', $groupIds);
                }
            })
            ->columns([
                TextColumn::make('group.name')
                    ->label('Guruh')
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('group.curator.name')
                    ->label('Kurator')
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Holat')
                    ->formatStateUsing(fn (TaskStatus $state) => $state->label())
                    ->badge()
                    ->color(fn (TaskStatus $state) => $state->color())
                    ->alignCenter(),

                TextColumn::make('submitted_at')
                    ->label('Topshirilgan sana')
                    ->dateTime('d.m.Y H:i')
                    ->alignCenter(),

                TextColumn::make('reviewer.name')
                    ->label('Tekshiruvchi')
                    ->alignCenter(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->recordUrl(fn (TaskSubmission $record) => TaskSubmissionResource::getUrl('edit', ['record' => $record]));
    }
}
