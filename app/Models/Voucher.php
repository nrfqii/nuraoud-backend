<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        return $this->is_active &&
               (!$this->starts_at || now()->gte($this->starts_at)) &&
               (!$this->expires_at || now()->lte($this->expires_at)) &&
               (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function isValidForOrder($subtotal = null)
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->min_order_amount && $subtotal && $subtotal < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return $discount;
    }
}
