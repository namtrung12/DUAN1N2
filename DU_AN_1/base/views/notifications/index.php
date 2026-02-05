<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Thông báo - Chill Drink</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#35c8dfff",
                        "background-light": "#f6f7f8",
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light">
    <?php require_once PATH_VIEW . 'layouts/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Thông báo</h1>
            <?php 
            $notificationModel = new Notification();
            $unreadCount = $notificationModel->getUnreadCount($_SESSION['user']['id']);
            if ($unreadCount > 0): 
            ?>
            <a href="<?= BASE_URL ?>?action=notifications-read-all" class="text-sm text-primary hover:underline">
                Đánh dấu tất cả đã đọc
            </a>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <?php if (empty($notifications)): ?>
            <div class="p-8 text-center">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">notifications_off</span>
                <p class="text-gray-500">Bạn chưa có thông báo nào</p>
            </div>
            <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
            <a href="<?= BASE_URL ?>?action=notification-read&id=<?= $notif['id'] ?><?= $notif['order_id'] ? '&redirect=order-detail&order_id=' . $notif['order_id'] : '' ?>" 
               class="block p-4 hover:bg-gray-50 border-b border-gray-100 last:border-0 <?= !$notif['is_read'] ? 'bg-blue-50' : '' ?>">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                        <?php if ($notif['type'] === 'order_delivering'): ?>
                        bg-cyan-100
                        <?php elseif ($notif['type'] === 'order_cancelled'): ?>
                        bg-red-100
                        <?php else: ?>
                        bg-primary/20
                        <?php endif; ?>">
                        <?php if ($notif['type'] === 'order_delivering'): ?>
                        <span class="material-symbols-outlined text-cyan-600">local_shipping</span>
                        <?php elseif ($notif['type'] === 'order_cancelled'): ?>
                        <span class="material-symbols-outlined text-red-600">cancel</span>
                        <?php else: ?>
                        <span class="material-symbols-outlined text-primary">info</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-900 <?= !$notif['is_read'] ? '' : 'font-normal' ?>">
                            <?= htmlspecialchars($notif['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>
                        <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($notif['message'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="text-xs text-gray-400 mt-2"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></p>
                    </div>
                    <?php if (!$notif['is_read']): ?>
                    <div class="flex-shrink-0">
                        <span class="w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
