<?php

class ProductAdminController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($_SESSION['user']['role_id'] != 2 && $_SESSION['user']['role_id'] != 3) {
            $_SESSION['errors'] = ['auth' => 'Bạn không có quyền truy cập'];
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function index()
    {
        $products = $this->productModel->getAllAdmin();
        require_once PATH_VIEW . 'admin/products.php';
    }

    public function create()
    {
        $productModel = $this->productModel;
        $categories = $this->categoryModel->getAll();
        $allSizes = $this->productModel->getAllSizes();
        $toppingModel = new Topping();
        $allToppings = $toppingModel->getAll();
        
        require_once PATH_VIEW . 'admin/product-create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $categoryId = $_POST['category_id'] ?? 0;
        $description = $_POST['description'] ?? '';
        $status = isset($_POST['status']) ? 1 : 0;

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }

        // Handle image upload
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $fileType = $_FILES['image']['type'];
            $fileSize = $_FILES['image']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $errors['image'] = 'Định dạng file không hợp lệ. Chỉ chấp nhận PNG, JPG, WEBP';
            } elseif ($fileSize > 5 * 1024 * 1024) {
                $errors['image'] = 'Kích thước file quá lớn. Tối đa 5MB';
            } else {
                try {
                    $imagePath = upload_file('products', $_FILES['image']);
                } catch (Exception $e) {
                    $errors['image'] = 'Upload ảnh thất bại: ' . $e->getMessage();
                }
            }
        } else {
            $errors['image'] = 'Vui lòng chọn ảnh sản phẩm';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=admin-product-create');
            exit;
        }

        try {
            $this->productModel->pdo->beginTransaction();

            // Insert product
            $sql = "INSERT INTO products (name, category_id, description, image, status) 
                    VALUES (:name, :category_id, :description, :image, :status)";
            $stmt = $this->productModel->pdo->prepare($sql);
            $stmt->execute([
                ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                ':category_id' => $categoryId ?: null,
                ':description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                ':image' => $imagePath,
                ':status' => $status
            ]);
            
            $productId = $this->productModel->pdo->lastInsertId();

            // Insert sizes
            $sizes = $_POST['sizes'] ?? [];
            $prices = $_POST['prices'] ?? [];

            foreach ($sizes as $sizeId) {
                if (!empty($prices[$sizeId])) {
                    $sql = "INSERT INTO product_sizes (product_id, size_id, price) 
                            VALUES (:product_id, :size_id, :price)";
                    $stmt = $this->productModel->pdo->prepare($sql);
                    $stmt->execute([
                        ':product_id' => $productId,
                        ':size_id' => $sizeId,
                        ':price' => $prices[$sizeId]
                    ]);
                }
            }

            // Insert toppings
            $toppings = $_POST['toppings'] ?? [];
            foreach ($toppings as $toppingId) {
                $sql = "INSERT INTO product_toppings (product_id, topping_id) 
                        VALUES (:product_id, :topping_id)";
                $stmt = $this->productModel->pdo->prepare($sql);
                $stmt->execute([
                    ':product_id' => $productId,
                    ':topping_id' => $toppingId
                ]);
            }

            $this->productModel->pdo->commit();
            $_SESSION['success'] = 'Thêm sản phẩm thành công';
            header('Location: ' . BASE_URL . '?action=admin-products');
        } catch (Exception $e) {
            $this->productModel->pdo->rollBack();
            $_SESSION['errors'] = ['create' => 'Thêm sản phẩm thất bại: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '?action=admin-product-create');
        }
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $product = $this->productModel->getById($id);

        if (!$product) {
            $_SESSION['errors'] = ['product' => 'Sản phẩm không tồn tại'];
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $productModel = $this->productModel;
        $categories = $this->categoryModel->getAll();
        $sizes = $this->productModel->getSizes($id);
        $toppings = $this->productModel->getToppings($id);
        
        require_once PATH_VIEW . 'admin/product-edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $categoryId = $_POST['category_id'] ?? 0;
        $description = $_POST['description'] ?? '';
        $status = isset($_POST['status']) ? 1 : 0;
        $currentImage = $_POST['current_image'] ?? '';

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }

        // Handle image upload
        $imagePath = $currentImage;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $fileType = $_FILES['image']['type'];
            $fileSize = $_FILES['image']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $errors['image'] = 'Định dạng file không hợp lệ. Chỉ chấp nhận PNG, JPG, WEBP';
            } elseif ($fileSize > 5 * 1024 * 1024) {
                $errors['image'] = 'Kích thước file quá lớn. Tối đa 5MB';
            } else {
                try {
                    // Delete old image if exists
                    if (!empty($currentImage) && file_exists(PATH_ASSETS_UPLOADS . $currentImage)) {
                        unlink(PATH_ASSETS_UPLOADS . $currentImage);
                    }

                    // Upload new image
                    $imagePath = upload_file('products', $_FILES['image']);
                } catch (Exception $e) {
                    $errors['image'] = 'Upload ảnh thất bại: ' . $e->getMessage();
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $id);
            exit;
        }

        try {
            $this->productModel->pdo->beginTransaction();

           
            // Update product info
            $this->productModel->updateWithImage($id, $name, $categoryId, $description, $imagePath, $status);

            
            // Xử lý Size
            $sizes = $_POST['sizes'] ?? [];
            $prices = $_POST['prices'] ?? [];

            // 1. Lấy danh sách size hiện có trong DB của sản phẩm này
            $stmtExisting = $this->productModel->pdo->prepare("SELECT size_id FROM product_sizes WHERE product_id = :product_id");
            $stmtExisting->execute([':product_id' => $id]);
            $existingSizes = $stmtExisting->fetchAll(PDO::FETCH_COLUMN); 

            foreach ($sizes as $sizeId) {
                if (!empty($prices[$sizeId])) {
                    if (in_array($sizeId, $existingSizes)) {
                        // TRƯỜNG HỢP 1: Size đã có -> Cập nhật (Update)
                        $sql = "UPDATE product_sizes SET price = :price WHERE product_id = :product_id AND size_id = :size_id";
                        $stmt = $this->productModel->pdo->prepare($sql);
                        $stmt->execute([
                            ':price' => $prices[$sizeId],
                            ':product_id' => $id,
                            ':size_id' => $sizeId
                        ]);
                        
                        // Loại bỏ size này khỏi danh sách existingSizes để sau này dễ xử lý xóa
                        $existingSizes = array_diff($existingSizes, [$sizeId]);
                    } else {
                        // TRƯỜNG HỢP 2: Size chưa có -> Thêm mới (Insert)
                        $sql = "INSERT INTO product_sizes (product_id, size_id, price) VALUES (:product_id, :size_id, :price)";
                        $stmt = $this->productModel->pdo->prepare($sql);
                        $stmt->execute([
                            ':product_id' => $id,
                            ':size_id' => $sizeId,
                            ':price' => $prices[$sizeId]
                        ]);
                    }
                }
            }

            if (!empty($existingSizes)) {
                foreach ($existingSizes as $deleteSizeId) {
                    try {
                        // Thử xóa, nếu dính lỗi khóa ngoại (đã bán) thì bỏ qua, không crash web
                        $stmt = $this->productModel->pdo->prepare("DELETE FROM product_sizes WHERE product_id = :product_id AND size_id = :size_id");
                        $stmt->execute([
                            ':product_id' => $id,
                            ':size_id' => $deleteSizeId
                        ]);
                    } catch (Exception $e) {
                        // Bỏ qua lỗi
                    }
                }
            }
            // Xử lý Topping
            $toppings = $_POST['toppings'] ?? [];
        

            // Delete old toppings
            $this->productModel->pdo->prepare("DELETE FROM product_toppings WHERE product_id = :product_id")->execute([':product_id' => $id]);

            // Insert new toppings
            foreach ($toppings as $toppingId) {
                $sql = "INSERT INTO product_toppings (product_id, topping_id) VALUES (:product_id, :topping_id)";
                $stmt = $this->productModel->pdo->prepare($sql);
                $stmt->execute([
                    ':product_id' => $id,
                    ':topping_id' => $toppingId
                ]);
            }

            $this->productModel->pdo->commit();
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công';
        } catch (Exception $e) {
            $this->productModel->pdo->rollBack();
            $_SESSION['errors'] = ['update' => 'Cập nhật sản phẩm thất bại: ' . $e->getMessage()];
        }

        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        try {
            $this->productModel->delete($id);
            $_SESSION['success'] = 'Xóa sản phẩm thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa sản phẩm thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }

    public function deleteMultiple()
    {
        $ids = $_GET['ids'] ?? '';
        
        if (empty($ids)) {
            $_SESSION['errors'] = ['delete' => 'Không có sản phẩm nào được chọn'];
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $idArray = explode(',', $ids);
        $successCount = 0;

        try {
            foreach ($idArray as $id) {
                if ($this->productModel->delete(trim($id))) {
                    $successCount++;
                }
            }
            $_SESSION['success'] = "Đã xóa thành công {$successCount} sản phẩm";
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa sản phẩm thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }
}
