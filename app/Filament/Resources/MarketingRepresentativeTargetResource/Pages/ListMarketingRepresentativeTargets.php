<?php

namespace App\Filament\Resources\MarketingRepresentativeTargetResource\Pages;

use App\Filament\Resources\MarketingRepresentativeTargetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketingRepresentativeTargets extends ListRecords
{
    protected static string $resource = MarketingRepresentativeTargetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
