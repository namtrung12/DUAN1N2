<?php

class Address extends BaseModel
{
    protected string $collection = 'addresses';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getByUserId(int $userId): array
    {
        $addresses = [];
        foreach ($this->all() as $address) {
            if ((int)$address['user_id'] === $userId) {
                $addresses[] = $address;
            }
        }
        usort($addresses, function ($a, $b) {
            return (int)$b['is_default'] <=> (int)$a['is_default'];
        });
        return $addresses;
    }

    public function createAddress(array $payload): array
    {
        return $this->create($payload);
    }

    public function updateAddress(int $id, array $payload): ?array
    {
        return $this->update($id, $payload);
    }

    public function deleteAddress(int $id): bool
    {
        return $this->delete($id);
    }

    public function setDefault(int $userId, int $addressId): void
    {
        $data = $this->all();
        foreach ($data as $index => $address) {
            if ((int)$address['user_id'] === $userId) {
                $data[$index]['is_default'] = ((int)$address['id'] === $addressId) ? 1 : 0;
            }
        }
        $this->saveAll($data);
    }
}
