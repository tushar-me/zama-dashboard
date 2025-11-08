<?php

namespace App\Models;

use App\Models\Scopes\AdminScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

#[ScopedBy([AdminScope::class])]
class Badge extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'logo',
        'type',
        'adjustment_type',
        'adjustment_value',
        'order_level',
        'status',
        'created_by',
        'last_updated_by',
    ];

    public function logo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? Storage::url($value) : asset('noimage.webp'),
            set: fn ($value) => $value,
        );
    }
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Badge $badge) {
            $badge->creator()->associate(auth('admin')->user()->id);
            $badge->editor()->associate(auth('admin')->user()->id);
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
