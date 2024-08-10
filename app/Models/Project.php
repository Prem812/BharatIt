<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'client_name', 'client_email',
        'project_manager_id', 'project_lead_id', 'technologies'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'technologies' => 'array',
    ];

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function projectLead(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_lead_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ProjectStatus::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }
}