<?php

namespace App\Filament\Resources\SalaryResource\Pages;

use App\Filament\Resources\SalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalary extends EditRecord
{
    protected static string $resource = SalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['attendance_ratio'] = $this->data['attendance_ratio'];
        $data['paid_amount'] = $this->data['paid_amount'];
        $data['payable_amount'] = $this->data['payable_amount'];

        return $data;
    }
}
