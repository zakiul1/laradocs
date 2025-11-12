<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleInvoiceItem extends Model
{
    protected $fillable = [
        'sample_invoice_id','art_num','article_description','size','hs_code',
        'qty','unit_price','sub_total','sort'
    ];

    protected static function booted(): void
    {
        static::saving(function (SampleInvoiceItem $item) {
            $item->sub_total = round(((float)$item->qty) * ((float)$item->unit_price), 2);
        });

        static::saved(function (SampleInvoiceItem $item) {
            optional($item->invoice)->recalcTotals();
            optional($item->invoice)->save();
        });

        static::deleted(function (SampleInvoiceItem $item) {
            optional($item->invoice)->recalcTotals();
            optional($item->invoice)->save();
        });
    }

    public function invoice() { return $this->belongsTo(SampleInvoice::class,'sample_invoice_id'); }
}
