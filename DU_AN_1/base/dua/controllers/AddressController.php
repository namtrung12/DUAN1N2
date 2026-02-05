<?php

class AddressController
{
    private $addressModel;

    public function __construct()
    {
        $this->checkAuth();
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
        $addresses = $this->addressModel->getByUserId($_SESSION['user']['id']);
        require_once PATH_VIEW . 'address/index.php';
    }

    public function create()
    {
        require_once PATH_VIEW . 'address/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        }

        $data = [
            'label' => $_POST['label'] ?? 'Nhà',
            'receiver_name' => $_POST['receiver_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'province' => $_POST['province'] ?? '',
            'district' => $_POST['district'] ?? '',
            'ward' => $_POST['ward'] ?? '',
            'detail' => $_POST['detail'] ?? '',
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        $errors = [];

        if (empty($data['receiver_name'])) {
            $errors['receiver_name'] = 'Tên người nhận không được để trống';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (empty($data['province'])) {
            $errors['province'] = 'Tỉnh/Thành phố không được để trống';
        }

        if (empty($data['district'])) {
            $errors['district'] = 'Quận/Huyện không được để trống';
        }

        if (empty($data['ward'])) {
            $errors['ward'] = 'Phường/Xã không được để trống';
        }

        if (empty($data['detail'])) {
            $errors['detail'] = 'Địa chỉ chi tiết không được để trống';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=address-create');
            exit;
        }

        try {
            if ($data['is_default']) {
                $this->addressModel->setDefault(0, $_SESSION['user']['id']);
            }
            $this->addressModel->create($_SESSION['user']['id'], $data);
            $_SESSION['success'] = 'Thêm địa chỉ thành công';
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['create' => 'Thêm địa chỉ thất bại. Vui lòng thử lại'];
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=address-create');
            exit;
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $address = $this->addressModel->getById($id);

        if (!$address || $address['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['address' => 'Địa chỉ không tồn tại'];
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        }

        require_once PATH_VIEW . 'address/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $data = [
            'label' => $_POST['label'] ?? 'Nhà',
            'receiver_name' => $_POST['receiver_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'province' => $_POST['province'] ?? '',
            'district' => $_POST['district'] ?? '',
            'ward' => $_POST['ward'] ?? '',
            'detail' => $_POST['detail'] ?? '',
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        $errors = [];

        if (empty($data['receiver_name'])) {
            $errors['receiver_name'] = 'Tên người nhận không được để trống';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (empty($data['province'])) {
            $errors['province'] = 'Tỉnh/Thành phố không được để trống';
        }

        if (empty($data['district'])) {
            $errors['district'] = 'Quận/Huyện không được để trống';
        }

        if (empty($data['ward'])) {
            $errors['ward'] = 'Phường/Xã không được để trống';
        }

        if (empty($data['detail'])) {
            $errors['detail'] = 'Địa chỉ chi tiết không được để trống';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: ' . BASE_URL . '?action=address-edit&id=' . $id);
            exit;
        }

        try {
            if ($data['is_default']) {
                $this->addressModel->setDefault($id, $_SESSION['user']['id']);
            }
            $this->addressModel->update($id, $_SESSION['user']['id'], $data);
            $_SESSION['success'] = 'Cập nhật địa chỉ thành công';
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors'] = ['update' => 'Cập nhật địa chỉ thất bại. Vui lòng thử lại'];
            header('Location: ' . BASE_URL . '?action=address-edit&id=' . $id);
            exit;
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $address = $this->addressModel->getById($id);

        if (!$address || $address['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['address' => 'Địa chỉ không tồn tại'];
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        }

        try {
            $this->addressModel->delete($id, $_SESSION['user']['id']);
            $_SESSION['success'] = 'Xóa địa chỉ thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['delete' => 'Xóa địa chỉ thất bại. Vui lòng thử lại'];
        }

        header('Location: ' . BASE_URL . '?action=address');
        exit;
    }

    public function setDefault()
    {
        $id = $_GET['id'] ?? 0;
        $address = $this->addressModel->getById($id);

        if (!$address || $address['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['errors'] = ['address' => 'Địa chỉ không tồn tại'];
            header('Location: ' . BASE_URL . '?action=address');
            exit;
        }

        try {
            $this->addressModel->setDefault($id, $_SESSION['user']['id']);
            $_SESSION['success'] = 'Đặt địa chỉ mặc định thành công';
        } catch (Exception $e) {
            $_SESSION['errors'] = ['default' => 'Đặt địa chỉ mặc định thất bại. Vui lòng thử lại'];
        }

        header('Location: ' . BASE_URL . '?action=address');
        exit;
    }
}
