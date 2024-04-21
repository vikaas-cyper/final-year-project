<?php

namespace App\Filament\Resources\DoctorTargetResource\Pages;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\DoctorTargetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctorTargets extends ListRecords
{
    protected static string $resource = DoctorTargetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->leftJoin('doctor_masters', 'doctor_targets.doctor_master_id' , '=' , 'doctor_masters.id')->leftJoin('billings',"billings.id","=", "doctor_masters.billing_id")->select('doctor_targets.*' , 'billing_name', 'doctor_name')->withoutGlobalScopes();
    
    }
    
}
