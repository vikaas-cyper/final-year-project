<?php

namespace App\Filament\Resources\ProductBuyRateResource\Pages;

use App\Filament\Resources\ProductBuyRateResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductBuyRate extends EditRecord
{
    protected static string $resource = ProductBuyRateResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
