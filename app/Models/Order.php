<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_code',
        'customer_id',
        'guest_id',
        'country_id',
        'state_id',
        'city_id',
        'paypal_order_id',
        'payment_details',
        'order_status_id',
        'payment_method',
        'tax_percentage',
        'shipping_charge',
        'shipping_warranty',
        'rush_production',
        'tax',
        'vat',
        'sub_total',
        'grand_total',
        'payment_status',
        'status_note',
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
