---
name: fitnado-affiliate
description: Affiliate skill định nghĩa kiến trúc và quy tắc điều hướng, theo dõi link tiếp thị liên kết FITNADO.
---

# FITNADO AFFILIATE SKILL

> [!NOTE]
> Skill này mô tả kiến trúc định hướng cho module Affiliate. Chưa triển khai code trong Task 00.

---

## 1. KIẾN TRÚC AFFILIATE DỰ KIẾN (TARGET ARCHITECTURE)

```text
Product (table_product)
      │
      ▼
Affiliate Sources (Shopee, Lazada, TikTok Shop, Brand Web)
      │
      ▼
Outbound Redirect Router (/go/{product_slug} hoặc /aff/{id})
      │
      ▼
Click Logging & Tracking (table_affiliate_logs: IP, User-Agent, Referer, Timestamp)
      │
      ▼
Destination URL (Link Affiliate có gắn UTM / Tracking Tag)
```

---

## 2. NGUYÊN TẮC BẮT BUỘC

1. **Quản lý qua Database/Admin**: Link Affiliate phải được cấu hình trong Admin/Database (ví dụ: trường `affiliate_link` hoặc bảng con `table_product_affiliate`), tuyệt đối không hardcode link Affiliate trong view template.
2. **Theo dõi chuyển hướng an toàn**: Mọi link out phải được điều hướng thông qua router nội bộ để có thể kiểm soát redirect, gắn thẻ rel="nofollow sponsored" chống phạt SEO và ghi log thống kê số lượt click.
3. **Hiển thị minh bạch**: Tuân thủ luật tiếp thị liên kết (đính kèm thông báo "Liên kết có thể là liên kết tiếp thị liên kết/affiliate").
