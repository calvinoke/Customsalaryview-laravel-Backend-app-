<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'name',
        'email',
        'salary_local',
        'salary_euros',
        'commission',
    ];

    protected $casts = [
        'salary_local' => 'float',
        'salary_euros' => 'float',
        'commission' => 'float',
    ];

    // Append displayed_salary to the model's JSON form
    protected $appends = ['displayed_salary'];

    // Accessor to get the calculated displayed salary (euros + commission)
    public function getDisplayedSalaryAttribute()
    {
        return ($this->salary_euros ?? 0) + ($this->commission ?? 0);
    }
}
