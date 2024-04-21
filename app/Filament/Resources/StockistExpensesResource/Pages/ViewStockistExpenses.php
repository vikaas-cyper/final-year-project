<?php

namespace App\Filament\Resources\StockistExpensesResource\Pages;

use App\Filament\Resources\StockistExpensesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStockistExpenses extends ViewRecord
{
    protected static string $resource = StockistExpensesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
