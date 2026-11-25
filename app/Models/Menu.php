<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'category_id', 'price', 'description', 'image', 'is_available'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeCategory($query, $categoryId)
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    public function getCategoryLabelAttribute()
    {
        return $this->category->name ?? 'Sans catégorie';
    }

    public function getFormattedPriceAttribute()
    {
        return \App\Helpers\Helper::formatCFA($this->price);
    }

    /** Un plat sans prix est proposé « sur commande » */
    public function getPriceLabelAttribute()
    {
        return $this->price > 0 ? $this->formatted_price : 'Sur commande';
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
