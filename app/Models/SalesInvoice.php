<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    protected $fillable = [
        'shipper_id',
        'customer_id',
        'bank_id',
        'currency_id',
        'created_by',
        'invoice_no',
        'invoice_type',
        'issue_date',
        'delivery_date',
        'payment_mode',
        'terms_of_shipment',
        'currency_code',
        'shipper_name',
        'shipper_address',
        'customer_company_name',
        'customer_address_block',
        'attention_name',
        'our_bank_block',
        'total_qty',
        'items_total',
        'commercial_cost',
        'siatex_discount',
        'grand_total',
        'terms_and_conditions',
        'message_type',
        'message_body',
        'footer_note',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'delivery_date' => 'date',
        'items_total' => 'decimal:2',
        'commercial_cost' => 'decimal:2',
        'siatex_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    // Relations
    public function items(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'shipper_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}