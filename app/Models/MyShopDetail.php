<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyShopDetail extends Model
{
    use HasFactory;

    protected $table = 'my_shop_details';

    protected $fillable = [
        'shop_name',
        'description',
        'address',
        'hotline',
        'email',
        'logo_image',
        'condition_1',
        'condition_2',
        'condition_3',
        'user_id', // Foreign key to the users table
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


