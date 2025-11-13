<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (CompanyCategory $category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
            if (auth()->check() && !$category->created_by) {
                $category->created_by = auth()->id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}