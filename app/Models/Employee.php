<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'country_id', 'state_id', 'city_id',
        'address', 'zip_code', 'department_id', 'employment_type_id', 'current_package',
        'github', 'linkedin', 'facebook', 'twitter', 'instagram', 'photo', 'cv',
        'skills', 'date_of_birth', 'date_of_hired', 'is_terminated', 'date_of_termination',
        'is_active',
    ];

    protected $casts = [
        'skills' => 'array',
        'date_of_birth' => 'date',
        'date_of_hired' => 'date',
        'date_of_termination' => 'date',
        'is_terminated' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(EmployeeQualification::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function leadProjects()
    {
        return $this->hasMany(Project::class, 'project_lead_id');
    }
}