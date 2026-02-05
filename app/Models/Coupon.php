<?php

class Coupon extends BaseModel
{
    protected string $collection = 'coupons';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getAll(): array
    {
        return $this->all();
    }

    public function createCoupon(array $payload): array
    {
        return $this->create($payload);
    }

    public function updateCoupon(int $id, array $payload): ?array
    {
        return $this->update($id, $payload);
    }

    public function deleteMany(array $ids): int
    {
        $data = $this->all();
        $ids = array_map('intval', $ids);
        $filtered = [];
        $count = 0;
        foreach ($data as $item) {
            if (in_array((int)$item['id'], $ids, true)) {
                $count++;
                continue;
            }
            $filtered[] = $item;
        }
        if ($count > 0) {
            $this->saveAll($filtered);
        }
        return $count;
    }

    public function hasUserRedeemed(int $userId, int $couponId): bool
    {
        $redemptions = $this->store->read('coupon_redemptions');
        foreach ($redemptions as $redeem) {
            if ((int)$redeem['user_id'] === $userId && (int)$redeem['coupon_id'] === $couponId) {
                return true;
            }
        }
        return false;
    }

    public function redeem(int $userId, int $couponId): bool
    {
        if ($this->hasUserRedeemed($userId, $couponId)) {
            return false;
        }

        $coupon = $this->findById($couponId);
        if (!$coupon) {
            return false;
        }
        $usageLimit = (int)($coupon['usage_limit'] ?? 0);
        $usedCount = (int)($coupon['used_count'] ?? 0);
        if ($usageLimit > 0 && $usedCount >= $usageLimit) {
            return false;
        }
        $maxRedemptions = (int)($coupon['max_redemptions'] ?? 0);
        $redemptionCount = (int)($coupon['redemption_count'] ?? 0);
        if ($maxRedemptions > 0 && $redemptionCount >= $maxRedemptions) {
            return false;
        }

        $redemptions = $this->store->read('coupon_redemptions');
        $redemptions[] = [
            'id' => $this->store->nextId('coupon_redemptions'),
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'redeemed_at' => date('Y-m-d H:i:s'),
            'is_used' => 0
        ];
        $this->store->write('coupon_redemptions', $redemptions);

        $used = $usedCount + 1;
        $redeemCount = $redemptionCount + 1;
        $this->update($couponId, [
            'used_count' => $used,
            'redemption_count' => $redeemCount
        ]);

        return true;
    }
}
