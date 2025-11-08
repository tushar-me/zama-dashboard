<?php

namespace App\Models;

use App\Models\Scopes\AdminScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Size extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'order_level',
        'created_by',
        'last_updated_by',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($size) {
            $size->creator()->associate(auth('admin')->user()->id);
            $size->editor()->associate(auth('admin')->user()->id);
        });

        static::updating(function ($size) {
            $size->editor()->associate(auth('admin')->user()->id);
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
