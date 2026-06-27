<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    protected $fillable = ['user_id', 'points', 'level', 'total_spent'];

    protected $casts = [
        'points' => 'integer',
        'total_spent' => 'float',
    ];

    public const LEVELS = [
        'bronze' => ['min_spent' => 0, 'name' => 'Bronze'],
        'silver' => ['min_spent' => 500, 'name' => 'Silver'],
        'gold' => ['min_spent' => 1500, 'name' => 'Gold'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getLevelForSpent(float $spent): string
    {
        $level = 'bronze';
        foreach (array_reverse(self::LEVELS, true) as $key => $config) {
            if ($spent >= $config['min_spent']) {
                $level = $key;
                break;
            }
        }
        return $level;
    }

    public static function addPointsForOrder(User $user, int $orderId, float $orderAmount, int $pointsEarned): void
    {
        $lp = self::firstOrCreate(
            ['user_id' => $user->id],
            ['points' => 0, 'level' => 'bronze', 'total_spent' => 0]
        );

        $lp->points += $pointsEarned;
        $lp->total_spent += $orderAmount;
        $lp->level = self::getLevelForSpent($lp->total_spent);
        $lp->save();

        LoyaltyPointLog::create([
            'user_id' => $user->id,
            'points' => $pointsEarned,
            'type' => 'order_reward',
            'description' => 'نقاط من طلب #' . $orderId,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Deduct loyalty points for order redemption (استبدال نقاط بكاش).
     */
    public static function deductPointsForOrder(User $user, int $orderId, int $pointsToUse, float $discountAmount): void
    {
        $lp = self::firstOrCreate(
            ['user_id' => $user->id],
            ['points' => 0, 'level' => 'bronze', 'total_spent' => 0]
        );

        if ($lp->points < $pointsToUse) {
            throw new \InvalidArgumentException('Insufficient loyalty points');
        }

        $lp->points -= $pointsToUse;
        $lp->save();

        LoyaltyPointLog::create([
            'user_id' => $user->id,
            'points' => -$pointsToUse,
            'type' => 'redemption',
            'description' => 'استبدال نقاط في طلب #' . $orderId,
            'order_id' => $orderId,
        ]);
    }
}
