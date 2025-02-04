<?php

namespace App\Models;

use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;

class Product extends Model
{
    use HasFactory,HasSlug;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discounted_price',
        'quantity',
        'seller_id',
        'sub_category_id',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
        ->generateSlugsFrom(fn ($model) => $model->name . '-' . $model->id)
        ->saveSlugsTo('slug');
    }


    public function getRouteKeyName()
    {
        return'slug';
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }
    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }
}
