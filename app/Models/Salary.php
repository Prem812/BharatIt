<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'base_salary',
        'upi_id',
        'advance_payments',
        'attendance_ratio',
        'paid_amount',
        'payable_amount',
    ];

    protected $casts = [
        'month' => 'date',
        'advance_payments' => 'array',
        'payable_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
