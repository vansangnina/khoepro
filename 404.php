<?php
http_response_code(404);
$base = !empty($configBase) ? $configBase : 'https://khoepro.com/';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | Khỏe Pro</title>
    <meta name="robots" content="noindex,follow" />
    <meta name="description" content="Trang bạn yêu cầu không tồn tại hoặc đã được chuyển sang địa chỉ mới trên Khỏe Pro." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; }
        body { background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; text-align: center; }
        .box-404 { max-width: 560px; background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 48px 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .num-404 { font-size: 80px; font-weight: 600; color: #38bdf8; line-height: 1; margin-bottom: 16px; letter-spacing: -2px; }
        h1 { font-size: 22px; font-weight: 600; color: #ffffff; margin-bottom: 12px; }
        p { font-size: 15px; color: #94a3b8; line-height: 1.6; margin-bottom: 28px; }
        .nav-links { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .btn-home { background: #0284c7; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s; }
        .btn-home:hover { background: #0369a1; }
        .btn-link { background: #334155; color: #cbd5e1; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.2s; }
        .btn-link:hover { background: #475569; color: #ffffff; }
    </style>
</head>
<body>
    <div class="box-404">
        <div class="num-404">404</div>
        <h1>Không tìm thấy nội dung yêu cầu</h1>
        <p>Trang bạn đang truy cập không tồn tại, đã bị xóa hoặc đường dẫn đã thay đổi. Vui lòng sử dụng các liên kết dưới đây để tiếp tục khám phá Khỏe Pro.</p>
        <div class="nav-links">
            <a href="<?= $base ?>" class="btn-home">Về Trang chủ</a>
            <a href="<?= $base ?>san-pham" class="btn-link">Sản phẩm</a>
            <a href="<?= $base ?>danh-gia-review" class="btn-link">Đánh giá & Review</a>
            <a href="<?= $base ?>huong-dan-chon-mua" class="btn-link">Hướng dẫn chọn mua</a>
        </div>
    </div>
</body>
</html>