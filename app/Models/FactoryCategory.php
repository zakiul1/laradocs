<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FactoryCategory extends Model
{
    /**
     * Mass-assignable fields
     */
    protected $fillable = [
        'name',
        'position',
        'factory_category_id', // <- parent id (null = parent category)
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'position' => 'integer',
        'factory_category_id' => 'integer',
    ];

    /* -----------------------------------------------------------------
     |  Relationships (single-table taxonomy)
     | ----------------------------------------------------------------- */

    /**
     * Parent category (null means this row is a parent/top-level).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FactoryCategory::class, 'factory_category_id');
        // ->withDefault(); // uncomment if you want a default empty parent object
    }

    /**
     * Children (subcategories).
     */
    public function children(): HasMany
    {
        return $this->hasMany(FactoryCategory::class, 'factory_category_id')
            ->orderBy('position')
            ->orderBy('name');
    }

    /**
     * Backwards-compatible alias for old code that called $category->subcategories.
     */
    public function subcategories(): HasMany
    {
        return $this->children();
    }

    /* -----------------------------------------------------------------
     |  Scopes
     | ----------------------------------------------------------------- */

    /**
     * Only top-level (parent) categories.
     */
    public function scopeParents($query)
    {
        return $query->whereNull('factory_category_id');
    }

    /**
     * Only children of a given parent id.
     */
    public function scopeChildrenOf($query, ?int $parentId)
    {
        return $query->where('factory_category_id', $parentId);
    }

    /* -----------------------------------------------------------------
     |  Helpers for selects (used in forms/filters)
     | ----------------------------------------------------------------- */

    /**
     * All categories (parents & children) for a flat select.
     * Keeps previous signature used in blades.
     */
    public static function forSelect(): array
    {
        return static::orderBy('position')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Only parents for a "Parent" dropdown.
     */
    public static function forSelectParents(): array
    {
        return static::parents()
            ->orderBy('position')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Children for a given parent (used by dependent subcategory dropdowns).
     */
    public static function forSelectByParent(?int $parentId): array
    {
        if (!$parentId) {
            return [];
        }

        return static::childrenOf($parentId)
            ->orderBy('position')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}