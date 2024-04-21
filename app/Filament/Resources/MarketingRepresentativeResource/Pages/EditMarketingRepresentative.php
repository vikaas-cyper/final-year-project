<?php

namespace App\Filament\Resources\MarketingRepresentativeResource\Pages;

use App\Filament\Resources\MarketingRepresentativeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketingRepresentative extends EditRecord
{
    protected static string $resource = MarketingRepresentativeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
