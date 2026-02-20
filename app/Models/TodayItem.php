<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodayItem extends Model
{
    use HasFactory;

    protected $table = 'today_items';

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
        'cost',
        'quantity',
        'total_cost',
        'stock_id',
        'user_id',
        'session_id',
        'selection_date'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'selection_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stock()
    {
        // Specify the correct table name in the relationship
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }
}