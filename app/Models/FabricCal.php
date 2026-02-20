<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricCal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fabric_cal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'stick',
        'one_rali',
        'two_rali',
        'tree_rali',
        'four_rali',
        'ilets',
        'sum_one_four',
        'sum_two_tree',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stick' => 'float',
        'one_rali' => 'decimal:2',
        'two_rali' => 'decimal:2',
        'tree_rali' => 'decimal:2',
        'four_rali' => 'decimal:2',
        'ilets' => 'decimal:2',
        'sum_one_four' => 'decimal:2',
        'sum_two_tree' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the customer that owns the fabric calculation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Check if the fabric calculation belongs to a customer.
     * 
     * @return bool
     */
    public function hasCustomer(): bool
    {
        return !is_null($this->customer_id);
    }

    /**
     * Calculate and update sum_one_four automatically.
     * 
     * @return void
     */
    public function calculateSumOneFour(): void
    {
        if (!is_null($this->one_rali) && !is_null($this->four_rali)) {
            $this->sum_one_four = $this->one_rali + $this->four_rali;
        }
    }

    /**
     * Calculate and update sum_two_tree automatically.
     * 
     * @return void
     */
    public function calculateSumTwoTree(): void
    {
        if (!is_null($this->two_rali) && !is_null($this->tree_rali)) {
            $this->sum_two_tree = $this->two_rali + $this->tree_rali;
        }
    }

    /**
     * Calculate both sums automatically.
     * 
     * @return void
     */
    public function calculateAllSums(): void
    {
        $this->calculateSumOneFour();
        $this->calculateSumTwoTree();
    }

    /**
     * Boot the model.
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate sums when saving
        static::saving(function ($fabricCal) {
            $fabricCal->calculateAllSums();
        });

        // Auto-calculate sums when updating
        static::updating(function ($fabricCal) {
            $fabricCal->calculateAllSums();
        });
    }

    /**
     * Scope a query to only include records with a specific customer.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include records with all fields filled.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('stick')
                     ->whereNotNull('one_rali')
                     ->whereNotNull('two_rali')
                     ->whereNotNull('tree_rali')
                     ->whereNotNull('four_rali')
                     ->whereNotNull('ilets');
    }

    /**
     * Scope a query to only include records with missing data.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stick')
              ->orWhereNull('one_rali')
              ->orWhereNull('two_rali')
              ->orWhereNull('tree_rali')
              ->orWhereNull('four_rali')
              ->orWhereNull('ilets');
        });
    }

    /**
     * Get the total of all rali measurements.
     * 
     * @return float|null
     */
    public function getTotalRaliAttribute(): ?float
    {
        $total = 0;
        $count = 0;
        
        if (!is_null($this->one_rali)) {
            $total += $this->one_rali;
            $count++;
        }
        if (!is_null($this->two_rali)) {
            $total += $this->two_rali;
            $count++;
        }
        if (!is_null($this->tree_rali)) {
            $total += $this->tree_rali;
            $count++;
        }
        if (!is_null($this->four_rali)) {
            $total += $this->four_rali;
            $count++;
        }
        
        return $count > 0 ? $total : null;
    }
}