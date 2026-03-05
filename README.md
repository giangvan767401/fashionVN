<div align="center">

# 🛍️ FashionVN

**Nền tảng thương mại điện tử thời trang — Full-Stack · Laravel 12 · PHP 8.2+**

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpinedotjs&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=flat-square&logo=vite&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-22c55e?style=flat-square)

</div>

---

## 📖 Giới thiệu

**FashionVN** là nền tảng thương mại điện tử thời trang full-stack, xây dựng trên **Laravel 12**. Hệ thống cung cấp trải nghiệm mua sắm hoàn chỉnh cho khách hàng và bảng quản trị mạnh mẽ cho người vận hành shop, bao gồm quản lý kho hàng, đơn mua hàng nhập, phân tích doanh thu và phân quyền chi tiết.

Thư mục `reference_theme/` chứa bản thiết kế UI/UX tham khảo được xây dựng bằng **Next.js**, giúp đội frontend có cơ sở chính xác để hiện thực giao diện trên Blade.

---

## ✨ Tính năng

<details>
<summary><strong>👤 Phía khách hàng</strong></summary>

- Trang chủ với banner động, collection và sản phẩm nổi bật
- Danh sách sản phẩm theo collection / danh mục / thương hiệu / tag
- Trang chi tiết sản phẩm — biến thể (màu sắc, kích cỡ, ...), gallery ảnh, mô tả, đánh giá
- Giỏ hàng: thêm, cập nhật số lượng, xóa sản phẩm, áp dụng coupon
- Danh sách yêu thích (Wishlist)
- Quy trình thanh toán đa bước: **Thông tin → Vận chuyển → Thanh toán → Xác nhận đơn**
- Đánh giá & nhận xét sản phẩm (Reviews)
- Quản lý hồ sơ cá nhân & đổi mật khẩu
- Các trang tĩnh: Sứ mệnh, Phát triển bền vững, FAQ, Liên hệ

</details>

<details>
<summary><strong>🔐 Xác thực & Phân quyền</strong></summary>

- Đăng ký / Đăng nhập / Quên mật khẩu (Laravel Breeze)
- Xác minh email (Email Verification)
- Hệ thống Roles & Permissions tùy chỉnh (bảng `roles`, `permissions`, `role_permissions`)

</details>

<details>
<summary><strong>🛠️ Bảng quản trị (Admin Panel)</strong></summary>

- Dashboard tổng quan
- Quản lý người dùng (xem, xóa)
- Quản lý danh mục (CRUD + toggle trạng thái)
- Quản lý sản phẩm & biến thể
- Quản lý kho hàng (Warehouses & Inventory)
- Quản lý nhà cung cấp & đơn đặt hàng nhập (Suppliers & Purchase Orders)
- Quản lý đơn hàng & thanh toán
- Banner & chiến dịch Email marketing
- Báo cáo & phân tích (Analytics & Reports)

</details>

---

## 🧱 Tech Stack

| Lớp | Công nghệ |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 3, Alpine.js 3 |
| Build tool | Vite 7 |
| Authentication | Laravel Breeze |
| Database | MySQL 8+ / MariaDB 10.4+ |
| Testing | PHPUnit 11 |
| Code style | Laravel Pint |
| Dev tools | Laravel Sail (Docker), Laravel Pail (log viewer) |
| UI Reference | Next.js (`reference_theme/`) |

---

## 🗂️ Cấu trúc dự án

```
fashionVN/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/          # AdminController, UserController, CategoryController, ...
│   │       ├── HomeController.php
│   │       ├── ProductController.php
│   │       ├── CartController.php
│   │       ├── CheckoutController.php
│   │       ├── WishlistController.php
│   │       └── ...
│   ├── Models/                 # Product, ProductVariant, Order, Cart, Wishlist, ...
│   └── Providers/
├── database/
│   ├── migrations/             # 30+ migration files
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── views/                  # Blade templates
│   ├── css/app.css             # Tailwind entry point
│   └── js/app.js               # Alpine.js entry point
├── routes/
│   ├── web.php                 # Toàn bộ route web
│   └── auth.php                # Route xác thực (Breeze)
├── public/                     # Web root
├── reference_theme/            # UI tham khảo (Next.js)
└── vite.config.js
```

---

## 🗄️ Database Schema

Dự án có **30+ bảng** được tổ chức theo nhóm chức năng:

| Nhóm | Bảng chính |
|---|---|
| Người dùng & Phân quyền | `users`, `roles`, `permissions`, `role_permissions` |
| Sản phẩm & Danh mục | `products`, `product_variants`, `product_images`, `categories`, `brands`, `collections`, `tags` |
| Thuộc tính biến thể | `attribute_groups`, `attribute_values`, `variant_attributes` |
| Kho hàng | `warehouses`, `inventory`, `suppliers`, `purchase_orders` |
| Mua sắm & Đặt hàng | `carts`, `cart_items`, `orders`, `order_items`, `coupons` |
| Thanh toán & Vận chuyển | `payment_methods`, `payments`, `shipping_methods`, `shipments` |
| Đánh giá & Hỗ trợ | `reviews`, `support_tickets`, `wishlists`, `banners` |
| Marketing & Analytics | `email_campaigns`, `analytics`, `reports` |

