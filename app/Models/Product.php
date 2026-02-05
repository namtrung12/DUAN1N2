<?php

class Product extends BaseModel
{
    protected string $collection = 'products';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getAll(): array
    {
        $products = $this->all();
        $categories = $this->store->read('categories');
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat['name'];
        }
        foreach ($products as &$product) {
            $product['category_name'] = $categoryMap[$product['category_id']] ?? 'N/A';
        }
        return $products;
    }

    public function findById(int $id): ?array
    {
        $product = parent::findById($id);
        if (!$product) {
            return null;
        }
        $categories = $this->store->read('categories');
        foreach ($categories as $cat) {
            if ((int)$cat['id'] === (int)$product['category_id']) {
                $product['category_name'] = $cat['name'];
                break;
            }
        }
        return $product;
    }

    public function getSizes(int $productId): array
    {
        $product = $this->findById($productId);
        if (!$product) {
            return [];
        }
        $sizeOptions = $this->store->read('sizes');
        $sizeMap = [];
        foreach ($sizeOptions as $size) {
            $sizeMap[$size['id']] = $size['name'];
        }
        $sizes = $product['sizes'] ?? [];
        $result = [];
        foreach ($sizes as $size) {
            $result[] = [
                'id' => $size['id'] ?? null,
                'size_id' => $size['size_id'],
                'size_name' => $sizeMap[$size['size_id']] ?? '',
                'price' => $size['price']
            ];
        }
        return $result;
    }

    public function getAllSizes(): array
    {
        return $this->store->read('sizes');
    }

    public function getToppings(int $productId): array
    {
        $product = $this->findById($productId);
        if (!$product) {
            return [];
        }
        $ids = $product['topping_ids'] ?? [];
        $toppings = $this->store->read('toppings');
        $result = [];
        foreach ($toppings as $topping) {
            if (in_array((int)$topping['id'], $ids, true)) {
                $result[] = $topping;
            }
        }
        return $result;
    }

    public function createProduct(array $payload): array
    {
        $payload['sizes'] = $this->normalizeSizes($payload['sizes'] ?? []);
        $payload['topping_ids'] = $payload['topping_ids'] ?? [];
        return $this->create($payload);
    }

    public function updateProduct(int $id, array $payload): ?array
    {
        if (isset($payload['sizes'])) {
            $payload['sizes'] = $this->normalizeSizes($payload['sizes']);
        }
        if (isset($payload['topping_ids'])) {
            $payload['topping_ids'] = $payload['topping_ids'];
        }
        return $this->update($id, $payload);
    }

    public function deleteMany(array $ids): int
    {
        $data = $this->all();
        $ids = array_map('intval', $ids);
        $filtered = [];
        $count = 0;
        foreach ($data as $item) {
            if (in_array((int)$item['id'], $ids, true)) {
                $count++;
                continue;
            }
            $filtered[] = $item;
        }
        if ($count > 0) {
            $this->saveAll($filtered);
        }
        return $count;
    }

    public function search(string $keyword): array
    {
        $keyword = strtolower($keyword);
        $products = $this->getAll();
        $result = [];
        foreach ($products as $product) {
            $name = strtolower($product['name']);
            if (strpos($name, $keyword) !== false) {
                $result[] = $product;
            }
        }
        return $result;
    }

    private function normalizeSizes(array $sizes): array
    {
        $nextId = $this->nextProductSizeId();
        $normalized = [];
        foreach ($sizes as $size) {
            $id = (int)($size['id'] ?? 0);
            if ($id <= 0) {
                $id = $nextId++;
            }
            $normalized[] = [
                'id' => $id,
                'size_id' => (int)$size['size_id'],
                'price' => (int)$size['price']
            ];
        }
        return $normalized;
    }

    private function nextProductSizeId(): int
    {
        $products = $this->all();
        $maxId = 0;
        foreach ($products as $product) {
            foreach (($product['sizes'] ?? []) as $size) {
                $maxId = max($maxId, (int)($size['id'] ?? 0));
            }
        }
        return $maxId + 1;
    }
}
