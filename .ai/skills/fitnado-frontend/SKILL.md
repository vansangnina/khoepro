---
name: fitnado-frontend
description: Frontend skill quy định cách chuyển đổi HTML Prototype thành Dynamic Template kết hợp dữ liệu Backend thực tế.
---

# FITNADO FRONTEND SKILL

## 1. TAM GIÁC CHÂN LÝ FRONTEND (THE FRONTEND TRINITY)

Mọi component giao diện FITNADO phải được hình thành từ sự kết hợp của 3 nguồn chân lý:

```text
       ┌────────────────────────┐
       │     PROTOTYPE          │  --> Chân lý về thị giác, CSS tokens, layout (Visual Truth)
       └───────────┬────────────┘
                   │
                   ▼
       ┌────────────────────────┐
       │     BACKEND / DB       │  --> Chân lý về cấu trúc dữ liệu thực tế (Data Truth)
       └───────────┬────────────┘
                   │
                   ▼
       ┌────────────────────────┐
       │     CONFIG / TYPE      │  --> Chân lý về cấu hình kích thước, option (Config Truth)
       └────────────────────────┘
```

---

## 2. NGUYÊN TẮC THI CÔNG GIAO DIỆN

1. **Cấm Copy-Paste thô bạo (No Dumb Copy-Paste)**:
   * Không copy nguyên khối HTML chứa dữ liệu demo vào file `templates/*.php`.
   * Bóc tách thành các biến dữ liệu: `$productHot`, `$newsHot`, `$videoHot`, `$proListHot`... được query từ `sources/*.php`.
2. **Xử lý trạng thái rỗng (Empty States)**:
   * Luôn bọc component trong câu lệnh điều kiện:
     ```php
     <?php if (!empty($productHot)) { ?>
         <div class="fitnado-products">
             <?php foreach ($productHot as $v) { ?>
                 <!-- Card Item -->
             <?php } ?>
         </div>
     <?php } ?>
     ```
3. **Responsive Chuẩn**:
   * Áp dụng Design Tokens từ `.ai/DESIGN_SYSTEM.md` và `assets/css/fitnado.css`.
   * Đảm bảo trải nghiệm tối ưu trên cả Desktop (1440px/1920px) và Mobile (360px - 430px).
