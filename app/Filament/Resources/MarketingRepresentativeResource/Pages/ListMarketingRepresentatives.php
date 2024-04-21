<?php

namespace App\Filament\Resources\MarketingRepresentativeResource\Pages;

use App\Filament\Resources\MarketingRepresentativeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketingRepresentatives extends ListRecords
{
    protected static string $resource = MarketingRepresentativeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
