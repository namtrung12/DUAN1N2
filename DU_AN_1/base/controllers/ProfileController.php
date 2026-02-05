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

    /**
     * Kiểm tra tên người dùng hợp lệ
     */
    private function isValidName($name)
    {
        $trimmed = trim($name);
        if (empty($trimmed) || preg_match('/^\s+$/', $name)) {
            return false;
        }
        // Tên chỉ chứa chữ cái và dấu cách
        if (preg_match('/[0-9<>\"\';\(\)\{\}\[\]\\\\!@#\$%\^&\*\+=]/', $name)) {
            return false;
        }
        return true;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Họ tên không được để trống';
        } elseif (preg_match('/^\s+$/', $name)) {
            $errors['name'] = 'Họ tên không được chỉ chứa khoảng trắng';
        } elseif (!$this->isValidName($name)) {
            $errors['name'] = 'Họ tên không hợp lệ (không chứa số hoặc ký tự đặc biệt)';
        } elseif (strlen($name) < 3) {
            $errors['name'] = 'Họ tên phải có ít nhất 3 ký tự';
        }

        if (empty($phone)) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (preg_match('/^\s+$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không được chỉ chứa khoảng trắng';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ (10-11 số)';
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

    public function updateAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['errors'] = ['avatar' => 'Vui lòng chọn ảnh đại diện'];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['errors'] = ['avatar' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)'];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['errors'] = ['avatar' => 'Kích thước ảnh không được vượt quá 2MB'];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $_SESSION['user']['id'] . '_' . time() . '.' . $extension;
        $uploadPath = PATH_ROOT . 'assets/uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Delete old avatar if exists
            $user = $this->userModel->findById($_SESSION['user']['id']);
            if (!empty($user['avatar']) && file_exists(PATH_ROOT . $user['avatar'])) {
                unlink(PATH_ROOT . $user['avatar']);
            }

            $avatarPath = 'assets/uploads/' . $filename;
            $this->userModel->updateAvatar($_SESSION['user']['id'], $avatarPath);
            $_SESSION['user']['avatar'] = $avatarPath;
            $_SESSION['success'] = 'Cập nhật ảnh đại diện thành công';
        } else {
            $_SESSION['errors'] = ['avatar' => 'Upload ảnh thất bại. Vui lòng thử lại'];
        }

        header('Location: ' . BASE_URL . '?action=profile');
        exit;
    }
}
