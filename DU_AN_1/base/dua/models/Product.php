<?php

class Product extends BaseModel
{
    protected $table = 'products';

    public function getAll()
    {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function search($keyword)
    {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND p.deleted_at IS NULL
                AND (p.name LIKE :keyword OR p.description LIKE :keyword OR c.name LIKE :keyword)
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id AND p.status = 1 AND p.deleted_at IS NULL LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByCategory($categoryId)
    {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = :category_id AND p.status = 1 AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    public function getSizes($productId)
    {
        $sql = "SELECT ps.*, s.name as size_name FROM product_sizes ps 
                JOIN sizes s ON ps.size_id = s.id 
                WHERE ps.product_id = :product_id 
                ORDER BY s.id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getToppings($productId)
    {
        $sql = "SELECT t.* FROM toppings t 
                JOIN product_toppings pt ON t.id = pt.topping_id 
                WHERE pt.product_id = :product_id AND t.status = 1 
                ORDER BY t.name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getProductSizeById($productSizeId)
    {
        $sql = "SELECT ps.*, s.name as size_name FROM product_sizes ps 
                JOIN sizes s ON ps.size_id = s.id 
                WHERE ps.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $productSizeId]);
        return $stmt->fetch();
    }

    public function getAllAdmin()
    {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function update($id, $name, $categoryId, $description, $status)
    {
        $sql = "UPDATE {$this->table} SET name = :name, category_id = :category_id, description = :description, status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            ':category_id' => $categoryId,
            ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            ':status' => $status
        ]);
    }

    public function updateWithImage($id, $name, $categoryId, $description, $image, $status)
    {
        $sql = "UPDATE {$this->table} SET name = :name, category_id = :category_id, description = :description, image = :image, status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            ':category_id' => $categoryId,
            ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            ':image' => $image,
            ':status' => $status
        ]);
    }

    public function delete($id)
    {
        // Soft delete - chỉ đánh dấu deleted_at
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function restore($id)
    {
        // Khôi phục sản phẩm đã xóa
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getDeleted()
    {
        // Lấy danh sách sản phẩm đã xóa
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NOT NULL
                ORDER BY p.deleted_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function forceDelete($id)
    {
        // Xóa cứng (vĩnh viễn) - chỉ dùng khi thực sự cần
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getAllSizes()
    {
        $sql = "SELECT * FROM sizes ORDER BY id ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getReviews($productId)
    {
        $sql = "SELECT r.*, u.name as user_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = :product_id 
                ORDER BY r.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getAverageRating($productId)
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                FROM reviews 
                WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetch();
    }
}
