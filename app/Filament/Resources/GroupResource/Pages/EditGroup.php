<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use App\Filament\Resources\GroupResource\Widgets\TaskStatsWidget;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        $isCurator = auth()->user()?->isCurator();

        return [
            DeleteAction::make()
                ->visible(! $isCurator),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TaskStatsWidget::class,
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
