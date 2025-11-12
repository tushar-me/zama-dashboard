<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
class ProductImage extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id',
        'campaign_id',
        'product_side_id',
        'product_color_id',
        'image',
        'side',
        'color_code'
    ];

    public function image(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : asset('noimage.webp'),
            set: fn ($value) => $value,
        );
    }
}
