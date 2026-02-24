<?php

class WalletController
{
    private $walletModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->walletModel = new Wallet();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['auth' => 'Vui lòng đăng nhập'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    public function index()
    {
        $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
        $transactions = $this->walletModel->getTransactions($_SESSION['user']['id']);

        require_once PATH_VIEW . 'wallet/index.php';
    }

    public function deposit()
    {
        $wallet = $this->walletModel->createOrGet($_SESSION['user']['id']);
        require_once PATH_VIEW . 'wallet/deposit.php';
    }

    public function processDeposit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=wallet-deposit');
            exit;
        }

        $amount = $_POST['amount'] ?? 0;
        $paymentMethod = $_POST['payment_method'] ?? '';

        $errors = [];

        if ($amount < 10000) {
            $errors['amount'] = 'Số tiền nạp tối thiểu là 10.000đ';
        } elseif ($amount > 50000000) {
            $errors['amount'] = 'Số tiền nạp tối đa là 50.000.000đ';
        }

        if (!in_array($paymentMethod, ['vnpay', 'momo', 'bank', 'card'])) {
            $errors['payment'] = 'Phương thức thanh toán không hợp lệ';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=wallet-deposit');
            exit;
        }

        // Nếu thanh toán VNPay, chuyển hướng đến VNPay
        if ($paymentMethod === 'vnpay') {
            require_once PATH_ROOT . 'configs/vnpay.php';
            $orderInfo = 'Nạp tiền vào ví: ' . number_format($amount, 0, ',', '.') . 'đ';
            $vnpayUrl = createVNPayPaymentUrl('wallet_' . $_SESSION['user']['id'], $amount, $orderInfo, 'topup');
            header('Location: ' . $vnpayUrl);
            exit;
        }

        // Xử lý các phương thức thanh toán khác (demo - tự động thành công)
        try {
            $this->walletModel->pdo->beginTransaction();

            $this->walletModel->updateBalance($_SESSION['user']['id'], $amount);

            $this->walletModel->addTransaction(
                $_SESSION['user']['id'],
                null,
                'deposit',
                $amount,
                'Nạp tiền vào ví qua ' . $paymentMethod
            );

            $this->walletModel->pdo->commit();

            $_SESSION['success'] = 'Nạp tiền thành công! Số dư mới: ' . number_format($amount, 0, ',', '.') . 'đ';
            header('Location: ' . BASE_URL . '?action=wallet');
            exit;
        } catch (Exception $e) {
            $this->walletModel->pdo->rollBack();
            $_SESSION['errors'] = ['deposit' => 'Nạp tiền thất bại. Vui lòng thử lại'];
            header('Location: ' . BASE_URL . '?action=wallet-deposit');
            exit;
        }
    }
}
