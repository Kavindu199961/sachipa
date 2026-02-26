<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'location',
        'invoice_id',
        'advanced_id'
    ];

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function advances()
    {
        return $this->hasMany(Advanced::class);
    }

    public function latestInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function latestAdvanced()
    {
        return $this->belongsTo(Advanced::class, 'advanced_id');
    }
}