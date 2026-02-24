<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Điểm thưởng - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#A0DDE6",
                        "background-light": "#F5F7FA",
                        "text-main-light": "#1F2937",
                        "text-secondary-light": "#6B7280",
                    },
                    // fontFamily: {
                    //     "display": ["Poppins", "sans-serif"]
                    // },
                },
            },
        }
    </script>
</head>

<body class="font-display bg-background-light">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    <div class="relative flex h-auto min-h-screen w-full flex-col">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex flex-1 justify-center py-5 px-4 sm:px-6 md:px-8" role="main">
                <div class="layout-content-container flex flex-col w-full max-w-5xl flex-1">
                    <div class="flex flex-wrap justify-between gap-3 p-4">
                        <p class="text-text-main-light text-4xl font-bold leading-tight tracking-[-0.033em] min-w-72">Điểm thưởng</p>
                    </div>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="mx-4 mb-4 p-4 bg-green-100 text-green-700 rounded-lg"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php unset($_SESSION['success']);
                    endif; ?>
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-stretch justify-between gap-6 rounded-lg bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-6">
                                <div class="flex flex-wrap gap-x-12 gap-y-4">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-text-secondary-light text-sm font-normal leading-normal">Điểm hiện tại</p>
                                        <p class="text-text-main-light text-3xl font-bold leading-tight"><?= number_format($loyaltyPoints['total_points'], 0, ',', '.') ?> điểm</p>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-text-secondary-light text-sm font-normal leading-normal">Tổng điểm tích lũy</p>
                                        <p class="text-text-main-light text-3xl font-bold leading-tight"><?= number_format($loyaltyPoints['lifetime_points'], 0, ',', '.') ?> điểm</p>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-text-secondary-light text-sm font-normal leading-normal">Hạng thành viên</p>
                                        <p class="text-text-main-light text-2xl font-bold leading-tight uppercase">
                                            <?php
                                            $rankNames = [
                                                'new' => '🆕 KHÁCH MỚI',
                                                'bronze' => '🥉 BRONZE',
                                                'silver' => '🥈 SILVER',
                                                'gold' => '🥇 GOLD',
                                                'diamond' => '💎 DIAMOND'
                                            ];
                                            echo $rankNames[$loyaltyPoints['level']] ?? strtoupper($loyaltyPoints['level']);
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <a href="<?= BASE_URL ?>?action=loyalty-rewards" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-text-main-light gap-2 text-sm font-semibold leading-normal w-fit">
                                    <span class="material-symbols-outlined text-lg">redeem</span>
                                    <span class="truncate">Đổi quà</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <h2 class="text-text-main-light text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-8">Phần thưởng của tôi</h2>
                    <div class="px-4 py-3">
                        <?php if (empty($userRewards)): ?>
                            <div class="bg-white rounded-lg p-10 text-center shadow-sm">
                                <p class="text-text-secondary-light">Bạn chưa có phần thưởng nào</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($userRewards as $reward): ?>
                                    <div class="bg-white rounded-lg p-6 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <h3 class="font-bold text-lg text-text-main-light"><?= htmlspecialchars($reward['reward_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                            <?php if (!$reward['is_used']): ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Chưa dùng</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Đã dùng</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-text-secondary-light mb-2">Mã: <span class="font-mono font-bold"><?= htmlspecialchars($reward['code'], ENT_QUOTES, 'UTF-8') ?></span></p>
                                        <?php if ($reward['expires_at']): ?>
                                            <p class="text-xs text-text-secondary-light">Hết hạn: <?= date('d/m/Y', strtotime($reward['expires_at'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-text-main-light text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-8">Lịch sử điểm thưởng</h2>
                    <div class="px-4 py-3">
                        <?php if (empty($transactions)): ?>
                            <div class="bg-white rounded-lg p-10 text-center shadow-sm">
                                <p class="text-text-secondary-light">Chưa có giao dịch nào</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                                <table class="w-full min-w-[700px] text-sm text-left text-text-secondary-light">
                                    <thead class="text-xs text-text-main-light uppercase bg-background-light border-b">
                                        <tr>
                                            <th class="px-6 py-4 font-semibold" scope="col">Loại giao dịch</th>
                                            <th class="px-6 py-4 font-semibold" scope="col">Điểm</th>
                                            <th class="px-6 py-4 font-semibold" scope="col">Mô tả</th>
                                            <th class="px-6 py-4 font-semibold" scope="col">Ngày/Giờ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $trans): ?>
                                            <tr class="border-b">
                                                <td class="px-6 py-4 font-medium text-text-main-light"><?= $trans['type'] === 'earn' ? 'Tích điểm' : 'Đổi quà' ?></td>
                                                <td class="px-6 py-4 <?= $trans['type'] === 'earn' ? 'text-green-600' : 'text-red-600' ?>">
                                                    <?= $trans['type'] === 'earn' ? '+' : '-' ?><?= number_format($trans['points'], 0, ',', '.') ?>
                                                </td>
                                                <td class="px-6 py-4"><?= htmlspecialchars($trans['description'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="px-6 py-4"><?= date('d/m/Y, H:i', strtotime($trans['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>