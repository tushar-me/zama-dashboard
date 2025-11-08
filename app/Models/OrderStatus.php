<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatus extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'status',
        'is_default',
        'order_level',
        'created_by',
        'last_updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'last_updated_by');
    }
}
