<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    use HasUuids;

    protected $fillable = ['size_id','product_id','compare_price','price','sku','status'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}
