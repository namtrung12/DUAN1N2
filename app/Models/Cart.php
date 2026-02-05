<?php

class Cart extends BaseModel
{
    protected string $collection = 'cart';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getByUserId(int $userId): array
    {
        $all = $this->all();
        foreach ($all as $cart) {
            if ((int)$cart['user_id'] === $userId) {
                return $cart;
            }
        }
        $cart = [
            'user_id' => $userId,
            'items' => []
        ];
        $all[] = $cart;
        $this->saveAll($all);
        return $cart;
    }

    public function countItems(int $userId): int
    {
        $cart = $this->getByUserId($userId);
        $count = 0;
        foreach ($cart['items'] as $item) {
            $count += (int)$item['quantity'];
        }
        return $count;
    }

    public function addItem(int $userId, array $item): void
    {
        $all = $this->all();
        $found = false;
        foreach ($all as $cIndex => $cart) {
            if ((int)$cart['user_id'] === $userId) {
                foreach ($cart['items'] as $iIndex => $existing) {
                    if ((int)$existing['product_id'] === (int)$item['product_id']
                        && (int)$existing['size_id'] === (int)$item['size_id']) {
                        $cart['items'][$iIndex]['quantity'] += (int)$item['quantity'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $item['id'] = $this->nextItemId($cart['items']);
                    $cart['items'][] = $item;
                }
                $all[$cIndex] = $cart;
                $this->saveAll($all);
                return;
            }
        }
        $item['id'] = 1;
        $all[] = [
            'user_id' => $userId,
            'items' => [$item]
        ];
        $this->saveAll($all);
    }

    public function updateQuantity(int $userId, int $itemId, int $quantity): void
    {
        $all = $this->all();
        foreach ($all as $cIndex => $cart) {
            if ((int)$cart['user_id'] === $userId) {
                foreach ($cart['items'] as $iIndex => $item) {
                    if ((int)$item['id'] === $itemId) {
                        $cart['items'][$iIndex]['quantity'] = max(1, $quantity);
                        $all[$cIndex] = $cart;
                        $this->saveAll($all);
                        return;
                    }
                }
            }
        }
    }

    public function removeItem(int $userId, int $itemId): void
    {
        $all = $this->all();
        foreach ($all as $cIndex => $cart) {
            if ((int)$cart['user_id'] === $userId) {
                $items = [];
                foreach ($cart['items'] as $item) {
                    if ((int)$item['id'] === $itemId) {
                        continue;
                    }
                    $items[] = $item;
                }
                $cart['items'] = $items;
                $all[$cIndex] = $cart;
                $this->saveAll($all);
                return;
            }
        }
    }

    public function clear(int $userId): void
    {
        $all = $this->all();
        foreach ($all as $cIndex => $cart) {
            if ((int)$cart['user_id'] === $userId) {
                $cart['items'] = [];
                $all[$cIndex] = $cart;
                $this->saveAll($all);
                return;
            }
        }
    }

    private function nextItemId(array $items): int
    {
        $maxId = 0;
        foreach ($items as $item) {
            $maxId = max($maxId, (int)($item['id'] ?? 0));
        }
        return $maxId + 1;
    }
}
