<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    protected $fillable = [
        'invoice_no','type','shipper_id','customer_id',
        'issue_date','delivery_date','payment_mode','terms_of_shipment',
        'currency_id','items_total','commercial_cost','siatex_discount','grand_total',
        'terms_and_conditions','created_by'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'delivery_date' => 'date',
        'items_total' => 'decimal:2',
        'commercial_cost' => 'decimal:2',
        'siatex_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items(): HasMany { return $this->hasMany(SalesInvoiceItem::class); }
    public function shipper() { return $this->belongsTo(Shipper::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function currency() { return $this->belongsTo(Currency::class); }

    public function recalcTotals(): void
    {
        $itemsTotal = $this->items()->sum('sub_total') ?: 0;
        $this->items_total = $itemsTotal;
        $this->grand_total = max(0, $itemsTotal + $this->commercial_cost - $this->siatex_discount);
    }
}