---

## 🚀 Hướng dẫn cài đặt

### Yêu cầu hệ thống

- PHP **8.2+** (extension: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`)
- Composer **2.x**
- Node.js **18+** & npm
- MySQL **8.0+** hoặc MariaDB **10.4+**

---

### Bước 1 — Clone repository

```bash
git clone <repo-url> fashionVN
cd fashionVN
```

### Bước 2 — Cài PHP dependencies

```bash
composer install
```

### Bước 3 — Cấu hình môi trường

```bash
# Windows
copy .env.example .env

# macOS / Linux
cp .env.example .env

# Tạo application key
php artisan key:generate
```

Mở `.env` và cập nhật thông tin database:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fashionvn
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 4 — Tạo database & chạy migration

```bash
# Tạo database (nếu chưa có)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS fashionvn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migrate + seed dữ liệu mẫu
php artisan migrate --seed
```

### Bước 5 — Cài frontend & tạo storage symlink

```bash
npm install
php artisan storage:link
```

---

## 🖥️ Chạy ứng dụng

### Cách 1 — One-command (khuyến nghị cho dev)

Chạy đồng thời PHP server, queue worker và Vite dev server:

```bash
composer run dev
```

Truy cập: **http://localhost:8000**

### Cách 2 — Tách riêng từng process

```bash
# Terminal 1: PHP server
php artisan serve

# Terminal 2: Vite dev server (hot reload)
npm run dev

# Terminal 3: Queue worker
php artisan queue:listen --tries=1
```

### Cách 3 — XAMPP

1. Khởi động **Apache** và **MySQL** trong XAMPP Control Panel.
2. Dự án đặt tại `C:\xampp\htdocs\fashionVN`.
3. Truy cập: **http://localhost/fashionVN/public**

> **Tip:** Tạo Virtual Host trong Apache để trỏ `DocumentRoot` thẳng tới `public/`, tránh phải gõ `/fashionVN/public` trong URL.

### Build production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ⚡ Setup tự động (one-liner)

Script `setup` trong `composer.json` tự động thực hiện toàn bộ:

```bash
composer run setup
```

Thực hiện theo thứ tự: `composer install` → copy `.env` → generate key → migrate → `npm install` → `npm run build`.

---

## 🧪 Kiểm thử

```bash
# Chạy toàn bộ test suite
composer run test

# Hoặc trực tiếp
php artisan test

# Chạy theo nhóm
php artisan test --filter=Feature
php artisan test --filter=Unit
```

---

## 🎨 Code Style

Dự án dùng **Laravel Pint** (cấu hình chuẩn Laravel):

```bash
./vendor/bin/pint
```

---

## ⚙️ Các lệnh Artisan thường dùng

```bash
php artisan optimize:clear     # Xóa toàn bộ cache
php artisan route:list         # Xem danh sách routes
php artisan tinker             # REPL tương tác với app
php artisan queue:work         # Chạy queue worker
```

---

## 📋 Biến môi trường quan trọng

| Biến | Mô tả | Mặc định |
|---|---|---|
| `APP_ENV` | Môi trường (`local` / `production`) | `local` |
| `APP_DEBUG` | Bật debug mode | `true` |
| `APP_URL` | URL gốc của ứng dụng | `http://localhost` |
| `DB_*` | Kết nối database | — |
| `MAIL_*` | Cấu hình gửi email | — |
| `QUEUE_CONNECTION` | Driver xử lý queue | `database` |
| `SESSION_DRIVER` | Driver lưu session | `database` |

---

## ⚠️ Lưu ý vận hành

- File `.env` **không** được commit lên repository.
- Đảm bảo `storage/` và `bootstrap/cache/` có quyền ghi:
  ```bash
  chmod -R 775 storage bootstrap/cache   # Linux/macOS
  ```
- Khi deploy production, bật cache để tăng hiệu năng:
  ```bash
  php artisan config:cache && php artisan route:cache && php artisan view:cache
  ```

---

## 🤝 Đóng góp

1. Fork repository
2. Tạo branch mới: `git checkout -b feature/ten-tinh-nang`
3. Commit: `git commit -m "feat: mô tả thay đổi"`
4. Push: `git push origin feature/ten-tinh-nang`
5. Mở **Pull Request** với mô tả rõ ràng

> Vui lòng chạy `./vendor/bin/pint` trước khi tạo PR.

---

## 📄 License

Dự án được phát hành theo giấy phép [MIT](LICENSE).

---

<div align="center">
  Made with ❤️ &nbsp;—&nbsp; FashionVN Team
</div>
