<?php

namespace App\Models;

use App\Models\Scopes\AdminScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Actions\GenerateUniqueCode;

class State extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'country_id',
        'shipping_charge',
        'tax_percentage',
        'vat_percentage',
        'capital',
        'timezone',
        'iso_code',
        'currency',
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
        static::creating(function (State $state) {
            $state->creator()->associate(auth('admin')->user()->id);
            $state->creator()->associate(auth('admin')->user()->id);
            $state->code = GenerateUniqueCode::for('states', 'code', 4);
        });

        static::updating(function (State $state) {
            $state->creator()->associate(auth('admin')->user()->id);
        });
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
