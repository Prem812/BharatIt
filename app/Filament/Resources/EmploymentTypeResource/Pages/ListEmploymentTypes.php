<?php

namespace App\Filament\Resources\EmploymentTypeResource\Pages;

use App\Filament\Resources\EmploymentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmploymentTypes extends ListRecords
{
    protected static string $resource = EmploymentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
