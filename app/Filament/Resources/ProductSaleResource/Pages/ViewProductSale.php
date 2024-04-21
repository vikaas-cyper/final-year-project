<?php

namespace App\Filament\Resources\ProductSaleResource\Pages;

use App\Filament\Resources\ProductSaleResource;
use App\Models\ProductSale;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductSale extends ViewRecord
{
    protected static string $resource = ProductSaleResource::class;

    public bool $action_diable = false;
    protected function mutateFormDataBeforeFill(array $data): array
    {

        $workflow_status = $data['workflow_status']['status']['name'];


        if (strtolower($workflow_status) == strtolower('approved')) {
            $this->action_diable = true;
        }
        return $data;
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()->hidden($this->action_diable),

            Actions\Action::make('back')
                            ->label('Back')
                            ->url(static::$resource::getUrl('index'))->color('secondary'),

        ];
    }
}
