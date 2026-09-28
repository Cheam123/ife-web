<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * An item in the product catalogue. Admins maintain it; order lines and
 * product recommendations point at it. Inactive products stay on old orders
 * but are no longer offered or recommended.
 */
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'unit_price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'is_active'  => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'product_id');
    }

    /** Distinct categories in use, for the catalogue filter and form suggestions. */
    public static function categories(): array
    {
        return static::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();
    }
}
