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

    public function updateStatus(int $id, int $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }
}
