<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Actions\GenerateUniqueCode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Store extends Model
{
    /**
     * @use HasUuids<\Illuminate\Database\Eloquent\Concerns\HasUuids>
     */
        use HasUuids, HasSlug;
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'email',
        'phone',
        'default_domain',
        'custom_domain',
        'custom_domain_verified',
        'logo',
        'favicon',
        'banner',
        'primary_color',
        'address',
        'is_active',
        'is_verified',
        'is_suspend',
        'last_active_at',
        'storage_used_mb',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('url');
    }
    protected static function booted()
    {
        static::creating(function (Store $store) {
            $store->vendor()->associate(request()->user()->id);
            $store->code = GenerateUniqueCode::for('stores', 'code', 6);
        });
    }
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
