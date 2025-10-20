<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\ChatMessage;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'subtotal',
        'voucher_discount',
        'voucher_code',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'shipping_method',
        'phone',
        'name',
        'city',
        'postal_code',
        'notes',
        'snap_token',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'voucher_discount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
