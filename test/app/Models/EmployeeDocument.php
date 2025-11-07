<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = ['employee_id', 'original_name', 'mime', 'size', 'path'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function url(): string
    {
        return asset('storage/' . $this->path);
    }
}