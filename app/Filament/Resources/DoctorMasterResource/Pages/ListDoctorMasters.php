<?php

namespace App\Filament\Resources\DoctorMasterResource\Pages;

use App\Filament\Resources\DoctorMasterResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDoctorMasters extends ListRecords
{
    protected static string $resource = DoctorMasterResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->leftJoin('marketing_representatives', 'doctor_masters.marketing_representative_id', '=', 'marketing_representatives.id')
            ->select('doctor_masters.*', 'marketing_representatives.name as marketing_representative');
    }
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
