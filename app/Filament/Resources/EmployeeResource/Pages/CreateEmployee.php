<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function afterCreate(): void
    {
        $employee = $this->record;

        User::create([
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'email' => $employee->email,
            'password' => Hash::make($employee->phone),
        ]);
    }
}
