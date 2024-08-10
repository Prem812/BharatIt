<?php

namespace App\Filament\Resources\EmploymentTypeResource\Pages;

use App\Filament\Resources\EmploymentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmploymentType extends EditRecord
{
    protected static string $resource = EmploymentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
