<?php

class Notification extends BaseModel
{
    protected string $collection = 'notifications';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getByUserId(int $userId, ?int $limit = null): array
    {
        $all = $this->all();
        $filtered = [];
        foreach ($all as $notif) {
            if ((int)$notif['user_id'] === $userId) {
                $filtered[] = $notif;
            }
        }
        usort($filtered, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        if ($limit !== null) {
            return array_slice($filtered, 0, $limit);
        }
        return $filtered;
    }

    public function getUnreadCount(int $userId): int
    {
        $count = 0;
        foreach ($this->all() as $notif) {
            if ((int)$notif['user_id'] === $userId && empty($notif['is_read'])) {
                $count++;
            }
        }
        return $count;
    }

    public function markRead(int $id): void
    {
        $this->update($id, ['is_read' => 1]);
    }

    public function markAllRead(int $userId): void
    {
        $data = $this->all();
        foreach ($data as $index => $notif) {
            if ((int)$notif['user_id'] === $userId) {
                $data[$index]['is_read'] = 1;
            }
        }
        $this->saveAll($data);
    }
}
