<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory,HasUuids,HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'image',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'og_title',
        'og_description',
        'sale',
        'view',
        'status',
        'video_url',
        'published_at',
        'tags',
        'is_featured',
        'is_trending',
        'is_bestseller',
        'is_sponsored'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sale' => 'integer',
        'view' => 'integer',
        'published_at' => 'datetime',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_sponsored' => 'boolean'
    ];

     /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    /**
     * Get the store that owns the campaign.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function defaultProduct(): HasOne
    {
        return $this->hasOne(Product::class)
            ->orderByDesc('is_default') 
            ->orderBy('id');           
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'published')
                    ->whereHas('products');
    }
    public function storefronts(): BelongsToMany
    {
        return $this->belongsToMany(StoreFront::class, 'store_front_campaign', 'campaign_id', 'store_front_id');
    }
}
