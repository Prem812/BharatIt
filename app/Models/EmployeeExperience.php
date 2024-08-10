<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employer',
        'from',
        'to',
        'employer_email',
        'employee_id_at_employer',
        'job_role',
        'job_location',
    ];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}