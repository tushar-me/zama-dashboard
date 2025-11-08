<?php

namespace App\Models;

use App\Actions\GenerateUniqueCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\AdminScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Country extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'iso2',
        'iso3',
        'flag',
        'currency',
        'currency_symbol',
        'tax_percentage',
        'shipping_charge',
        'vat_percentage',
        'order_level',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function flag(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : asset('noimage.webp'),
            set: fn ($value) => $value,
        );
    }

    protected static function booted(): void
    {
        static::creating(function (Country $country) {
            $country->creator()->associate(auth('admin')->user()->id);
            $country->editor()->associate(auth('admin')->user()->id);
            $country->code = GenerateUniqueCode::for('countries', 'code', 4);
        });

        static::updating(function (Country $country) {
            $country->editor()->associate(auth('admin')->user()->id);
        });
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
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
