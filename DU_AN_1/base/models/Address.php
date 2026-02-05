<?php

class Address extends BaseModel
{
    protected $table = 'addresses';

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
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

    public function create($userId, $data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, label, receiver_name, phone, province, district, ward, detail, is_default) 
                VALUES (:user_id, :label, :receiver_name, :phone, :province, :district, :ward, :detail, :is_default)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':label' => htmlspecialchars($data['label'], ENT_QUOTES, 'UTF-8'),
            ':receiver_name' => htmlspecialchars($data['receiver_name'], ENT_QUOTES, 'UTF-8'),
            ':phone' => htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8'),
            ':province' => htmlspecialchars($data['province'], ENT_QUOTES, 'UTF-8'),
            ':district' => htmlspecialchars($data['district'], ENT_QUOTES, 'UTF-8'),
            ':ward' => htmlspecialchars($data['ward'], ENT_QUOTES, 'UTF-8'),
            ':detail' => htmlspecialchars($data['detail'], ENT_QUOTES, 'UTF-8'),
            ':is_default' => $data['is_default'] ?? 0
        ]);
    }

    public function update($id, $userId, $data)
    {
        $sql = "UPDATE {$this->table} SET label = :label, receiver_name = :receiver_name, phone = :phone, 
                province = :province, district = :district, ward = :ward, detail = :detail, is_default = :is_default 
                WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
            ':label' => htmlspecialchars($data['label'], ENT_QUOTES, 'UTF-8'),
            ':receiver_name' => htmlspecialchars($data['receiver_name'], ENT_QUOTES, 'UTF-8'),
            ':phone' => htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8'),
            ':province' => htmlspecialchars($data['province'], ENT_QUOTES, 'UTF-8'),
            ':district' => htmlspecialchars($data['district'], ENT_QUOTES, 'UTF-8'),
            ':ward' => htmlspecialchars($data['ward'], ENT_QUOTES, 'UTF-8'),
            ':detail' => htmlspecialchars($data['detail'], ENT_QUOTES, 'UTF-8'),
            ':is_default' => $data['is_default'] ?? 0
        ]);
    }

    public function delete($id, $userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    public function setDefault($id, $userId)
    {
        $this->pdo->beginTransaction();
        try {
            $sql1 = "UPDATE {$this->table} SET is_default = 0 WHERE user_id = :user_id";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->execute([':user_id' => $userId]);

            $sql2 = "UPDATE {$this->table} SET is_default = 1 WHERE id = :id AND user_id = :user_id";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([':id' => $id, ':user_id' => $userId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getDefault($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND is_default = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }
}
