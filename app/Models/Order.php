<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'payment_method_id',
        'shipping_method_id',
        'ship_name',
        'ship_phone',
        'ship_province',
        'ship_district',
        'ship_ward',
        'ship_address',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'payment_status',
        'points_processed',
        'points_earned',
        'points_redeemed',
        'points_discount',
        'tier_discount',
        'cancel_reason',
        'admin_note',
        'customer_note',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'points_processed' => 'boolean',
        'points_earned' => 'integer',
        'points_redeemed' => 'integer',
        'points_discount' => 'decimal:2',
        'tier_discount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::updated(function ($order) {
            // Kiểm tra khi đơn hàng hoàn thành (finished) để tích điểm
            if ($order->wasChanged('status') && $order->status === 'finished') {
                if ($order->user && !$order->points_processed) {
                    // Tỷ lệ điểm thưởng theo hạng thành viên (Kim cương tích lũy x2)
                    $multiplier = $order->user->getTierPointsPercent();
                    $earnedPoints = (int) floor($order->subtotal / 10000) * $multiplier;

                    $order->updateQuietly([
                        'points_processed' => true,
                        'points_earned' => $earnedPoints,
                    ]);

                    // Cộng điểm tích lũy và cập nhật hạng thành viên
                    $order->user->increment('loyalty_points', $earnedPoints);
                    $order->user->updateLoyaltyAndTier();
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
