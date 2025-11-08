<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PaymentMethod extends Model
{
    use HasUuids;

    protected $fillable = ['name','logo','status','is_vendor_payment_method','order_level','created_by','last_updated_by'];
    protected $casts = [
        'is_vendor_payment_method' => 'boolean',
        'status' => 'boolean',
    ];
    public function logo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : asset('noimage.webp'),
            set: fn ($value) => $value,
        );
    }
    protected static function booted()
    {
        static::creating(function (PaymentMethod $method) {
            $method->creator()->associate(auth('admin')->user()->id);
            $method->editor()->associate(auth('admin')->user()->id);
        });

        static::updating(function (PaymentMethod $method) {
            $method->editor()->associate(auth('admin')->user()->id);
        });
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
