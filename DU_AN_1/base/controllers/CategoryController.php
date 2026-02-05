<?php

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->categoryModel = new Category();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($_SESSION['user']['role_id'] != 2) {
            $_SESSION['errors'] = ['auth' => 'Bạn không có quyền truy cập'];
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function index()
    {
        $categories = $this->categoryModel->getAll();
        require_once PATH_VIEW . 'admin/categories.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?? '';

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }

        if (empty($slug)) {
            $slug = $this->createSlug($name);
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->create($name, $slug);
            $_SESSION['success'] = 'Thêm danh mục thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['create' => 'Thêm danh mục thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?? '';

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }

        if (empty($slug)) {
            $slug = $this->createSlug($name);
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->update($id, $name, $slug);
            $_SESSION['success'] = 'Cập nhật danh mục thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật danh mục thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        try {
            $this->categoryModel->delete($id);
            $_SESSION['success'] = 'Xóa danh mục thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa danh mục thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function deleteMultiple()
    {
        $ids = $_GET['ids'] ?? '';
        
        if (empty($ids)) {
            $_SESSION['errors'] = ['delete' => 'Không có danh mục nào được chọn'];
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $idArray = explode(',', $ids);
        $successCount = 0;

        try {
            foreach ($idArray as $id) {
                if ($this->categoryModel->delete(trim($id))) {
                    $successCount++;
                }
            }
            $_SESSION['success'] = "Đã xóa thành công {$successCount} danh mục";
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa danh mục thất bại'];
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    private function createSlug($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $string);
        $string = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $string);
        $string = preg_replace('/[íìỉĩị]/u', 'i', $string);
        $string = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $string);
        $string = preg_replace('/[úùủũụưứừửữự]/u', 'u', $string);
        $string = preg_replace('/[ýỳỷỹỵ]/u', 'y', $string);
        $string = preg_replace('/đ/u', 'd', $string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }
}
