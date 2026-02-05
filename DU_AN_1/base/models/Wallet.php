<?php

class Wallet extends BaseModel
{
    protected $table = 'wallets';

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function createOrGet($userId)
    {
        $wallet = $this->getByUserId($userId);
        if ($wallet) return $wallet;

        $sql = "INSERT INTO {$this->table} (user_id, balance) VALUES (:user_id, 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $this->getByUserId($userId);
    }

    public function updateBalance($userId, $amount)
    {
        $sql = "UPDATE {$this->table} SET balance = balance + :amount, updated_at = NOW() 
                WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':amount' => $amount]);
    }

    public function getTransactions($userId)
    {
        $sql = "SELECT * FROM wallet_transactions WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function addTransaction($userId, $orderId, $type, $amount, $description)
    {
        $sql = "INSERT INTO wallet_transactions (user_id, order_id, type, amount, description) 
                VALUES (:user_id, :order_id, :type, :amount, :description)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':order_id' => $orderId,
            ':type' => $type,
            ':amount' => $amount,
            ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8')
        ]);
    }
}
