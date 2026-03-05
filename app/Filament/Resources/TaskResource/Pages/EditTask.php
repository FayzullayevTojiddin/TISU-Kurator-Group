<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        $isCurator = auth()->user()?->isCurator();

        return [
            DeleteAction::make()
                ->visible(! $isCurator),
        ];
    }

    protected function getFormActions(): array
    {
        if (auth()->user()?->isCurator()) {
            return [];
        }

        return parent::getFormActions();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
