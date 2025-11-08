<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Actions\GenerateUniqueCode;
use Illuminate\support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Product extends Model
{
    use HasUuids, HasUuids, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_id',
        'store_id',
        'campaign_id',
        'mockup_id',
        'category_id',
        'brand_id',
        'code',
        'name',
        'slug',
        'cover_image',
        'hover_image',
        'size_chart',
        'short_description',
        'description',
        'specification',
        'video_url',
        'type',
        'is_default',
        'price',
        'compare_price',
        'unit_price',
        'profit_amount',
        'cost',
        'tax',
        'is_tax_included',
        'inventory_quantity',
        'inventory_policy',
        'weight',
        'dimensions',
        'vendor_profit',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'og_title',
        'og_description',
        'canonical_url',
        'alt_text',
        'extra_meta',
        'sale',
        'view',
        'likes',
        'status',
        'published_at',
        'tags',
        'artwork',
        'artwork_file',
        'is_deleted'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'specification' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'is_tax_included' => 'boolean',
        'inventory_quantity' => 'integer',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'vendor_profit' => 'decimal:2',
        'sale' => 'integer',
        'view' => 'integer',
        'likes' => 'integer',
        'published_at' => 'datetime',
        'tags' => 'array',
        'is_default' => 'boolean',
        'is_deleted' => 'boolean'
    ];
    protected static function booted()
    {
        static::creating(function ($product) {
            $product->code = GenerateUniqueCode::for('products', 'code', 4);
        });
    }
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
      public function artwork(): Attribute
      {
            return new Attribute(
                get: fn ($value) => $value ? Storage::url($value) : null,
                set: fn ($value) => $value,
            );
      }
    /**
     * Get the vendor that owns the product.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the store that owns the product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the campaign that owns the product.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the mockup that owns the product.
     */
    public function mockup(): BelongsTo
    {
        return $this->belongsTo(Mockup::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
    public function scopeActive($query)
    {
        return $query->whereHas('images');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function productSides(): HasMany
    {
        return $this->hasMany(ProductSide::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }
     public function defaultColor(): HasOne
     {
        return $this->hasOne(ProductColor::class)
            ->orderByDesc('is_default') 
            ->orderBy('id');           
     }
    public function defaultVariation(): HasOne
    {
        return $this->hasOne(ProductVariation::class)->oldest();
    }
     public function defaultSide(): HasOne
     {
        return $this->hasOne(ProductSide::class)
            ->orderByDesc('is_default') 
            ->orderBy('created_at');           
     }
    public function coverImage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $defaultColorImage = $this->colors()
                    ->where('is_default', true)
                    ->first()?->images()
                    ->latest()
                    ->first()
                    ?->image;
                if ($defaultColorImage) {
                    return $defaultColorImage;
                }
                return $this->images()
                    ->latest()
                    ->first()
                    ?->image;
                }
        );
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
