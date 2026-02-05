<?php

class Category extends BaseModel
{
    protected string $collection = 'categories';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getAll(): array
    {
        return $this->all();
    }

    public function createCategory(string $name, string $slug): array
    {
        return $this->create([
            'name' => $name,
            'slug' => $slug
        ]);
    }

    public function updateCategory(int $id, string $name, string $slug): ?array
    {
        return $this->update($id, [
            'name' => $name,
            'slug' => $slug
        ]);
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
}
