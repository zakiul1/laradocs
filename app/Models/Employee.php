<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'designation',
        'address',
        'join_date',
        'leave_date',
        'photo_path',
    ];

    protected $casts = [
        'join_date' => 'date',
        'leave_date' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }
}