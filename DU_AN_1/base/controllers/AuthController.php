<?php

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin()
    {
        require_once PATH_VIEW . 'auth/login.php';
    }

    public function showRegister()
    {
        require_once PATH_VIEW . 'auth/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (empty($password)) {
            $errors['password'] = 'Mật khẩu không được để trống';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $_SESSION['errors'] = ['login' => 'Email hoặc mật khẩu không chính xác'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $storedPassword = $user['password'] ?? '';
        $isBcryptHash = is_string($storedPassword) && (strpos($storedPassword, '$2y$') === 0 || strpos($storedPassword, '$2a$') === 0);

        if (!$isBcryptHash) {
            if (!hash_equals($storedPassword, $password)) {
                $_SESSION['errors'] = ['login' => 'Email hoặc mật khẩu không chính xác'];
                $_SESSION['old'] = $_POST;
                header('Location: ' . BASE_URL . '?action=login');
                exit;
            }

            $this->userModel->updatePassword($user['id'], $password);
            $user = $this->userModel->findById($user['id']);
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['errors'] = ['login' => 'Email hoặc mật khẩu không chính xác'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($user['is_active'] == 0) {
            $_SESSION['errors'] = ['login' => 'Tài khoản đã bị khóa'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role_id' => $user['role_id']
        ];

        $_SESSION['success'] = 'Đăng nhập thành công';
        
        // Nếu là Admin (role_id = 2) thì chuyển đến trang dashboard
        if ($user['role_id'] == 2) {
            header('Location: ' . BASE_URL . '?action=admin');
            exit;
        }
        
        header('Location: ' . BASE_URL);
        exit;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Họ tên không được để trống';
        } elseif (strlen($name) < 3) {
            $errors['name'] = 'Họ tên phải có ít nhất 3 ký tự';
        }

        if (empty($email)) {
            $errors['email'] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        } elseif ($this->userModel->emailExists($email)) {
            $errors['email'] = 'Email đã được sử dụng';
        }

        if (empty($phone)) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (empty($password)) {
            $errors['password'] = 'Mật khẩu không được để trống';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        try {
            $userId = $this->userModel->register($name, $email, $phone, $password);
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập';
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['register' => 'Đăng ký thất bại. Vui lòng thử lại'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '?action=login');
        exit;
    }
}
