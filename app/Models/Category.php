<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['scope', 'name', 'slug', 'parent_id', 'position'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    // scopes
    public function scopeScope(Builder $q, string $scope): Builder
    {
        return $q->where('scope', $scope);
    }

    public function scopeRoots(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
    }

    // helper: flatten tree for selects
    public static function flatForSelect(string $scope): array
    {
        $build = function ($nodes, $prefix = '') use (&$build) {
            $out = [];
            foreach ($nodes as $n) {
                $out[$n->id] = ($prefix ? $prefix . ' ' : '') . $n->name;
                if ($n->children->isNotEmpty()) {
                    $out += $build($n->children, $prefix . '—');
                }
            }
            return $out;
        };

        $roots = static::scope($scope)->roots()->with('children.children.children')->orderBy('position')->get();
        return $build($roots);
    }
}