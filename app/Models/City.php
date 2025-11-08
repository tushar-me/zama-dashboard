<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Actions\GenerateUniqueCode;

class City extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'state_id',
        'country_id',
        'code',
        'postal_code',
        'timezone',
        'area_code',
        'shipping_charge',
        'tax_percentage',
        'vat_percentage',
        'latitude',
        'longitude',
        'is_default',
        'order_level',
        'status',
        'created_by',
        'last_updated_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (City $city) {
            $city->creator()->associate(auth('admin')->user()->id);
            $city->creator()->associate(auth('admin')->user()->id);
            $city->code = GenerateUniqueCode::for('states', 'code', 4);
        });

        static::updating(function (City $city) {
            $city->editor()->associate(auth('admin')->user()->id);
        });
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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
