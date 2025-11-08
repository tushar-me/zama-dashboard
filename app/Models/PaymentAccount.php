<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentAccount extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'vendor_id',
        'payment_method_id',
        'account_name',
        'account_number',
        'bank_name',
        'branch_name',
        'routing_number',
        'swift_code',
        'country',
        'currency',
        'extra_details',
        'is_default',
    ];

    protected $casts = [
        'extra_details' => 'array',
        'is_default' => 'boolean',
    ];

protected static function booted()
    {
        static::creating(function ($account) {
            $account->vendor()->associate(request()->user()->id);
        });
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
