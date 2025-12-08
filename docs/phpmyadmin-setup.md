# Chuyển ứng dụng sang MySQL / phpMyAdmin (XAMPP)

Hướng dẫn nhanh để chạy ứng dụng trên MySQL (phpMyAdmin) — áp dụng cho môi trường XAMPP local.

1) Tạo database trong phpMyAdmin

- Mở http://localhost/phpmyadmin
- Tạo database, ví dụ tên `laravel` (hoặc tên bạn muốn). Trong ví dụ này ta dùng `laravel`.

2) Cập nhật `.env`

Thay đổi các dòng DB trong file `.env` ở root project như sau:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

3) Xóa cache cấu hình và build lại

Mở terminal (PowerShell) ở thư mục project và chạy:

```powershell
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

4) Chạy migrations + seed admin

```powershell
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

Nếu bạn muốn làm sạch mọi thứ và tái tạo từ đầu (cẩn thận — sẽ xóa dữ liệu hiện tại):

```powershell
php artisan migrate:fresh --seed
```

5) Kiểm tra admin tồn tại

Bạn có thể kiểm tra bằng phpMyAdmin (bảng `users`) hoặc dùng các script tiện ích trong `tools/`:

```powershell
php tools/check_admin.php
php tools/check_admin_password.php
```

Những script này tự phát hiện `DB_CONNECTION` từ `.env` và hỗ trợ MySQL lẫn SQLite.

6) Ghi chú / lưu ý

- Một số code trong project là driver-aware; nhưng nếu thấy lỗi SQL driver-specific, hãy báo cho tôi và tôi sẽ sửa.
- Nếu bạn cần tính năng Firebase, hãy đặt file JSON credentials vào `storage/app/firebase/credentials.json` hoặc gán `FIREBASE_CREDENTIALS` trong `.env`.

---

Nếu muốn, tôi sẽ thêm các bước để rollback hoặc hỗ trợ di chuyển dữ liệu từ SQLite → MySQL.
