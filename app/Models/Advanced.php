<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advanced extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'invoice_customer_id',
        'advance_amount',
        'due_balance',
        'date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'advance_amount' => 'decimal:2',
        'due_balance' => 'decimal:2',
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the invoice that this advance payment belongs to.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Get the customer that this advance payment belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(InvoiceCustomer::class, 'invoice_customer_id');
    }

    /**
     * Get the formatted advance amount.
     */
    public function getFormattedAdvanceAmountAttribute()
    {
        return 'LKR ' . number_format($this->advance_amount, 2);
    }

    /**
     * Get the formatted due balance.
     */
    public function getFormattedDueBalanceAttribute()
    {
        return 'LKR ' . number_format($this->due_balance, 2);
    }

    /**
     * Get the formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d M Y');
    }

    /**
     * Check if this is the latest advance for the customer.
     */
    public function isLatestForCustomer()
    {
        $latestAdvance = Advanced::where('invoice_customer_id', $this->invoice_customer_id)
            ->latest()
            ->first();
            
        return $latestAdvance && $latestAdvance->id === $this->id;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // After creating an advance, update the customer's advanced_id
        static::created(function ($advance) {
            $customer = $advance->customer;
            if ($customer) {
                $customer->update(['advanced_id' => $advance->id]);
            }
        });

        // After creating an advance, update the invoice if it exists
        static::created(function ($advance) {
            if ($advance->invoice_id) {
                $invoice = $advance->invoice;
                // You can add any invoice-specific logic here
            }
        });
    }
}