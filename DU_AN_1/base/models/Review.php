<?php

class Review extends BaseModel
{
    protected $table = 'reviews';

    public function create($data)
    {
        // Kiểm tra xem cột order_id có tồn tại không
        try {
            $sql = "INSERT INTO {$this->table} (user_id, product_id, order_id, rating, comment) 
                    VALUES (:user_id, :product_id, :order_id, :rating, :comment)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            // Nếu cột order_id chưa tồn tại, insert không có order_id
            if (strpos($e->getMessage(), 'order_id') !== false) {
                $sql = "INSERT INTO {$this->table} (user_id, product_id, rating, comment) 
                        VALUES (:user_id, :product_id, :rating, :comment)";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    ':user_id' => $data[':user_id'],
                    ':product_id' => $data[':product_id'],
                    ':rating' => $data[':rating'],
                    ':comment' => $data[':comment']
                ]);
            }
            throw $e;
        }
    }

    public function getByProduct($productId)
    {
        $sql = "SELECT r.*, u.name as user_name, u.avatar as user_avatar 
                FROM reviews r
                INNER JOIN users u ON r.user_id = u.id
                WHERE r.product_id = :product_id AND r.status = 1
                ORDER BY r.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function hasUserReviewed($userId, $productId, $orderId = null)
    {
        // Kiểm tra xem cột order_id có tồn tại không
        try {
            if ($orderId !== null) {
                $sql = "SELECT COUNT(*) FROM {$this->table} 
                        WHERE user_id = :user_id AND product_id = :product_id AND order_id = :order_id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId,
                    ':order_id' => $orderId
                ]);
            } else {
                $sql = "SELECT COUNT(*) FROM {$this->table} 
                        WHERE user_id = :user_id AND product_id = :product_id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId
                ]);
            }
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Nếu cột order_id chưa tồn tại, chỉ kiểm tra user_id và product_id
            $sql = "SELECT COUNT(*) FROM {$this->table} 
                    WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            return $stmt->fetchColumn() > 0;
        }
    }

    public function getAverageRating($productId)
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM {$this->table} 
                WHERE product_id = :product_id AND status = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetch();
    }

    public function getAll()
    {
        $sql = "SELECT r.*, u.name as user_name, u.email as user_email, u.avatar as user_avatar, p.name as product_name
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                ORDER BY r.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getByStatus($status)
    {
        $sql = "SELECT r.*, u.name as user_name, u.email as user_email, u.avatar as user_avatar, p.name as product_name
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE r.status = :status
                ORDER BY r.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    public function getById($id)
    {
        $sql = "SELECT r.*, u.name as user_name, u.email as user_email, u.avatar as user_avatar, p.name as product_name
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE r.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
