<?php

class Order extends BaseModel
{
    protected string $collection = 'orders';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getAll(?string $status = null): array
    {
        $orders = $this->all();
        if ($status) {
            $filtered = [];
            foreach ($orders as $order) {
                if ($order['status'] === $status) {
                    $filtered[] = $order;
                }
            }
            $orders = $filtered;
        }
        usort($orders, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        return $orders;
    }

    public function getByUserId(int $userId): array
    {
        $orders = [];
        foreach ($this->all() as $order) {
            if ((int)$order['user_id'] === $userId) {
                $orders[] = $order;
            }
        }
        usort($orders, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        return $orders;
    }

    public function updateStatus(int $id, string $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }

    public function cancel(int $id, string $reason): ?array
    {
        return $this->update($id, [
            'status' => 'cancelled',
            'cancel_reason' => $reason
        ]);
    }

    public function createOrder(array $payload): array
    {
        return $this->create($payload);
    }
}
