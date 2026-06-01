<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'role_id',
        'loyalty_points',
        'total_spent',
        'member_tier',
        'phone',
        'gender',
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'loyalty_points' => 'integer',
            'total_spent' => 'decimal:2',
        ];
    }

    /**
     * Override the password attribute name.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the social accounts associated with the user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Lấy tỷ lệ % giảm giá trực tiếp theo hạng thành viên.
     */
    public function getTierDiscountPercent(): int
    {
        switch ($this->member_tier) {
            case 'silver': return 2;
            case 'gold': return 5;
            case 'diamond': return 10;
            default: return 0;
        }
    }

    /**
     * Lấy tỷ lệ tích điểm theo hạng thành viên (Kim cương tích lũy x2).
     */
    public function getTierPointsPercent(): int
    {
        return $this->member_tier === 'diamond' ? 2 : 1;
    }

    /**
     * Cập nhật số tiền đã chi tiêu và phân hạng thành viên tương ứng.
     */
    public function updateLoyaltyAndTier(): void
    {
        // Tính tổng tiền từ các đơn hàng hoàn thành
        $totalSpent = $this->orders()->where('status', 'finished')->sum('total_amount');

        // Phân hạng thành viên dựa trên tổng tiền chi tiêu
        $tier = 'bronze';
        if ($totalSpent >= 15000000) {
            $tier = 'diamond';
        } elseif ($totalSpent >= 5000000) {
            $tier = 'gold';
        } elseif ($totalSpent >= 2000000) {
            $tier = 'silver';
        }

        $this->update([
            'total_spent' => $totalSpent,
            'member_tier' => $tier,
        ]);
    }
}
