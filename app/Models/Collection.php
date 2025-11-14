<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    use HasUuids,HasSlug;
    protected $fillable = ['name', 'image', 'description','created_by', 'last_updated_by'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    protected static function booted()
    {
        static::creating(function ($collection) {
            $collection->creator()->associate(auth('admin')->user()->id);
            $collection->editor()->associate(auth('admin')->user()->id);
        });
        static::updating(function ($collection) {
            $collection->editor()->associate(auth('admin')->user()->id);
        });
    }

    public function image(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : asset('noimage.webp'),
            set: fn ($value) => $value,
        );
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'collection_product', 'collection_id', 'product_id');
    }
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class,'collection_category', 'collection_id', 'category_id');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    public function editor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'last_updated_by');
    }
}
