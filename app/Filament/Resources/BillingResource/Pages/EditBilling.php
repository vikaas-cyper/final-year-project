<?php

namespace App\Filament\Resources\BillingResource\Pages;

use App\Filament\Resources\BillingResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBilling extends EditRecord
{
    protected static string $resource = BillingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
