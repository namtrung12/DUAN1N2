<?php
// API endpoint for search suggestions
require_once '../configs/env.php';

header('Content-Type: application/json');

$keyword = $_GET['q'] ?? '';

if (empty($keyword) || strlen($keyword) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
        DB_USERNAME,
        DB_PASSWORD,
        DB_OPTIONS
    );

    $sql = "SELECT p.id, p.name, p.image, c.name as category_name,
            (SELECT MIN(price) FROM product_sizes WHERE product_id = p.id) as min_price
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 1 
            AND (p.name LIKE :keyword OR p.description LIKE :keyword OR c.name LIKE :keyword)
            ORDER BY p.name ASC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':keyword' => '%' . $keyword . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
