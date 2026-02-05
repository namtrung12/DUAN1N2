<?php

class Notification extends BaseModel
{
    protected $table = 'notifications';

    public function create($userId, $type, $title, $message, $orderId = null)
    {
        $sql = "INSERT INTO {$this->table} (user_id, type, title, message, order_id) 
                VALUES (:user_id, :type, :title, :message, :order_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            ':message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            ':order_id' => $orderId
        ]);
    }

    public function getByUserId($userId, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUnreadCount($userId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($id, $userId)
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    public function markAllAsRead($userId)
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
}
