<?php

namespace App\Filament\Resources\TaskSubmissionResource\Pages;

use App\Filament\Resources\TaskSubmissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskSubmission extends CreateRecord
{
    protected static string $resource = TaskSubmissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
