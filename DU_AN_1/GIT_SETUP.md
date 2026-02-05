# Hướng dẫn đẩy project lên GitHub

## Bước 1: Khởi tạo Git (chỉ làm 1 lần)

```bash
# Di chuyển vào thư mục project
cd C:\xampp\htdocs\DU_AN_1

# Khởi tạo git
git init

# Thêm tất cả file vào staging
git add .

# Commit lần đầu
git commit -m "Initial commit: Pizza Store project with admin features"
```

## Bước 2: Kết nối với GitHub

```bash
# Thay YOUR_USERNAME và YOUR_REPO bằng thông tin của bạn
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git

# Đổi branch thành main (nếu cần)
git branch -M main

# Đẩy code lên GitHub
git push -u origin main
```

## Bước 3: Cài đặt GitHub Desktop (Khuyến nghị)

1. Tải GitHub Desktop: https://desktop.github.com/
2. Cài đặt và đăng nhập tài khoản GitHub
3. Click "Add" > "Add existing repository"
4. Chọn thư mục: `C:\xampp\htdocs\DU_AN_1`
5. Click "Add repository"

## Bước 4: Mời thành viên vào repository

1. Vào repository trên GitHub
2. Click tab "Settings"
3. Click "Collaborators" ở sidebar
4. Click "Add people"
5. Nhập username hoặc email của thành viên
6. Click "Add [username] to this repository"

## Bước 5: Thành viên clone project

### Cách 1: Dùng GitHub Desktop (Dễ nhất)
1. Mở GitHub Desktop
2. Click "File" > "Clone repository"
3. Chọn repository từ danh sách
4. Chọn thư mục lưu (ví dụ: `C:\xampp\htdocs\`)
5. Click "Clone"

### Cách 2: Dùng Command Line
```bash
cd C:\xampp\htdocs
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git
```

## Bước 6: Cấu hình database cho thành viên

Sau khi clone, mỗi thành viên cần:

1. Import database:
   - Mở phpMyAdmin
   - Tạo database mới tên `du_an1`
   - Import file `Du_An_1.sql`

2. Cấu hình kết nối (nếu cần):
   - Mở file `base/configs/env.php`
   - Kiểm tra thông tin database:
     ```php
     define('DB_HOST',     'localhost');
     define('DB_PORT',     '3306');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', '');
     define('DB_NAME',     'du_an1');
     ```

3. Tạo thư mục uploads (nếu chưa có):
   ```bash
   mkdir base/assets/uploads/products
   mkdir base/assets/uploads/settings
   mkdir base/assets/uploads/banners
   ```

## Quy trình làm việc hàng ngày

### Trước khi bắt đầu làm việc:
```bash
# Pull code mới nhất từ GitHub
git pull origin main
```

Hoặc trong GitHub Desktop: Click "Fetch origin" rồi "Pull origin"

### Sau khi hoàn thành công việc:
```bash
# Xem file đã thay đổi
git status

# Thêm file vào staging
git add .

# Commit với message mô tả
git commit -m "Add user management feature"

# Đẩy lên GitHub
git push origin main
```

Hoặc trong GitHub Desktop:
1. Xem các file thay đổi ở tab "Changes"
2. Nhập commit message ở ô bên dưới
3. Click "Commit to main"
4. Click "Push origin"

## Lưu ý quan trọng

### File KHÔNG nên commit lên GitHub:
- `base/assets/uploads/*` (file ảnh upload)
- `base/configs/env.php` (nếu có thông tin nhạy cảm)
- `.DS_Store`, `Thumbs.db` (file hệ thống)

File `.gitignore` đã được tạo để tự động bỏ qua các file này.

### Xử lý conflict:
Nếu có conflict khi pull:
1. GitHub Desktop sẽ thông báo
2. Mở file bị conflict
3. Tìm dòng có `<<<<<<<`, `=======`, `>>>>>>>`
4. Chọn giữ code nào (hoặc merge cả 2)
5. Xóa các dấu conflict
6. Commit lại

### Branch strategy (Nâng cao):
Nếu nhóm muốn làm việc an toàn hơn:
```bash
# Tạo branch mới cho feature
git checkout -b feature/ten-tinh-nang

# Làm việc và commit
git add .
git commit -m "Add feature"

# Đẩy branch lên GitHub
git push origin feature/ten-tinh-nang

# Tạo Pull Request trên GitHub để review
# Sau khi approve, merge vào main
```

## Troubleshooting

### Lỗi: "Permission denied"
- Kiểm tra đã được mời vào repository chưa
- Đăng nhập lại GitHub Desktop

### Lỗi: "Failed to push"
- Chạy `git pull` trước để lấy code mới nhất
- Giải quyết conflict (nếu có)
- Push lại

### Lỗi: "Database connection failed"
- Kiểm tra XAMPP đã bật MySQL chưa
- Kiểm tra đã import database chưa
- Kiểm tra thông tin trong `env.php`

## Liên hệ
Nếu gặp vấn đề, hỏi trong group chat hoặc tạo Issue trên GitHub.
