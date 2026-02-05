<?php

class User extends BaseModel
{
    protected string $collection = 'users';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getAll(): array
    {
        return $this->all();
    }

    public function findByEmail(string $email): ?array
    {
        foreach ($this->all() as $user) {
            if (strtolower($user['email']) === strtolower($email)) {
                return $user;
            }
        }
        return null;
    }

    public function createUser(array $payload): ?array
    {
        if ($this->findByEmail($payload['email'])) {
            return null;
        }
        $payload['created_at'] = date('Y-m-d H:i:s');
        $payload['is_active'] = $payload['is_active'] ?? 1;
        $payload['role_id'] = $payload['role_id'] ?? 1;
        return $this->create($payload);
    }

    public function updateRole(int $id, int $roleId): ?array
    {
        return $this->update($id, ['role_id' => $roleId]);
    }

    public function updateUser(int $id, array $payload): ?array
    {
        return $this->update($id, $payload);
    }

    public function lockUsers(array $ids): int
    {
        return $this->setActive($ids, 0);
    }

    public function unlockUsers(array $ids): int
    {
        return $this->setActive($ids, 1);
    }

    private function setActive(array $ids, int $status): int
    {
        $ids = array_map('intval', $ids);
        $data = $this->all();
        $count = 0;
        foreach ($data as $index => $user) {
            if (in_array((int)$user['id'], $ids, true)) {
                $data[$index]['is_active'] = $status;
                $count++;
            }
        }
        if ($count > 0) {
            $this->saveAll($data);
        }
        return $count;
    }
}
