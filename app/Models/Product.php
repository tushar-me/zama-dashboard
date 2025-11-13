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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasUuids, HasUuids, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'code',
        'name',
        'slug',
        'cover_image',
        'hover_image',
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
            $product->creator()->associate(auth('admin')->user()->id);
            $product->editor()->associate(auth('admin')->user()->id);
            $product->code = GenerateUniqueCode::for('products', 'code', 4);
        });
        static::updating(function ($product) {
            $product->editor()->associate(auth('admin')->user()->id);
        });
    }
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    public function coverImage(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : null,
            set: fn ($value) => $value,
        );
    }
    public function hoverImage(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : null,
            set: fn ($value) => $value,
        );
    }
    public function measurementGuide(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : asset('noimage.webp'),
            set: fn ($value) => $value,
        );
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

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }
    public function collections():BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_product', 'product_id', 'collection_id');
    }
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    public function editor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'last_updated_by');
    }

    public function sizeChart(): HasOne
    {
        return $this->hasOne(SizeChart::class, 'product_id');
    }
}
