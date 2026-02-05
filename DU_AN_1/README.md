 Dự án 1
 
## 📋 Mục lục
- [Tính năng](#tính-năng)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Cài đặt](#cài-đặt)
- [Hướng dẫn sử dụng Git](#hướng-dẫn-sử-dụng-git)

## ✨ Tính năng

### Khách hàng
- 🛒 Xem và đặt hàng sản phẩm
- 🔍 Tìm kiếm và lọc sản phẩm theo danh mục
- 🛍️ Giỏ hàng với tùy chọn size và topping
- 💳 Thanh toán (COD, VNPay, Ví điện tử)
- 📦 Theo dõi trạng thái đơn hàng
- ⭐ Tích điểm thưởng và đổi quà
- 💰 Ví điện tử nạp tiền
- 📍 Quản lý nhiều địa chỉ giao hàng
- 👤 Quản lý thông tin tài khoản

### Admin
- 📊 Dashboard với biểu đồ thống kê
- 🍕 Quản lý sản phẩm (thêm, sửa, xóa, tìm kiếm)
- 🧀 Quản lý topping
- 📂 Quản lý danh mục sản phẩm
- 📋 Quản lý đơn hàng và cập nhật trạng thái
- 👥 Quản lý người dùng (thay đổi vai trò, khóa tài khoản)
- ⚙️ Cài đặt website (logo, banner, thông tin liên hệ)

## 🛠️ Công nghệ sử dụng

- **Backend**: PHP 8.x (Pure PHP, MVC pattern)
- **Database**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **CSS Framework**: Tailwind CSS, Bootstrap 5
- **Icons**: Material Symbols, Font Awesome 6
- **Charts**: Chart.js
- **Server**: Apache (XAMPP)

## 📦 Cài đặt

### Yêu cầu hệ thống
- XAMPP (PHP 8.x, MySQL 8.0, Apache)
- Git
- GitHub Desktop (khuyến nghị)
- Trình duyệt web hiện đại

### Bước 1: Clone repository

```bash
cd C:\xampp\htdocs
git clone https://github.com/YOUR_USERNAME/du-an-1-pizza-store.git DU_AN_1
```

Hoặc dùng GitHub Desktop:
1. File > Clone repository
2. Chọn repository từ danh sách
3. Local path: `C:\xampp\htdocs\DU_AN_1`

### Bước 2: Import database

1. Khởi động XAMPP (Apache + MySQL)
2. Mở phpMyAdmin: http://localhost/phpmyadmin
3. Tạo database mới tên `du_an1`
4. Click vào database `du_an1`
5. Click tab "Import"
6. Chọn file `Du_An_1.sql`
7. Click "Go"

### Bước 3: Cấu hình

1. Kiểm tra file `base/configs/env.php`:
```php
define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'du_an1');
```

2. Tạo thư mục uploads (nếu chưa có):
```bash
mkdir base\assets\uploads\products
mkdir base\assets\uploads\settings
mkdir base\assets\uploads\banners
```

### Bước 4: Chạy ứng dụng

1. Mở trình duyệt
2. Truy cập: http://localhost/DU_AN_1/base/

### Tài khoản mặc định

**Admin:**
- Email: admin@example.com
- Password: adminpass

**Customer:**
- Email: user@example.com  
- Password: user123

## 🔄 Hướng dẫn sử dụng Git

Xem file [GIT_SETUP.md](GIT_SETUP.md) để biết chi tiết.

### Quy trình làm việc hàng ngày

**Trước khi bắt đầu:**
```bash
git pull origin main
```

**Sau khi hoàn thành:**
```bash
git add .
git commit -m "Mô tả thay đổi"
git push origin main
```

**Hoặc dùng GitHub Desktop:**
1. Fetch origin (lấy code mới)
2. Pull origin (cập nhật code)
3. Làm việc
4. Commit changes (với message rõ ràng)
5. Push origin (đẩy code lên)

## 📁 Cấu trúc thư mục

```
DU_AN_1/
├── base/
│   ├── assets/
│   │   ├── css/          # File CSS
│   │   ├── js/           # File JavaScript
│   │   ├── images/       # Hình ảnh tĩnh
│   │   └── uploads/      # File upload (không commit)
│   ├── configs/
│   │   ├── env.php       # Cấu hình database
│   │   └── helper.php    # Hàm helper
│   ├── controllers/      # Controllers (xử lý logic)
│   ├── models/           # Models (tương tác database)
│   ├── views/            # Views (giao diện)
│   │   ├── admin/        # Trang admin
│   │   ├── layouts/      # Layout chung
│   │   ├── home/         # Trang chủ
│   │   ├── products/     # Trang sản phẩm
│   │   ├── cart/         # Giỏ hàng
│   │   ├── orders/       # Đơn hàng
│   │   └── ...
│   ├── routes/
│   │   └── index.php     # Định tuyến
│   └── index.php         # Entry point
├── Du_An_1.sql           # File database
├── .gitignore            # File bỏ qua khi commit
├── README.md             # File này
└── GIT_SETUP.md          # Hướng dẫn Git chi tiết
```

## 👥 Phân công công việc

### Thành viên 1: Frontend
- Thiết kế giao diện khách hàng
- Responsive design
- UX/UI improvements

### Thành viên 2: Backend
- API endpoints
- Business logic
- Database optimization

### Thành viên 3: Admin Panel
- Quản lý sản phẩm
- Quản lý đơn hàng
- Báo cáo thống kê

## 🐛 Báo lỗi

Nếu gặp lỗi, tạo Issue trên GitHub với thông tin:
- Mô tả lỗi
- Các bước tái hiện
- Screenshot (nếu có)
- Thông tin môi trường (PHP version, OS...)

## 📝 Quy tắc commit

- `feat:` Thêm tính năng mới
- `fix:` Sửa lỗi
- `style:` Thay đổi giao diện
- `refactor:` Tái cấu trúc code
- `docs:` Cập nhật tài liệu
- `test:` Thêm test

Ví dụ:
```
feat: Add user management page
fix: Fix cart total calculation
style: Update product card design
```

## 📞 Liên hệ

- Group Chat: [Link]
- Email: [Email]
- GitHub: [Repository URL]

## 📄 License

Dự án học tập - FPT Polytechnic

---

**Lưu ý:** File `base/configs/env.php` và thư mục `base/assets/uploads/` không được commit lên GitHub. Mỗi thành viên cần tự cấu hình local.
