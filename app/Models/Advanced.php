<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advanced extends Model
{
    use HasFactory;

    protected $table = 'advances';

    protected $fillable = [
        'invoice_id',
        'invoice_customer_id',
        'advance_amount',
        'due_balance',
        'date'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(InvoiceCustomer::class, 'invoice_customer_id');
    }
}