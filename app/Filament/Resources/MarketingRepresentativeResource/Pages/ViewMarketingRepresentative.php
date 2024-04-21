<?php

namespace App\Filament\Resources\MarketingRepresentativeResource\Pages;

use App\Filament\Resources\MarketingRepresentativeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMarketingRepresentative extends ViewRecord
{
    protected static string $resource = MarketingRepresentativeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
