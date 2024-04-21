<?php

namespace App\Filament\Resources\ProductBuyRateResource\Pages;

use App\Filament\Resources\ProductBuyRateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductBuyRates extends ListRecords
{
    protected static string $resource = ProductBuyRateResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
