<?php

class BaseModel
{
    protected DataStore $store;
    protected string $collection;

    public function __construct(?DataStore $store = null)
    {
        $this->store = $store ?? get_store();
    }

    protected function all(): array
    {
        $data = $this->store->read($this->collection);
        return is_array($data) ? $data : [];
    }

    protected function saveAll(array $data): void
    {
        $this->store->write($this->collection, array_values($data));
    }

    public function findById(int $id): ?array
    {
        foreach ($this->all() as $item) {
            if ((int)($item['id'] ?? 0) === $id) {
                return $item;
            }
        }
        return null;
    }

    public function create(array $payload): array
    {
        $data = $this->all();
        $payload['id'] = $this->store->nextId($this->collection);
        $data[] = $payload;
        $this->saveAll($data);
        return $payload;
    }

    public function update(int $id, array $payload): ?array
    {
        $data = $this->all();
        foreach ($data as $index => $item) {
            if ((int)($item['id'] ?? 0) === $id) {
                $data[$index] = array_merge($item, $payload);
                $this->saveAll($data);
                return $data[$index];
            }
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $data = $this->all();
        $changed = false;
        $filtered = [];
        foreach ($data as $item) {
            if ((int)($item['id'] ?? 0) === $id) {
                $changed = true;
                continue;
            }
            $filtered[] = $item;
        }
        if ($changed) {
            $this->saveAll($filtered);
        }
        return $changed;
    }
}
