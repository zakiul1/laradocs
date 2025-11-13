<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',             // eg: 'Customer Bank' | 'Shipper Bank'
        'email',
        'address',
        'phone',
        'country',
        'note',
        'created_by',

        // NEW FIELDS
        'swift_code',
        'bank_account',
        'company_id',
        'company_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User who created the bank record.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Related company (Customer or Shipper), using polymorphic relation.
     *
     * banks.company_type = App\Models\Customer or App\Models\Shipper
     * banks.company_id   = that model's id
     */
    public function company(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Helper accessor for a cleaner label if you ever need it.
     */
    public function getTypeLabelAttribute(): string
    {
        return (string) $this->type; // currently stores 'Customer Bank' / 'Shipper Bank'
    }
}