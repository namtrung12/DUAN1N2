<?php
// VNPay Configuration
date_default_timezone_set('Asia/Ho_Chi_Minh');

define('VNP_TMN_CODE', 'LPV6UI00'); // Mã merchant
define('VNP_HASH_SECRET', '6MCXXIDJLTD5W0IAID2HXUXM0HSRMBWA'); // Secret key
define('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
define('VNP_RETURN_URL', BASE_URL . 'vnpay-return.php'); // URL return sau khi thanh toán
define('VNP_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction');

/**
 * Tạo URL thanh toán VNPay
 */
function createVNPayPaymentUrl($orderId, $amount, $orderInfo, $orderType = 'billpayment') {
    $vnp_TxnRef = $orderId . '_' . time(); // Mã giao dịch
    $vnp_Amount = $amount * 100; // VNPay tính theo đơn vị nhỏ nhất (VND * 100)
    $vnp_Locale = 'vn';
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => VNP_TMN_CODE,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $orderInfo,
        "vnp_OrderType" => $orderType,
        "vnp_ReturnUrl" => VNP_RETURN_URL,
        "vnp_TxnRef" => $vnp_TxnRef,
    );

    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $vnp_Url = VNP_URL . "?" . $query;
    $vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASH_SECRET);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

    return $vnp_Url;
}

/**
 * Xác thực callback từ VNPay
 */
function verifyVNPayCallback($inputData) {
    $vnp_SecureHash = $inputData['vnp_SecureHash'];
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    
    $hashdata = "";
    $i = 0;
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }

    $secureHash = hash_hmac('sha512', $hashdata, VNP_HASH_SECRET);
    
    return $secureHash === $vnp_SecureHash;
}
