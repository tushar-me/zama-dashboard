<?php

namespace App\Models;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\CombinedOrder;
use App\Models\Mockup;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
class OrderDetail extends Model
{
    use HasUuids;

    protected $fillable = [
        'combined_order_id',
        'order_id',
        'product_id',
        'category_id',
        'mockup_id',
        'campaign_id',
        'artworks',
        'color',
        'size',
        'quantity',
        'price',
        'total',
        'product_images'
    ];

    protected $casts = [
        'artworks' => 'array',
        'product_images' => 'array'
    ];

    public function combinedOrder(): BelongsTo
    {
        return $this->belongsTo(CombinedOrder::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function mockup(): BelongsTo
    {
        return $this->belongsTo(Mockup::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
