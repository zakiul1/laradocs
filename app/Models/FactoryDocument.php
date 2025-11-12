<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FactoryDocument extends Model
{
    protected $fillable = ['factory_id', 'path', 'name', 'mime', 'size'];

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}