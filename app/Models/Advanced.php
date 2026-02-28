<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advanced extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'advances';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_id',
        'invoice_customer_id',
        'advance_amount',
        'due_balance',
        'date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'advance_amount' => 'decimal:2',
        'due_balance'    => 'decimal:2',
        'date'           => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(InvoiceCustomer::class, 'invoice_customer_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFormattedAdvanceAmountAttribute()
    {
        return 'LKR ' . number_format($this->advance_amount, 2);
    }

    public function getFormattedDueBalanceAttribute()
    {
        return 'LKR ' . number_format($this->due_balance, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d M Y');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Check if this is the latest advance for the customer.
     */
    public function isLatestForCustomer()
    {
        $latest = Advanced::where('invoice_customer_id', $this->invoice_customer_id)
            ->latest()
            ->first();

        return $latest && $latest->id === $this->id;
    }

    /**
     * Calculate and set due_balance automatically from the linked invoice.
     * due_balance = invoice.final_amount - total advances paid so far (including this one)
     */
    protected static function calculateDueBalance(Advanced $advance): void
    {
        if (! $advance->invoice_id) {
            return;
        }

        $invoice = Invoice::find($advance->invoice_id);

        if (! $invoice) {
            return;
        }

        // Sum all previously saved advances for this invoice (excluding current record on update)
        $previousTotal = Advanced::where('invoice_id', $advance->invoice_id)
            ->when($advance->exists, fn($q) => $q->where('id', '!=', $advance->id))
            ->sum('advance_amount');

        $totalPaid = $previousTotal + (float) $advance->advance_amount;

        $due = max(0, (float) $invoice->final_amount - $totalPaid);

        $advance->due_balance = round($due, 2);
    }

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        // Auto-calculate due_balance before saving
        static::creating(function (Advanced $advance) {
            static::calculateDueBalance($advance);

            // Set date if not provided
            if (empty($advance->date)) {
                $advance->date = now();
            }
        });

        static::updating(function (Advanced $advance) {
            static::calculateDueBalance($advance);
        });

        // After creating an advance, update the customer's advanced_id
        static::created(function (Advanced $advance) {
            if ($advance->customer) {
                $advance->customer->update(['advanced_id' => $advance->id]);
            }
        });
    }
}