<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = ['scope', 'name', 'slug', 'parent_id', 'position'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Scopes
    public function scopeScope($q, string $scope)
    {
        return $q->where('scope', $scope);
    }
    public function scopeRoots($q)
    {
        return $q->whereNull('parent_id');
    }
}