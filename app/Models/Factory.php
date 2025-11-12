<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Factory extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'registration_no',
        'total_employees',
        'lines',
        'notes',
        'category_id',
        'subcategory_id',
        'created_by',
        'updated_by',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FactoryCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(FactorySubcategory::class, 'subcategory_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(FactoryPhoto::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FactoryDocument::class);
    }
}