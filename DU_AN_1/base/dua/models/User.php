<?php

class User extends BaseModel
{
    protected $table = 'users';

    public function register($name, $email, $phone, $password)
    {
        $sql = "INSERT INTO {$this->table} (name, email, phone, password, role_id) VALUES (:name, :email, :phone, :password, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            ':email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            ':phone' => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
            ':password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
        return $this->pdo->lastInsertId();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $name, $phone)
    {
        $sql = "UPDATE {$this->table} SET name = :name, phone = :phone WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            ':phone' => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8')
        ]);
    }

    public function updatePassword($id, $newPassword)
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAll()
    {
        $sql = "SELECT u.*, r.name as role_name FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.id 
                ORDER BY u.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    public function updateRole($userId, $roleId)
    {
        $sql = "UPDATE {$this->table} SET role_id = :role_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':role_id' => $roleId,
            ':id' => $userId
        ]);
    }

    public function lockMultiple($userIds)
    {
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $sql = "UPDATE {$this->table} SET is_active = 0 WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($userIds);
        return $stmt->rowCount();
    }
}
