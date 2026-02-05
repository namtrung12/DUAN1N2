<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if ($q === '' || strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$productModel = new Product(get_store());
$products = $productModel->search($q);

$result = [];
foreach (array_slice($products, 0, 8) as $product) {
    $sizes = $productModel->getSizes((int)$product['id']);
    $minPrice = 0;
    if (!empty($sizes)) {
        $minPrice = min(array_column($sizes, 'price'));
    }
    $result[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'image' => $product['image'] ?? 'placeholder.svg',
        'category_name' => $product['category_name'] ?? '',
        'min_price' => $minPrice
    ];
}

echo json_encode($result);
