<?php

class ProfileController
{
    private $userModel;
    private $addressModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->userModel = new User();
        $this->addressModel = new Address();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    public function index()
    {
        $user = $this->userModel->findById($_SESSION['user']['id']);
        $addresses = $this->addressModel->getByUserId($_SESSION['user']['id']);
        require_once PATH_VIEW . 'profile/index.php';
    }

    public function edit()
    {
        $user = $this->userModel->findById($_SESSION['user']['id']);
        require_once PATH_VIEW . 'profile/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Họ tên không được để trống';
        } elseif (strlen($name) < 3) {
            $errors['name'] = 'Họ tên phải có ít nhất 3 ký tự';
        }

        if (empty($phone)) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=profile-edit');
            exit;
        }

        try {
            $this->userModel->updateProfile($_SESSION['user']['id'], $name, $phone);
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['success'] = 'Cập nhật thông tin thành công';
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật thất bại. Vui lòng thử lại'];
            header('Location: ' . BASE_URL . '?action=profile-edit');
            exit;
        }
    }

    public function changePassword()
    {
        require_once PATH_VIEW . 'profile/change-password.php';
    }

    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors = [];

        $user = $this->userModel->findById($_SESSION['user']['id']);

        if (empty($currentPassword)) {
            $errors['current_password'] = 'Mật khẩu hiện tại không được để trống';
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $errors['current_password'] = 'Mật khẩu hiện tại không chính xác';
        }

        if (empty($newPassword)) {
            $errors['new_password'] = 'Mật khẩu mới không được để trống';
        } elseif (strlen($newPassword) < 6) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        }

        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }

        try {
            $this->userModel->updatePassword($_SESSION['user']['id'], $newPassword);
            $_SESSION['success'] = 'Đổi mật khẩu thành công';
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Đổi mật khẩu thất bại. Vui lòng thử lại'];
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }
    }
}
