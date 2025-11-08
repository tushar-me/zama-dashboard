<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShippingCharge extends Model
{
    use HasUuids;
    protected $fillable = [
        'us_charge',
        'us_add_charge_per_item',
        'worldwide_charge',
        'worldwide_add_charge_per_item',
        'status',
        'is_free',
        'order_level',
        'created_by',
        'last_updated_by',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (ShippingCharge $shippingCharge) {
            $shippingCharge->creator()->associate(auth('admin')->user()->id);
            $shippingCharge->editor()->associate(auth('admin')->user()->id);
        });

        static::updating(function (ShippingCharge $shippingCharge) {
            $shippingCharge->editor()->associate(auth('admin')->user()->id);
        });
    }
    public function chargeable(): MorphTo
    {
        return $this->morphTo();
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
