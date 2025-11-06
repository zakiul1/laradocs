<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'factory_id',
        'path',
        'original_name',
        'size',
    ];

    /**
     * Relationships
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    /**
     * URL helper (uses public disk)
     */
    public function url(): string
    {
        return asset('storage/' . ltrim($this->path, '/'));
    }

    /**
     * Human-readable size (optional helper)
     */
    public function sizeForHumans(): string
    {
        $bytes = (int) $this->size;
        if ($bytes < 1024)
            return $bytes . ' B';
        if ($bytes < 1048576)
            return number_format($bytes / 1024, 1) . ' KB';
        return number_format($bytes / 1048576, 1) . ' MB';
    }
}