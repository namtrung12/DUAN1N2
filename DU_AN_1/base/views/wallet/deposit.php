<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Nạp tiền - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#32c6ddff",
                        "background-light": "#F5F7FA",
                        "text-main-light": "#1F2937",
                        "text-secondary-light": "#6B7280",
                    },
                    fontFamily: {
                        "display": ["Poppins", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>

<body class="font-display bg-background-light">
    <div class="relative flex h-auto min-h-screen w-full flex-col items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6 sm:p-8">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-text-main-light">Nạp tiền vào ví</h3>
                <p class="mt-2 text-sm text-text-secondary-light">Số dư hiện tại: <?= number_format($wallet['balance'], 0, ',', '.') ?> VNĐ</p>
            </div>
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                </div>
            <?php unset($_SESSION['errors']);
            endif; ?>
            <form action="<?= BASE_URL ?>?action=wallet-process-deposit" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-text-main-light" for="amount">Số tiền (VNĐ)</label>
                    <div class="relative mt-2">
                        <input class="w-full h-12 pl-4 pr-12 rounded-lg border border-gray-300 bg-white text-text-main-light text-lg font-semibold focus:ring-2 focus:ring-primary focus:border-transparent" id="amount" name="amount" placeholder="Ví dụ: 100000" type="number" min="10000" max="50000000" required />
                        <span class="absolute inset-y-0 right-4 flex items-center text-text-secondary-light">VNĐ</span>
                    </div>
                    <p class="mt-1 text-xs text-text-secondary-light">Tối thiểu: 10.000đ - Tối đa: 50.000.000đ</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-main-light mb-3">Phương thức thanh toán</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/10 transition-all">
                            <input type="radio" name="payment_method" value="vnpay" class="form-radio h-5 w-5 text-primary" checked required />
                            <div class="ml-4 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-text-main-light">💳 VNPay</span>
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Khuyến nghị</span>
                                </div>
                                <p class="text-xs text-text-secondary-light mt-1">Thanh toán qua ATM, Visa, MasterCard</p>
                            </div>
                        </label>


                    </div>
                </div>
                <button type="submit" class="flex w-full min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-4 bg-primary text-text-main-light gap-2 text-base font-semibold leading-normal">Xác nhận nạp tiền</button>
                <a href="<?= BASE_URL ?>?action=wallet" class="block text-center text-sm text-text-secondary-light hover:text-primary">Quay lại</a>
            </form>
        </div>
    </div>
</body>

</html>