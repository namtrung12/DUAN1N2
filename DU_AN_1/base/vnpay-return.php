<?php
session_start();
require_once 'configs/env.php';
require_once 'configs/vnpay.php';

// Lấy dữ liệu từ VNPay
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
$vnp_Amount = $_GET['vnp_Amount'] ?? 0;
$vnp_OrderInfo = $_GET['vnp_OrderInfo'] ?? '';
$vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
$vnp_BankCode = $_GET['vnp_BankCode'] ?? '';

// Xác thực chữ ký
$isValid = verifyVNPayCallback($_GET);

if (!$isValid) {
    $_SESSION['errors'] = ['vnpay' => 'Chữ ký không hợp lệ'];
    header('Location: ' . BASE_URL);
    exit;
}

// Xử lý theo response code
if ($vnp_ResponseCode == '00') {
    // Thanh toán thành công
    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
            DB_USERNAME,
            DB_PASSWORD,
            DB_OPTIONS
        );

        // Parse transaction reference
        $parts = explode('_', $vnp_TxnRef);
        $type = $parts[0]; // 'order' hoặc 'wallet'
        $id = $parts[1] ?? 0;

        if ($type === 'order') {
            // Kiểm tra đơn hàng có tồn tại không
            $stmt = $pdo->prepare("SELECT id, status, payment_status FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                // Chỉ cập nhật nếu chưa thanh toán
                if ($order['payment_status'] !== 'paid') {
                    // Cập nhật trạng thái thanh toán và chuyển đơn hàng sang processing
                    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'processing', vnpay_transaction_id = :txn_id, updated_at = NOW() WHERE id = :id");
                    $stmt->execute([
                        ':txn_id' => $vnp_TransactionNo,
                        ':id' => $id
                    ]);

                    $_SESSION['success'] = 'Thanh toán đơn hàng thành công! Đơn hàng đang được xử lý. Mã giao dịch: ' . $vnp_TransactionNo;
                } else {
                    $_SESSION['success'] = 'Đơn hàng đã được thanh toán trước đó';
                }
            } else {
                $_SESSION['errors'] = ['vnpay' => 'Không tìm thấy đơn hàng'];
            }

            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $id);
        } elseif ($type === 'wallet') {
            // Nạp tiền vào ví
            $amount = $vnp_Amount / 100; // Chuyển về VND
            $userId = $_SESSION['user']['id'] ?? 0;

            if ($userId) {
                // Cập nhật số dư ví
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + :amount WHERE user_id = :user_id");
                $stmt->execute([
                    ':amount' => $amount,
                    ':user_id' => $userId
                ]);

                // Lưu lịch sử giao dịch
                $stmt = $pdo->prepare("INSERT INTO wallet_transactions (user_id, type, amount, description, transaction_id) 
                                      VALUES (:user_id, 'deposit', :amount, :description, :txn_id)");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':amount' => $amount,
                    ':description' => 'Nạp tiền qua VNPay',
                    ':txn_id' => $vnp_TransactionNo
                ]);

                $_SESSION['success'] = 'Nạp tiền thành công! Số tiền: ' . number_format($amount, 0, ',', '.') . 'đ';
            }

            header('Location: ' . BASE_URL . '?action=wallet');
        } else {
            header('Location: ' . BASE_URL);
        }
    } catch (Exception $e) {
        $_SESSION['errors'] = ['vnpay' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        header('Location: ' . BASE_URL);
    }
} else {
    // Thanh toán thất bại
    $errorMessages = [
        '07' => 'Giao dịch bị nghi ngờ gian lận',
        '09' => 'Thẻ chưa đăng ký dịch vụ',
        '10' => 'Xác thực thông tin thẻ không đúng',
        '11' => 'Hết hạn chờ thanh toán',
        '12' => 'Thẻ bị khóa',
        '13' => 'Sai mật khẩu xác thực',
        '24' => 'Giao dịch bị hủy',
        '51' => 'Tài khoản không đủ số dư',
        '65' => 'Vượt quá số lần nhập sai',
        '75' => 'Ngân hàng đang bảo trì',
        '79' => 'Nhập sai mật khẩu quá số lần quy định'
    ];

    $errorMsg = $errorMessages[$vnp_ResponseCode] ?? 'Giao dịch không thành công';
    $_SESSION['errors'] = ['vnpay' => $errorMsg];
    
    // Parse transaction reference để redirect đúng trang
    $parts = explode('_', $vnp_TxnRef);
    $type = $parts[0]; // 'order' hoặc 'wallet'
    $id = $parts[1] ?? 0;
    
    if ($type === 'wallet') {
        // Nếu là nạp tiền ví thất bại, quay về trang ví
        header('Location: ' . BASE_URL . '?action=wallet');
    } elseif ($type === 'order' && $id > 0) {
        // Nếu là thanh toán đơn hàng thất bại, quay về trang đơn hàng
        // Khách hàng có thể đổi phương thức thanh toán hoặc thử lại
        header('Location: ' . BASE_URL . '?action=orders');
    } else {
        // Trường hợp khác, quay về trang chủ
        header('Location: ' . BASE_URL);
    }
}
exit;
