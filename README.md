# 👗 FashionVN — Nền Tảng Thương Mại Thời Trang Hiện Đại

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-5.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
</p>

> **FashionVN** là nền tảng thương mại điện tử thời trang phụ nữ cao cấp, được xây dựng với Laravel & Blade Templates. Giao diện mượt mà, Mega Menu đa cấp, bố cục masonry chuẩn thiết kế — sẵn sàng cho môi trường production.

---

## ✨ Tính Năng Nổi Bật

| Tính năng | Mô tả |
|---|---|
| 🗂 **Mega Menu 5 danh mục** | Bộ Sưu Tập, Hàng Mới, Modiweek, Kích Thước, Sản Phẩm Xanh — mở bằng click, đóng khi click ngoài |
| 🏠 **Trang Chủ Nổi Bật** | Hero banner, Bán Chạy Nhất, Bộ Sưu Tập bento grid, Modiweek gallery, Instagram Feed |
| 🖼 **Masonry Collection** | Grid ảnh động tỷ lệ chuyên biệt theo từng danh mục |
| 📱 **Responsive** | Tối ưu cho cả desktop và thiết bị di động |
| ⚡ **Vite Build** | Hot Module Replacement khi phát triển, bundle tối ưu khi production |
| 🍃 **Sản Phẩm Xanh** | Trang riêng cho chính sách bền vững và thân thiện môi trường |

---

## 🛠 Công Nghệ Sử Dụng

- **Backend:** Laravel 12 (PHP 8.x), Eloquent ORM, Blade Templates
- **Frontend:** Tailwind CSS 3, Vite, Vanilla JavaScript
- **Database:** MySQL / MariaDB
- **Assets:** WebP, AVIF (tối ưu hiệu năng ảnh)

---

## 🚀 Cài Đặt Nhanh

### Yêu cầu

- PHP >= 8.2
- Composer
- Node.js >= 18 & npm
- MySQL hoặc MariaDB

### Các bước

```bash
# 1. Clone dự án
git clone https://github.com/giangvan767401/fashionVN.git
cd fashionVN

# 2. Cài đặt dependency PHP
composer install

# 3. Cấu hình môi trường
copy .env.example .env
php artisan key:generate

# 4. Cài đặt frontend
npm install
npm run build

# 5. Chạy migration
php artisan migrate --seed

# 6. Khởi động server
php artisan serve
```

Truy cập ứng dụng tại: **`http://127.0.0.1:8000`**

---

## 🗂 Cấu Trúc Thư Mục

```
fashionVN/
├── app/                  # Models, Controllers, Middleware
├── resources/
│   ├── css/              # Tailwind CSS & styles
│   ├── js/               # JavaScript modules
│   └── views/
│       ├── layouts/      # Layout chính (app.blade.php)
│       ├── home.blade.php
│       └── collection.blade.php
├── routes/
│   └── web.php           # Định nghĩa route
└── public/
    └── user/img/         # Toàn bộ assets hình ảnh
```

---

## ⚙️ Lệnh Hữu Ích

```bash
# Phát triển (hot reload)
npm run dev

# Build production
npm run build

# Chạy tests
php artisan test

# Xoá cache
php artisan optimize:clear
```

---

## 🌿 Lưu Ý

- File `.env` **không** được commit — hãy tự cấu hình thông tin database.
- Đảm bảo thư mục `storage/` và `bootstrap/cache/` có quyền ghi.
- Khi deploy production, chạy `php artisan config:cache` và `php artisan route:cache`.

---

## 📄 Giấy Phép

Dự án thuộc sở hữu của **[giangvan767401](https://github.com/giangvan767401)** — Mọi đóng góp xin mở Pull Request hoặc Issue.
