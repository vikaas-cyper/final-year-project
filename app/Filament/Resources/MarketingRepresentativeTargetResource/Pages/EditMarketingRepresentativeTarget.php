<?php

namespace App\Filament\Resources\MarketingRepresentativeTargetResource\Pages;

use App\Filament\Resources\MarketingRepresentativeTargetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketingRepresentativeTarget extends EditRecord
{
    protected static string $resource = MarketingRepresentativeTargetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
