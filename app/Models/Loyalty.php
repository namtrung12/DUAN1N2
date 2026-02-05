<?php

class Loyalty extends BaseModel
{
    protected string $collection = 'loyalty';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getByUserId(int $userId): array
    {
        $all = $this->all();
        foreach ($all as $row) {
            if ((int)$row['user_id'] === $userId) {
                return $row;
            }
        }
        $row = [
            'user_id' => $userId,
            'total_points' => 0,
            'lifetime_points' => 0,
            'level' => 'new',
            'rewards' => [],
            'transactions' => []
        ];
        $all[] = $row;
        $this->saveAll($all);
        return $row;
    }

    public function addTransaction(int $userId, string $type, int $points, string $description): void
    {
        $all = $this->all();
        foreach ($all as $index => $row) {
            if ((int)$row['user_id'] === $userId) {
                $row['transactions'][] = [
                    'type' => $type,
                    'points' => $points,
                    'description' => $description,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if ($type === 'earn') {
                    $row['total_points'] += $points;
                    $row['lifetime_points'] += $points;
                } elseif ($type === 'redeem') {
                    $row['total_points'] = max(0, $row['total_points'] - $points);
                }
                $row['level'] = $this->calculateLevel($row['lifetime_points']);
                $all[$index] = $row;
                $this->saveAll($all);
                return;
            }
        }
    }

    public function addReward(int $userId, array $reward): void
    {
        $all = $this->all();
        foreach ($all as $index => $row) {
            if ((int)$row['user_id'] === $userId) {
                $row['rewards'][] = $reward;
                $all[$index] = $row;
                $this->saveAll($all);
                return;
            }
        }
    }

    private function calculateLevel(int $lifetimePoints): string
    {
        if ($lifetimePoints >= 2000) {
            return 'diamond';
        }
        if ($lifetimePoints >= 1000) {
            return 'gold';
        }
        if ($lifetimePoints >= 500) {
            return 'silver';
        }
        if ($lifetimePoints >= 200) {
            return 'bronze';
        }
        return 'new';
    }
}
