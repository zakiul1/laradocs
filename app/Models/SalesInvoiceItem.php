<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    protected $fillable = [
        'sales_invoice_id','art_num','article_description','size','hs_code',
        'qty','unit_price','sub_total','sort'
    ];

    protected static function booted(): void
    {
        static::saving(function (SalesInvoiceItem $item) {
            $qty = (float) $item->qty;
            $price = (float) $item->unit_price;
            $item->sub_total = round($qty * $price, 2);
        });

        static::saved(function (SalesInvoiceItem $item) {
            optional($item->invoice)->recalcTotals();
            optional($item->invoice)->save();
        });

        static::deleted(function (SalesInvoiceItem $item) {
            optional($item->invoice)->recalcTotals();
            optional($item->invoice)->save();
        });
    }

    public function invoice() { return $this->belongsTo(SalesInvoice::class,'sales_invoice_id'); }
}
