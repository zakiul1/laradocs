<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    use HasFactory;

    /**
     * Allow mass-assignment for these attributes
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'country',
        'company_name',
        'website',
        'designation',
        'shipping_address',
        'photo',          // storage path
        'documents',      // json array of paths
        'whatsapp_number',
        'created_by',     // FK to users.id (nullable)
    ];

    /**
     * Casts
     */
    protected $casts = [
        'documents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Creator (optional)
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Accessor used by views: $customer->photo_url
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? Storage::disk('public')->url($this->photo) : null;
    }
}