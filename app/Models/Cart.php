<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasUuids;
    protected $fillable = [
        'customer_id',
        'category_id',
        'product_id',
        'product_color_id',
        'product_variation_id',
        'guest_id',
        'quantity',
        'total',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productColor(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
