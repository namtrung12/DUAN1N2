<?php

class Coupon extends BaseModel
{
    protected $table = 'coupons';

    public function getByCode($code)
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code AND status = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => strtoupper($code)]);
        return $stmt->fetch();
    }

    public function isValid($coupon, $subtotal, $userRank = null)
    {
        if (!$coupon) return false;

        // Kiểm tra trạng thái
        if (!$coupon['status']) return false;

        // Kiểm tra giới hạn sử dụng
        if ($coupon['usage_limit'] > 0 && $coupon['used_count'] >= $coupon['usage_limit']) {
            return false;
        }

        // Kiểm tra đơn tối thiểu
        if ($coupon['min_order'] > 0 && $subtotal < $coupon['min_order']) {
            return false;
        }

        // Kiểm tra thời gian bắt đầu
        if ($coupon['starts_at'] && strtotime($coupon['starts_at']) > time()) {
            return false;
        }

        // Kiểm tra hết hạn
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            return false;
        }

        // Kiểm tra rank (nếu có yêu cầu)
        if ($coupon['required_rank']) {
            // Nếu coupon yêu cầu rank nhưng không có userRank, reject
            if (!$userRank) {
                return false;
            }
            
            $rankOrder = ['bronze' => 1, 'silver' => 2, 'gold' => 3, 'diamond' => 4];
            $requiredLevel = $rankOrder[$coupon['required_rank']] ?? 0;
            $userLevel = $rankOrder[$userRank] ?? 0;
            
            if ($userLevel < $requiredLevel) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($coupon, $subtotal)
    {
        if (!$coupon) return 0;

        if ($coupon['type'] === 'percent') {
            $discount = ($subtotal * $coupon['value']) / 100;
            
            // Áp dụng giảm tối đa nếu có
            if (isset($coupon['max_discount']) && $coupon['max_discount'] > 0) {
                $discount = min($discount, $coupon['max_discount']);
            }
            
            return $discount;
        } else {
            return min($coupon['value'], $subtotal);
        }
    }

    public function getAllAdmin()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (code, type, value, max_discount, min_order, usage_limit, required_rank, point_cost, is_redeemable, starts_at, expires_at, status, description) 
                VALUES 
                (:code, :type, :value, :max_discount, :min_order, :usage_limit, :required_rank, :point_cost, :is_redeemable, :starts_at, :expires_at, :status, :description)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':code' => $data['code'],
            ':type' => $data['type'],
            ':value' => $data['value'],
            ':max_discount' => $data['max_discount'] ?? null,
            ':min_order' => $data['min_order'],
            ':usage_limit' => $data['usage_limit'],
            ':required_rank' => $data['required_rank'],
            ':point_cost' => $data['point_cost'],
            ':is_redeemable' => $data['is_redeemable'],
            ':starts_at' => $data['starts_at'],
            ':expires_at' => $data['expires_at'],
            ':status' => $data['status'],
            ':description' => $data['description'] ?? null
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                code = :code, 
                type = :type, 
                value = :value,
                max_discount = :max_discount, 
                min_order = :min_order, 
                usage_limit = :usage_limit,
                required_rank = :required_rank,
                point_cost = :point_cost,
                is_redeemable = :is_redeemable,
                starts_at = :starts_at, 
                expires_at = :expires_at, 
                status = :status,
                description = :description 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':code' => $data['code'],
            ':type' => $data['type'],
            ':value' => $data['value'],
            ':max_discount' => $data['max_discount'] ?? null,
            ':min_order' => $data['min_order'],
            ':usage_limit' => $data['usage_limit'],
            ':required_rank' => $data['required_rank'],
            ':point_cost' => $data['point_cost'],
            ':is_redeemable' => $data['is_redeemable'],
            ':starts_at' => $data['starts_at'],
            ':expires_at' => $data['expires_at'],
            ':status' => $data['status'],
            ':description' => $data['description'] ?? null
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getRedeemableCoupons()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_redeemable = 1 
                AND status = 1 
                AND (expires_at IS NULL OR expires_at > NOW())
                AND (usage_limit = 0 OR used_count < usage_limit)
                ORDER BY point_cost ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function redeemCoupon($userId, $couponId, $pointsSpent)
    {
        // Thêm vào bảng user_redeemed_coupons
        $sql = "INSERT INTO user_redeemed_coupons (user_id, coupon_id, points_spent) 
                VALUES (:user_id, :coupon_id, :points_spent)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':coupon_id' => $couponId,
            ':points_spent' => $pointsSpent
        ]);
    }

    public function hasUserRedeemed($userId, $couponId)
    {
        $sql = "SELECT COUNT(*) FROM user_redeemed_coupons 
                WHERE user_id = :user_id AND coupon_id = :coupon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':coupon_id' => $couponId]);
        return $stmt->fetchColumn() > 0;
    }

    public function hasUserUsedCoupon($userId, $couponId)
    {
        $sql = "SELECT COUNT(*) FROM user_coupon_usage 
                WHERE user_id = :user_id AND coupon_id = :coupon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':coupon_id' => $couponId]);
        return $stmt->fetchColumn() > 0;
    }

    public function recordCouponUsage($userId, $couponId, $orderId, $discountAmount)
    {
        $sql = "INSERT INTO user_coupon_usage (user_id, coupon_id, order_id, discount_amount) 
                VALUES (:user_id, :coupon_id, :order_id, :discount_amount)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':coupon_id' => $couponId,
            ':order_id' => $orderId,
            ':discount_amount' => $discountAmount
        ]);
    }

    public function getSuggestedCoupons($userRank, $minOrder = 100000)
    {
        // Hiển thị tất cả mã user có thể dùng (rank của user và thấp hơn)
        // Ưu tiên sắp xếp: Mã rank cao nhất trước
        
        $rankOrder = ['new' => 0, 'bronze' => 1, 'silver' => 2, 'gold' => 3, 'diamond' => 4];
        $userLevel = $rankOrder[$userRank] ?? 0;
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 
                AND is_redeemable = 0
                AND (expires_at IS NULL OR expires_at > NOW())
                AND (starts_at IS NULL OR starts_at <= NOW())
                AND min_order <= :min_order
                AND (usage_limit = 0 OR used_count < usage_limit)
                AND (required_rank IS NULL OR (
                    CASE required_rank
                        WHEN 'bronze' THEN 1
                        WHEN 'silver' THEN 2
                        WHEN 'gold' THEN 3
                        WHEN 'diamond' THEN 4
                    END
                ) <= :user_level)
                ORDER BY 
                    CASE required_rank
                        WHEN 'diamond' THEN 4
                        WHEN 'gold' THEN 3
                        WHEN 'silver' THEN 2
                        WHEN 'bronze' THEN 1
                        ELSE 0
                    END DESC,
                    value DESC
                LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':min_order' => $minOrder,
            ':user_level' => $userLevel
        ]);
        
        return $stmt->fetchAll();
    }

    public function getUserRedeemedCoupons($userId)
    {
        $sql = "SELECT c.* FROM user_redeemed_coupons urc
                JOIN {$this->table} c ON urc.coupon_id = c.id
                WHERE urc.user_id = :user_id
                AND c.status = 1
                AND (c.expires_at IS NULL OR c.expires_at > NOW())
                ORDER BY urc.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function incrementUsedCount($couponId)
    {
        $sql = "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $couponId]);
    }
}
