<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'college',
        'course',
        'branch',
        'from_year',
        'passing_year',
        'percentage',
        'certificate',
    ];

    protected $casts = [
        'from_year' => 'integer',
        'passing_year' => 'integer',
        'percentage' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}