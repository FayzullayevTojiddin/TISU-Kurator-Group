<?php

namespace App\Filament\Resources\TaskSubmissionResource\Pages;

use App\Filament\Resources\TaskSubmissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaskSubmissions extends ListRecords
{
    protected static string $resource = TaskSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Topshirish qo\'shish'),
        ];
    }
}
