<?php

namespace App\Filament\Resources\DoctorTargetResource\Pages;

use App\Filament\Resources\DoctorTargetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorTarget extends EditRecord
{
    protected static string $resource = DoctorTargetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
