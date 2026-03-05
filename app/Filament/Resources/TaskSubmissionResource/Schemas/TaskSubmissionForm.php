<?php

namespace App\Filament\Resources\TaskSubmissionResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCurator = auth()->user()?->isCurator();

        return $schema->components([
            Section::make('Topshirish ma\'lumotlari')
                ->columnSpan(1)
                ->schema([
                    Select::make('task_id')
                        ->label('Vazifa')
                        ->relationship('task', 'title')
                        ->required()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled(fn ($operation) => $operation === 'edit'),

                    Select::make('group_id')
                        ->label('Guruh')
                        ->relationship('group', 'name')
                        ->required()
                        ->native(false)
                        ->placeholder('Tanlang')
                        ->disabled(fn ($operation) => $operation === 'edit'),

                    Textarea::make('description')
                        ->label('Tavsif')
                        ->rows(3)
                        ->maxLength(2000)
                        ->disabled(! $isCurator),

                    FileUpload::make('files')
                        ->label('Fayllar')
                        ->multiple()
                        ->directory('submissions')
                        ->disabled(! $isCurator),
                ]),

            Section::make('Tekshiruv va izoh')
                ->columnSpan(1)
                ->schema([
                    Textarea::make('notes')
                        ->label('Izoh')
                        ->rows(3)
                        ->maxLength(2000)
                        ->disabled(! $isCurator),

                    DateTimePicker::make('submitted_at')
                        ->label('Topshirilgan vaqt')
                        ->native(false)
                        ->disabled()
                        ->visible(! $isCurator),

                    DateTimePicker::make('reviewed_at')
                        ->label('Tekshirilgan vaqt')
                        ->native(false)
                        ->disabled()
                        ->visible(! $isCurator),

                    Placeholder::make('reviewer_name')
                        ->label('Tekshiruvchi')
                        ->content(fn ($record) => $record?->reviewer?->name ?? '—')
                        ->visible(! $isCurator),
                ]),
        ])->columns(2);
    }
}
