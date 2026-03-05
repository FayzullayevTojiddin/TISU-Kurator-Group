<?php

namespace App\Filament\Resources\WeekResource\Pages;

use App\Filament\Resources\WeekResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditWeek extends EditRecord
{
    protected static string $resource = WeekResource::class;

    protected Width | string | null $maxContentWidth = Width::SevenExtraLarge;

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
