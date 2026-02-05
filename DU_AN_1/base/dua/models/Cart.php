<?php

class Cart extends BaseModel
{
    protected $table = 'cart';

    public function getByUserId($userId)
    {
        $sql = "SELECT c.*, p.name as product_name, p.image, p.id as product_id
                FROM {$this->table} c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id 
                ORDER BY c.updated_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function add($userId, $productId, $size, $quantity, $iceLevel = 100, $sugarLevel = 100, $note = null)
    {
        $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity, size, ice_level, sugar_level, note) 
                VALUES (:user_id, :product_id, :quantity, :size, :ice_level, :sugar_level, :note)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':size' => htmlspecialchars($size, ENT_QUOTES, 'UTF-8'),
            ':ice_level' => $iceLevel,
            ':sugar_level' => $sugarLevel,
            ':note' => $note ? htmlspecialchars($note, ENT_QUOTES, 'UTF-8') : null
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function updateQuantity($id, $userId, $quantity)
    {
        $sql = "UPDATE {$this->table} SET quantity = :quantity, updated_at = NOW() 
                WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
            ':quantity' => $quantity
        ]);
    }

    public function remove($id, $userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    public function getToppings($cartId)
    {
        $sql = "SELECT t.* FROM toppings t 
                JOIN cart_item_toppings cit ON t.id = cit.topping_id 
                WHERE cit.cart_id = :cart_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cart_id' => $cartId]);
        return $stmt->fetchAll();
    }

    public function addToppings($cartId, $toppingIds)
    {
        if (empty($toppingIds)) return true;
        
        $sql = "INSERT INTO cart_item_toppings (cart_id, topping_id) VALUES (:cart_id, :topping_id)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($toppingIds as $toppingId) {
            $stmt->execute([
                ':cart_id' => $cartId,
                ':topping_id' => $toppingId
            ]);
        }
        return true;
    }

    public function clearToppings($cartId)
    {
        $sql = "DELETE FROM cart_item_toppings WHERE cart_id = :cart_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':cart_id' => $cartId]);
    }

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function countItems($userId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn();
    }
}
