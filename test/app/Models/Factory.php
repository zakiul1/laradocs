<?php

namespace App\Models;

use App\Models\Concerns\Categorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factory extends Model
{
    use HasFactory, Categorizable;

    /**
     * Mass-assignable fields
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'lines',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'lines' => 'integer',
    ];

    /**
     * Relationships
     */
    public function photos(): HasMany
    {
        return $this->hasMany(FactoryPhoto::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FactoryDocument::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Helpers
     */
    public function primaryPhotoUrl(): ?string
    {
        $p = $this->photos()->latest('id')->first();
        return $p?->url();
    }
}