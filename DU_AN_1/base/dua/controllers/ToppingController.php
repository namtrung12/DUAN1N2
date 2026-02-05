<?php

class ToppingController
{
    private $toppingModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->toppingModel = new Topping();
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
        $toppings = $this->toppingModel->getAll();
        require_once PATH_VIEW . 'admin/toppings.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-toppings');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $status = isset($_POST['status']) ? 1 : 0;

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên topping không được để trống';
        }

        if ($price < 0) {
            $errors['price'] = 'Giá không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=admin-toppings');
            exit;
        }

        try {
            $this->toppingModel->create($name, $price, $status);
            $_SESSION['success'] = 'Thêm topping thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['create' => 'Thêm topping thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-toppings');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-toppings');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $status = isset($_POST['status']) ? 1 : 0;

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên topping không được để trống';
        }

        if ($price < 0) {
            $errors['price'] = 'Giá không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=admin-toppings');
            exit;
        }

        try {
            $this->toppingModel->update($id, $name, $price, $status);
            $_SESSION['success'] = 'Cập nhật topping thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật topping thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-toppings');
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        try {
            $this->toppingModel->delete($id);
            $_SESSION['success'] = 'Xóa topping thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa topping thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-toppings');
        exit;
    }

    public function deleteMultiple()
    {
        $ids = $_GET['ids'] ?? '';
        
        if (empty($ids)) {
            $_SESSION['errors'] = ['delete' => 'Không có topping nào được chọn'];
            header('Location: ' . BASE_URL . '?action=admin-toppings');
            exit;
        }

        $idArray = explode(',', $ids);
        $successCount = 0;

        try {
            foreach ($idArray as $id) {
                if ($this->toppingModel->delete(trim($id))) {
                    $successCount++;
                }
            }
            $_SESSION['success'] = "Đã xóa thành công {$successCount} topping";
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa topping thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-toppings');
        exit;
    }
}
