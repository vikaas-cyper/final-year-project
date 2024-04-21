<?php

namespace App\Filament\Resources\StockistExpensesResource\Pages;

use App\Filament\Resources\StockistExpensesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockistExpenses extends EditRecord
{
    protected static string $resource = StockistExpensesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
