<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'designation',
        'join_date',
        'leave_date',
        'photo',
        'documents',
        'address',
        'alternative_contact_number',
        'created_by',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'gender',
        'blood_group',
        'status',
        'notes',
    ];

    protected $casts = [
        'join_date' => 'date',
        'leave_date' => 'date',
        'documents' => 'array',
    ];

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper: computed URLs for photo & docs (public disk)
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->photo ? asset('storage/' . $this->photo) : null
        );
    }

    public function documentUrls(): array
    {
        return collect($this->documents ?? [])
            ->map(fn($path) => asset('storage/' . $path))
            ->all();
    }
}