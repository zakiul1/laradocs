<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactorySubcategory extends Model
{
    protected $fillable = ['name', 'factory_category_id', 'position', 'created_by', 'updated_by'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FactoryCategory::class, 'factory_category_id');
    }

    public static function forSelectByCategory(?int $categoryId): array
    {
        if (!$categoryId)
            return [];
        return static::where('factory_category_id', $categoryId)
            ->orderBy('position')->orderBy('name')
            ->pluck('name', 'id')->toArray();
    }
}