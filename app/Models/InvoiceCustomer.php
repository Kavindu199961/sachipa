<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCustomer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'location',
        'total_amount',
        'final_discount',
        'final_amount',
        'invoice_id',
        'advanced_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'final_discount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the invoices for this customer.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'invoice_customer_id');
    }

    /**
     * Get the advance payments for this customer.
     */
    public function advances()
    {
        return $this->hasMany(Advanced::class, 'invoice_customer_id');
    }

    /**
     * Get the latest invoice for this customer.
     */
    public function latestInvoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Get the latest advance payment for this customer.
     */
    public function latestAdvanced()
    {
        return $this->belongsTo(Advanced::class, 'advanced_id');
    }

    /**
     * Get the total advance amount paid by this customer.
     */
    public function getTotalAdvanceAmountAttribute()
    {
        return $this->advances()->sum('advance_amount');
    }

    /**
     * Get the current due balance for this customer.
     */
    public function getDueBalanceAttribute()
    {
        return $this->final_amount - $this->getTotalAdvanceAmountAttribute();
    }

    /**
     * Get the payment status attribute.
     */
    public function getPaymentStatusAttribute()
    {
        $dueBalance = $this->due_balance;
        
        if ($dueBalance <= 0) {
            return 'Paid';
        } elseif ($dueBalance < $this->final_amount / 2) {
            return 'Partial';
        } else {
            return 'Due';
        }
    }

    /**
     * Get the status color for badges.
     */
    public function getStatusColorAttribute()
    {
        $dueBalance = $this->due_balance;
        
        if ($dueBalance <= 0) {
            return 'success';
        } elseif ($dueBalance < $this->final_amount / 2) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate final amount before saving if not set
        static::saving(function ($customer) {
            if (isset($customer->total_amount) && isset($customer->final_discount)) {
                $discountAmount = $customer->total_amount * ($customer->final_discount / 100);
                $customer->final_amount = round($customer->total_amount - $discountAmount, 2);
            }
        });
    }
}