<?php

namespace App\Models;

use App\Models\Scopes\AdminScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Color extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'hex_code',
        'order_level',
        'created_by',
        'last_updated_by',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (Color $color) {
            $color->creator()->associate(auth('admin')->user()->id);
            $color->editor()->associate(auth('admin')->user()->id);
        });

        static::updating(function (Color $color) {
            $color->editor()->associate(auth('admin')->user()->id);
        });
    }

    public function mockups(): BelongsToMany
    {
        return $this->belongsToMany(Mockup::class, 'color_mockup', 'color_id','mockup_id');
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
