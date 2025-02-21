<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Enum\OrderStatusEnum;
use Spatie\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\SlugOptions;

class Order extends Model
{
    use HasFactory,HasSlug;
    protected $fillable = [
        'slug',
        'user_id',
        'total_price',
        'status',
        'shipping_address',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
        ->generateSlugsFrom(fn($order) => 'ORD-'. $order->id)
        ->saveSlugsTo('slug');
    }
}