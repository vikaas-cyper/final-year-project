<?php

namespace App\Filament\Resources\FreeUnitResource\Pages;

use App\Filament\Resources\FreeUnitResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFreeUnits extends ListRecords
{
    protected static string $resource = FreeUnitResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import')
                ->url(static::$resource::getUrl('import'))
                ->color('primary')
                ->outlined(),
        ];
    }
}
