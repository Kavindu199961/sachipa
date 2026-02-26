<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_customer_id',
        'item_name',
        'rate',
        'qty',
        'amount',
        'item_discount',
        'final_amount',
        'final_amount_discount',
        'total_amount'

    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-calculate amount before saving
        static::creating(function ($invoice) {
            $invoice->amount = $invoice->rate * $invoice->qty;
        });

        static::updating(function ($invoice) {
            $invoice->amount = $invoice->rate * $invoice->qty;
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(InvoiceCustomer::class, 'invoice_customer_id');
    }

    public function advances()
    {
        return $this->hasMany(Advanced::class);
    }
}