<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'price',
        'description',
        'condition',
        'image',
        'status',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
        // 出品者情報
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
        // 購入情報
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id')->withTimeStamps();
        // いいね情報
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
