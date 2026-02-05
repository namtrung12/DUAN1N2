<?php

class LoyaltyReward extends BaseModel
{
    protected $table = 'loyalty_rewards';

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 1 ORDER BY point_cost ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND status = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserRewards($userId)
    {
        // Lấy rewards từ bảng loyalty_user_rewards (chưa dùng)
        $sql = "SELECT lur.id, lur.user_id, lur.reward_id as item_id, lr.reward_name, 
                       lr.reward_type, lr.value, lur.code, lur.expires_at, 
                       lur.is_used, lur.created_at, 'reward' as source
                FROM loyalty_user_rewards lur 
                JOIN loyalty_rewards lr ON lur.reward_id = lr.id 
                WHERE lur.user_id = :user_id 
                AND lur.is_used = 0
                
                UNION ALL
                
                SELECT urc.id, urc.user_id, urc.coupon_id as item_id, c.code as reward_name,
                       'coupon' as reward_type, 
                       CONCAT(IF(c.type = 'percent', CONCAT(c.value, '%'), CONCAT(FORMAT(c.value, 0), 'đ')), ' giảm') as value,
                       c.code, c.expires_at, 0 as is_used, urc.created_at, 'coupon' as source
                FROM user_redeemed_coupons urc
                JOIN coupons c ON urc.coupon_id = c.id
                LEFT JOIN user_coupon_usage ucu ON ucu.user_id = urc.user_id AND ucu.coupon_id = urc.coupon_id
                WHERE urc.user_id = :user_id
                AND ucu.id IS NULL
                
                ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function redeemReward($userId, $rewardId, $code, $expiresAt)
    {
        $sql = "INSERT INTO loyalty_user_rewards (user_id, reward_id, code, expires_at) 
                VALUES (:user_id, :reward_id, :code, :expires_at)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':reward_id' => $rewardId,
            ':code' => htmlspecialchars($code, ENT_QUOTES, 'UTF-8'),
            ':expires_at' => $expiresAt
        ]);
    }

    public function generateRewardCode()
    {
        return 'RW' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
}
