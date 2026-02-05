<?php

class Topping extends BaseModel
{
    protected $table = 'toppings';

    public function getAll()
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

    public function getByIds($ids)
    {
        if (empty($ids)) return [];
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE id IN ($placeholders) AND status = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    public function create($name, $price, $status = 1)
    {
        $sql = "INSERT INTO {$this->table} (name, price, status) VALUES (:name, :price, :status)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            ':price' => $price,
            ':status' => $status
        ]);
    }

    public function update($id, $name, $price, $status)
    {
        $sql = "UPDATE {$this->table} SET name = :name, price = :price, status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            ':price' => $price,
            ':status' => $status
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
