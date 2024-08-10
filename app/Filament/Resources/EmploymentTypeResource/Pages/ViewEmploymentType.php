<?php

namespace App\Filament\Resources\EmploymentTypeResource\Pages;

use App\Filament\Resources\EmploymentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmploymentType extends ViewRecord
{
    protected static string $resource = EmploymentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
