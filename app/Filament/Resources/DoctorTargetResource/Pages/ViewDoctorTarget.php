<?php

namespace App\Filament\Resources\DoctorTargetResource\Pages;

use App\Filament\Resources\DoctorTargetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDoctorTarget extends ViewRecord
{
    protected static string $resource = DoctorTargetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
