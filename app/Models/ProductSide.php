<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
class ProductSide extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'product_id',
        'image',
        'artwork',
        'bounding_box',
        'artwork_props',
        'status'
    ];
    protected $casts = [
        'bounding_box' => 'array',
        'artwork_props' => 'array',
        'status' => 'boolean'
    ];

    public function image(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : null,
            set: fn ($value) => $value,
        );
    }
    public function artwork(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : null,
            set: fn ($value) => $value,
        );
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
