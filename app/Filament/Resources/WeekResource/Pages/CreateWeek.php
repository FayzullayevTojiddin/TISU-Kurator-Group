<?php

namespace App\Filament\Resources\WeekResource\Pages;

use App\Filament\Resources\WeekResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateWeek extends CreateRecord
{
    protected static string $resource = WeekResource::class;

    protected Width | string | null $maxContentWidth = Width::SevenExtraLarge;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
