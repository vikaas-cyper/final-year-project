<?php

namespace App\Filament\Resources\ProductBuyRateResource\Pages;

use App\Filament\Resources\ProductBuyRateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductBuyRate extends ViewRecord
{
    protected static string $resource = ProductBuyRateResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
