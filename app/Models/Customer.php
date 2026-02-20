<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the fabric calculations for the customer.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fabricCalculations(): HasMany
    {
        return $this->hasMany(FabricCal::class, 'customer_id');
    }

    /**
     * Get the customer's full name with phone number.
     * 
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->phone_number ? ' (' . $this->phone_number . ')' : '');
    }

    /**
     * Scope a query to only include customers with phone numbers.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasPhoneNumber($query)
    {
        return $query->whereNotNull('phone_number');
    }

    /**
     * Scope a query to search customers by name or phone.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('phone_number', 'like', "%{$search}%");
    }
}