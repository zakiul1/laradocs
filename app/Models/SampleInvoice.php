<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleInvoice extends Model
{
    protected $fillable = [
        'invoice_no','shipper_id','customer_id','buyer_account','shipment_terms',
        'courier_name','tracking_number','currency_id','items_total','grand_total',
        'footer_note','created_by'
    ];

    protected $casts = [
        'items_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items() { return $this->hasMany(SampleInvoiceItem::class); }
    public function shipper() { return $this->belongsTo(Shipper::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function currency() { return $this->belongsTo(Currency::class); }

    public function recalcTotals(): void
    {
        $sum = $this->items()->sum('sub_total') ?: 0;
        $this->items_total = $sum;
        $this->grand_total = $sum;
    }
}
