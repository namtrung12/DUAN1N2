<?php

class Settings
{
    private DataStore $store;

    public function __construct(?DataStore $store = null)
    {
        $this->store = $store ?? get_store();
    }

    public function all(): array
    {
        $data = $this->store->read('settings');
        return is_array($data) ? $data : [];
    }

    public function update(array $payload): array
    {
        $current = $this->all();
        $merged = array_merge($current, $payload);
        $this->store->write('settings', $merged);
        return $merged;
    }
}
