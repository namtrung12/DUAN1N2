<?php

class Order extends BaseModel
{
    protected $table = 'orders';

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, address_id, coupon_id, shipping_zone_id, subtotal, shipping_fee, discount, total, payment_method, note) 
                VALUES (:user_id, :address_id, :coupon_id, :shipping_zone_id, :subtotal, :shipping_fee, :discount, :total, :payment_method, :note)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':address_id' => $data['address_id'],
            ':coupon_id' => $data['coupon_id'] ?? null,
            ':shipping_zone_id' => $data['shipping_zone_id'] ?? null,
            ':subtotal' => $data['subtotal'],
            ':shipping_fee' => $data['shipping_fee'],
            ':discount' => $data['discount'],
            ':total' => $data['total'],
            ':payment_method' => $data['payment_method'],
            ':note' => $data['note'] ? htmlspecialchars($data['note'], ENT_QUOTES, 'UTF-8') : null
        ]);
        return $this->pdo->lastInsertId();
    }

    public function getById($id)
    {
        $sql = "SELECT o.*, a.detail as address_detail, a.province, a.district, a.ward, a.receiver_name, a.phone as address_phone 
                FROM {$this->table} o 
                LEFT JOIN addresses a ON o.address_id = a.id 
                WHERE o.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getItems($orderId)
    {
        $sql = "SELECT oi.*, p.name as product_name, p.image, ps.size_id, s.name as size_name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                JOIN product_sizes ps ON oi.product_size_id = ps.id 
                JOIN sizes s ON ps.size_id = s.id 
                WHERE oi.order_id = :order_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getItemToppings($orderItemId)
    {
        $sql = "SELECT oit.*, t.name as topping_name 
                FROM order_item_toppings oit 
                JOIN toppings t ON oit.topping_id = t.id 
                WHERE oit.order_item_id = :order_item_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':order_item_id' => $orderItemId]);
        return $stmt->fetchAll();
    }

    public function addItem($orderId, $productId, $productSizeId, $quantity, $unitPrice, $totalPrice, $iceLevel = 100, $sugarLevel = 100)
    {
        $sql = "INSERT INTO order_items (order_id, product_id, product_size_id, quantity, unit_price, total_price, ice_level, sugar_level) 
                VALUES (:order_id, :product_id, :product_size_id, :quantity, :unit_price, :total_price, :ice_level, :sugar_level)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':product_size_id' => $productSizeId,
            ':quantity' => $quantity,
            ':unit_price' => $unitPrice,
            ':total_price' => $totalPrice,
            ':ice_level' => $iceLevel,
            ':sugar_level' => $sugarLevel
        ]);
        return $this->pdo->lastInsertId();
    }

    public function addItemTopping($orderItemId, $toppingId, $price)
    {
        $sql = "INSERT INTO order_item_toppings (order_item_id, topping_id, price) 
                VALUES (:order_item_id, :topping_id, :price)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':order_item_id' => $orderItemId,
            ':topping_id' => $toppingId,
            ':price' => $price
        ]);
    }

    public function clearCart($userId, $cartIds = null)
    {
        if ($cartIds === null) {
            // Xóa toàn bộ giỏ hàng (cho tương thích ngược)
            $sql = "DELETE FROM cart WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } else {
            // Xóa chỉ các items đã chỉ định
            if (empty($cartIds)) {
                return true;
            }
            $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
            $sql = "DELETE FROM cart WHERE user_id = ? AND id IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(array_merge([$userId], $cartIds));
        }
    }

    public function getAllOrders()
    {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getRecentOrders($limit = 10, $offset = 0)
    {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByStatus($status)
    {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.status = :status 
                ORDER BY o.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function updateStatus($orderId, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $orderId, ':status' => $status]);
    }

    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        $sql = "UPDATE {$this->table} SET payment_status = :payment_status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $orderId, ':payment_status' => $paymentStatus]);
    }

    public function updatePaymentMethod($orderId, $paymentMethod)
    {
        $sql = "UPDATE {$this->table} SET payment_method = :payment_method, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $orderId, ':payment_method' => $paymentMethod]);
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    public function getTotalRevenue()
    {
        $sql = "SELECT SUM(total) FROM {$this->table} WHERE status IN ('completed', 'delivered')";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn() ?? 0;
    }

    public function getRecent($limit = 5)
    {
        $sql = "SELECT o.*, u.name as user_name FROM {$this->table} o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = :status";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchColumn();
    }
}
