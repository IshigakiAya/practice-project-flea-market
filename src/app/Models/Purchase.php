<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
        // 購入者
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
        // 購入商品
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
        // 購入時の配送先住所
    }
}
