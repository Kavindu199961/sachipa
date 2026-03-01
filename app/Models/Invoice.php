<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'date',
        'invoice_customer_id',
        'item_name',
        'rate',
        'qty',
        'item_discount',
        'amount',
        'final_amount'
    ];

    protected $casts = [
        'invoice_number' => 'string',
        'rate'           => 'decimal:2',
        'qty'            => 'integer',
        'item_discount'  => 'decimal:2',
        'amount'         => 'decimal:2',
        'final_amount'   => 'decimal:2',
        'date'           => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime'
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the customer that owns this invoice.
     */
    public function customer()
    {
        return $this->belongsTo(InvoiceCustomer::class, 'invoice_customer_id');
    }

    /**
     * Get the advance payments for this invoice.
     */
    public function advances()
    {
        return $this->hasMany(Advanced::class, 'invoice_id');
    }

    /**
     * Calculate the subtotal before discount.
     */
    public function getSubtotalAttribute()
    {
        return $this->rate * $this->qty;
    }

    /**
     * Calculate the discount amount.
     */
    public function getDiscountAmountAttribute()
    {
        return $this->subtotal * ($this->item_discount / 100);
    }

    /**
     * Get the formatted rate.
     */
    public function getFormattedRateAttribute()
    {
        return 'LKR ' . number_format($this->rate, 2);
    }

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return 'LKR ' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted final amount.
     */
    public function getFormattedFinalAmountAttribute()
    {
        return 'LKR ' . number_format($this->final_amount, 2);
    }

    /**
     * Get the formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d M Y') : 'N/A';
    }

    /**
     * Generate the next sequential invoice number.
     * Stored as INV-000001, INV-000002, INV-000003 ...
     */
    public static function generateInvoiceNumber(): string
{
    $last = self::orderBy('id', 'desc')
                ->whereNotNull('invoice_number')
                ->first();

    if ($last && $last->invoice_number) {
        // Extract number from "INV-000001" → 1
        preg_match('/(\d+)$/', $last->invoice_number, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $next = $lastNumber + 1;
    } else {
        $next = 1;
    }

    return 'INV-' . str_pad($next, 6, '0', STR_PAD_LEFT);
}
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate amount before creating
        static::creating(function ($invoice) {
            $subTotal       = $invoice->rate * $invoice->qty;
            $discountAmount = $subTotal * ($invoice->item_discount / 100);
            $invoice->amount       = round($subTotal - $discountAmount, 2);
            $invoice->final_amount = $invoice->amount;

            // Set date if not provided
            if (empty($invoice->date)) {
                $invoice->date = now();
            }
            // NOTE: invoice_number is set explicitly in the controller
            // (one number shared across all line items of the same invoice)
        });

        // Auto-calculate amount before updating
        static::updating(function ($invoice) {
            $subTotal       = $invoice->rate * $invoice->qty;
            $discountAmount = $subTotal * ($invoice->item_discount / 100);
            $invoice->amount       = round($subTotal - $discountAmount, 2);
            $invoice->final_amount = $invoice->amount;
        });

        // After creating an invoice, update the customer's invoice_id
        static::created(function ($invoice) {
            $customer = $invoice->customer;
            if ($customer && !$customer->invoice_id) {
                $customer->update(['invoice_id' => $invoice->id]);
            }
        });
    }
}