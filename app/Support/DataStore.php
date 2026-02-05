<?php

class DataStore
{
    private string $baseDir;

    public function __construct(string $baseDir)
    {
        $this->baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);
        if (!is_dir($this->baseDir)) {
            mkdir($this->baseDir, 0777, true);
        }
    }

    public function read(string $collection)
    {
        $path = $this->path($collection);
        if (!file_exists($path)) {
            $seed = $this->seedData($collection);
            $this->write($collection, $seed);
            return $seed;
        }

        $raw = file_get_contents($path);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return [];
        }
        return $data;
    }

    public function write(string $collection, $data): void
    {
        $path = $this->path($collection);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $json = "[]";
        }
        file_put_contents($path, $json, LOCK_EX);
    }

    public function nextId(string $collection): int
    {
        $data = $this->read($collection);
        if (!is_array($data) || empty($data)) {
            return 1;
        }
        $ids = [];
        foreach ($data as $item) {
            if (is_array($item) && isset($item['id'])) {
                $ids[] = (int)$item['id'];
            }
        }
        return empty($ids) ? 1 : (max($ids) + 1);
    }

    private function path(string $collection): string
    {
        return $this->baseDir . DIRECTORY_SEPARATOR . $collection . '.json';
    }

    private function seedData(string $collection)
    {
        $now = date('Y-m-d H:i:s');
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $userPassword = password_hash('123456', PASSWORD_DEFAULT);

        $seeds = [
            'settings' => [
                'site_name' => 'Chill Drink',
                'site_logo' => '',
                'contact_email' => 'support@chilldrink.vn',
                'contact_phone' => '1900 0000',
                'site_address' => '123 ABC Street, District 1, HCMC',
                'banner_1' => '',
                'banner_2' => '',
                'banner_3' => ''
            ],
            'categories' => [
                ['id' => 1, 'name' => 'Trà', 'slug' => 'tra'],
                ['id' => 2, 'name' => 'Trà sữa', 'slug' => 'tra-sua'],
                ['id' => 3, 'name' => 'Cà phê', 'slug' => 'ca-phe'],
                ['id' => 4, 'name' => 'Sinh tố', 'slug' => 'sinh-to'],
                ['id' => 5, 'name' => 'Nước ép', 'slug' => 'nuoc-ep']
            ],
            'sizes' => [
                ['id' => 1, 'name' => 'S'],
                ['id' => 2, 'name' => 'M'],
                ['id' => 3, 'name' => 'L']
            ],
            'toppings' => [
                ['id' => 1, 'name' => 'Trân châu đen', 'price' => 5000, 'status' => 1],
                ['id' => 2, 'name' => 'Thạch rau câu', 'price' => 4000, 'status' => 1],
                ['id' => 3, 'name' => 'Kem cheese', 'price' => 8000, 'status' => 1]
            ],
            'products' => [
                [
                    'id' => 1,
                    'name' => 'Trà sữa trân châu',
                    'slug' => 'tra-sua-tran-chau',
                    'category_id' => 2,
                    'description' => 'Món trà sữa ngọt ngào, dai sườn.',
                    'image' => 'placeholder.svg',
                    'status' => 1,
                    'sizes' => [
                        ['id' => 1, 'size_id' => 1, 'price' => 25000],
                        ['id' => 2, 'size_id' => 2, 'price' => 30000],
                        ['id' => 3, 'size_id' => 3, 'price' => 35000]
                    ],
                    'topping_ids' => [1, 2]
                ],
                [
                    'id' => 2,
                    'name' => 'Cà phê sữa',
                    'slug' => 'ca-phe-sua',
                    'category_id' => 3,
                    'description' => 'Cà phê đậm đà, thêm sữa.',
                    'image' => 'placeholder.svg',
                    'status' => 1,
                    'sizes' => [
                        ['id' => 4, 'size_id' => 1, 'price' => 20000],
                        ['id' => 5, 'size_id' => 2, 'price' => 25000]
                    ],
                    'topping_ids' => []
                ],
                [
                    'id' => 3,
                    'name' => 'Trà đào cam sả',
                    'slug' => 'tra-dao-cam-sa',
                    'category_id' => 1,
                    'description' => 'Trà đào thanh mát với cam sả.',
                    'image' => 'placeholder.svg',
                    'status' => 1,
                    'sizes' => [
                        ['id' => 6, 'size_id' => 2, 'price' => 28000],
                        ['id' => 7, 'size_id' => 3, 'price' => 32000]
                    ],
                    'topping_ids' => [2]
                ]
            ],
            'coupons' => [
                [
                    'id' => 1,
                    'code' => 'WELCOME10',
                    'type' => 'percent',
                    'value' => 10,
                    'max_discount' => 50000,
                    'min_order' => 50000,
                    'usage_limit' => 0,
                    'used_count' => 0,
                    'required_rank' => '',
                    'point_cost' => 0,
                    'is_redeemable' => 0,
                    'status' => 1,
                    'description' => 'Giam 10% cho don hang dau tien',
                    'starts_at' => '',
                    'expires_at' => '',
                    'max_redemptions' => 0,
                    'redemption_count' => 0
                ],
                [
                    'id' => 2,
                    'code' => 'LOYALTY50',
                    'type' => 'fixed',
                    'value' => 50000,
                    'max_discount' => 0,
                    'min_order' => 150000,
                    'usage_limit' => 50,
                    'used_count' => 3,
                    'required_rank' => 'silver',
                    'point_cost' => 120,
                    'is_redeemable' => 1,
                    'status' => 1,
                    'description' => 'Doi bang diem tich luy',
                    'starts_at' => '',
                    'expires_at' => '',
                    'max_redemptions' => 50,
                    'redemption_count' => 3
                ]
            ],
            'users' => [
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'email' => 'admin@chilldrink.vn',
                    'phone' => '0900000000',
                    'role_id' => 2,
                    'is_active' => 1,
                    'avatar' => '',
                    'password' => $adminPassword,
                    'created_at' => $now
                ],
                [
                    'id' => 2,
                    'name' => 'Customer',
                    'email' => 'user@chilldrink.vn',
                    'phone' => '0911111111',
                    'role_id' => 1,
                    'is_active' => 1,
                    'avatar' => '',
                    'password' => $userPassword,
                    'created_at' => $now
                ]
            ],
            'orders' => [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'user_name' => 'Customer',
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'items' => [
                        [
                            'product_id' => 1,
                            'product_name' => 'Tra sua tran chau',
                            'image' => 'placeholder.svg',
                            'size_name' => 'M',
                            'quantity' => 1,
                            'unit_price' => 30000,
                            'total_price' => 30000,
                            'ice_level' => 100,
                            'sugar_level' => 100,
                            'toppings' => [
                                ['topping_name' => 'Trân châu đen']
                            ]
                        ]
                    ],
                    'subtotal' => 30000,
                    'shipping_fee' => 15000,
                    'discount' => 0,
                    'total' => 45000
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'user_name' => 'Customer',
                    'status' => 'completed',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'items' => [
                        [
                            'product_id' => 3,
                            'product_name' => 'Tra dao cam sa',
                            'image' => 'placeholder.svg',
                            'size_name' => 'L',
                            'quantity' => 2,
                            'unit_price' => 32000,
                            'total_price' => 64000,
                            'ice_level' => 70,
                            'sugar_level' => 70,
                            'toppings' => []
                        ]
                    ],
                    'subtotal' => 64000,
                    'shipping_fee' => 0,
                    'discount' => 0,
                    'total' => 64000
                ]
            ],
            'notifications' => [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'type' => 'order_delivering',
                    'title' => 'Don hang dang giao',
                    'message' => 'Don hang #0001 dang duoc giao.',
                    'is_read' => 0,
                    'order_id' => 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'type' => 'order_cancelled',
                    'title' => 'Don hang bi huy',
                    'message' => 'Don hang #0003 da bi huy.',
                    'is_read' => 1,
                    'order_id' => null,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ]
            ],
            'addresses' => [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'label' => 'Nha',
                    'receiver_name' => 'Customer',
                    'phone' => '0911111111',
                    'province' => 'HCMC',
                    'district' => 'District 1',
                    'ward' => 'Ward 1',
                    'detail' => '123 ABC Street',
                    'is_default' => 1
                ]
            ],
            'loyalty' => [
                [
                    'user_id' => 2,
                    'total_points' => 150,
                    'lifetime_points' => 300,
                    'level' => 'silver',
                    'rewards' => [
                        [
                            'reward_name' => 'Coupon LOYALTY50',
                            'code' => 'LOYALTY50',
                            'is_used' => 0,
                            'expires_at' => date('Y-m-d', strtotime('+30 days'))
                        ]
                    ],
                    'transactions' => [
                        [
                            'type' => 'earn',
                            'points' => 100,
                            'description' => 'Tich diem tu don hang #0002',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                        ],
                        [
                            'type' => 'redeem',
                            'points' => 50,
                            'description' => 'Doi coupon LOYALTY50',
                            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                        ]
                    ]
                ]
            ],
            'cart' => [
                [
                    'user_id' => 2,
                    'items' => [
                        [
                            'id' => 1,
                            'product_id' => 1,
                            'product_name' => 'Tra sua tran chau',
                            'image' => 'placeholder.svg',
                            'size_id' => 2,
                            'size_name' => 'M',
                            'quantity' => 1,
                            'unit_price' => 30000,
                            'toppings' => [
                                ['topping_name' => 'Trân châu đen', 'price' => 5000]
                            ]
                        ]
                    ]
                ]
            ],
            'reviews' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'product_name' => 'Tra sua tran chau',
                    'user_id' => 2,
                    'user_name' => 'Customer',
                    'user_email' => 'user@chilldrink.vn',
                    'rating' => 5,
                    'comment' => 'Rat ngon!',
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
                ]
            ],
            'coupon_redemptions' => []
        ];

        return $seeds[$collection] ?? [];
    }
}
