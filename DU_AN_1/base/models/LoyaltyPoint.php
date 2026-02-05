<?php

class LoyaltyPoint extends BaseModel
{
    protected $table = 'loyalty_points';

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function createOrUpdate($userId, $points, $lifetimePoints)
    {
        $sql = "INSERT INTO {$this->table} (user_id, total_points, lifetime_points, level) 
                VALUES (:user_id, :total_points, :lifetime_points, :level)
                ON DUPLICATE KEY UPDATE 
                total_points = :total_points, 
                lifetime_points = :lifetime_points,
                level = :level,
                updated_at = NOW()";
        
        $level = $this->calculateLevel($lifetimePoints);
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':total_points' => $points,
            ':lifetime_points' => $lifetimePoints,
            ':level' => $level
        ]);
    }

    public function deductPoints($userId, $points)
    {
        $sql = "UPDATE {$this->table} SET total_points = total_points - :points, updated_at = NOW() 
                WHERE user_id = :user_id AND total_points >= :points";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':points' => $points]);
    }

    public function getTransactions($userId)
    {
        $sql = "SELECT * FROM loyalty_transactions WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function addTransaction($userId, $orderId, $type, $points, $description)
    {
        $sql = "INSERT INTO loyalty_transactions (user_id, order_id, type, points, description) 
                VALUES (:user_id, :order_id, :type, :points, :description)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':order_id' => $orderId,
            ':type' => $type,
            ':points' => $points,
            ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8')
        ]);
    }

    private function calculateLevel($lifetimePoints)
    {
        if ($lifetimePoints >= 1000) return 'diamond';  // 1000+ điểm
        if ($lifetimePoints >= 600) return 'gold';      // 600-999 điểm
        if ($lifetimePoints >= 400) return 'silver';    // 400-599 điểm
        if ($lifetimePoints >= 200) return 'bronze';    // 200-399 điểm
        return 'new';  // Dưới 200 điểm - Khách mới
    }

    /**
     * Cộng điểm loyalty từ đơn hàng hoàn thành
     * Quy tắc: 5.000đ = 1 điểm
     */
    public function addPointsFromOrder($userId, $orderTotal)
    {
        // Tính điểm: 5.000đ = 1 điểm
        $earnedPoints = floor($orderTotal / 5000);
        
        if ($earnedPoints <= 0) {
            return false;
        }

        // Lấy thông tin loyalty hiện tại
        $current = $this->getByUserId($userId);
        
        if ($current) {
            $newTotalPoints = $current['total_points'] + $earnedPoints;
            $newLifetimePoints = $current['lifetime_points'] + $earnedPoints;
        } else {
            $newTotalPoints = $earnedPoints;
            $newLifetimePoints = $earnedPoints;
        }

        // Cập nhật điểm
        return $this->createOrUpdate($userId, $newTotalPoints, $newLifetimePoints);
    }
}
