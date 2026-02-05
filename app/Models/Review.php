<?php

class Review extends BaseModel
{
    protected string $collection = 'reviews';

    public function __construct(?DataStore $store = null)
    {
        parent::__construct($store);
    }

    public function getAll(?string $status = null): array
    {
        $reviews = $this->all();
        if ($status !== null && $status !== '') {
            $filtered = [];
            foreach ($reviews as $review) {
                if ((string)$review['status'] === (string)$status) {
                    $filtered[] = $review;
                }
            }
            $reviews = $filtered;
        }
        usort($reviews, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        return $reviews;
    }

    public function getByProductId(int $productId, ?int $status = 1): array
    {
        $reviews = [];
        foreach ($this->all() as $review) {
            if ((int)($review['product_id'] ?? 0) !== $productId) {
                continue;
            }
            if ($status !== null && (int)($review['status'] ?? 0) !== (int)$status) {
                continue;
            }
            $reviews[] = $review;
        }
        usort($reviews, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });
        return $reviews;
    }

    public function createReview(array $payload): array
    {
        $payload['created_at'] = $payload['created_at'] ?? date('Y-m-d H:i:s');
        $payload['status'] = $payload['status'] ?? 1;
        return $this->create($payload);
    }

    public function hasUserReviewed(int $userId, int $productId, int $orderId): bool
    {
        foreach ($this->all() as $review) {
            if ((int)($review['user_id'] ?? 0) === $userId
                && (int)($review['product_id'] ?? 0) === $productId
                && (int)($review['order_id'] ?? 0) === $orderId) {
                return true;
            }
        }
        return false;
    }

    public function updateStatus(int $id, int $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }
}
