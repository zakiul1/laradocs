<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'shipper_id',
        'customer_id',
        'courier_company_id',
        'shipper_name',
        'shipper_address',
        'customer_company_name',
        'customer_address_block',
        'attention_name',
        'buyer_account',
        'shipment_terms',
        'courier_name',
        'tracking_number',
        'footer_note',
        'total_amount',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SampleInvoiceItem::class);
    }

    public function shipper()
    {
        return $this->belongsTo(Company::class, 'shipper_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Courier is also a company
    public function courierCompany()
    {
        return $this->belongsTo(Company::class, 'courier_company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}