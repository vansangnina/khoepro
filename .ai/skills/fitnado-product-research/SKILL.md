---
name: fitnado-product-research
description: "Guidelines and rules for FITNADO Product Research, Candidate Pipeline, Scoring Engine, and Product Creation Workflow (Phase 03)."
---

# FITNADO PRODUCT RESEARCH & SCORING ENGINE SKILL

## 1. CỐT LÕI (CORE PRINCIPLES)

1. **Candidate != Live Product**:
   - Dữ liệu nghiên cứu sản phẩm (`table_product_research`) tách biệt hoàn toàn với sản phẩm chính thức trên website (`table_product`).
   - Tuyệt đối không cho phép AI hoặc Scraper tự động tạo sản phẩm hiển thị (`hienthi`) ra ngoài frontend.
2. **Quy trình Duyệt bắt buộc (Human Approval First)**:
   ```text
   DISCOVER PRODUCT
         ↓
   RESEARCH CANDIDATE (table_product_research)
         ↓
   NORMALIZE DATA (Name, URL, Metrics)
         ↓
   CALCULATE SCORE (0–100 Weighted Signal)
         ↓
   ADMIN REVIEW & DUPLICATE CHECK
         ↓
   APPROVE / REJECT (with Reason)
         ↓
   CREATE DRAFT PRODUCT (table_product, unpublished) + table_product_affiliate
         ↓
   READY FOR CONTENT
   ```
3. **Score != Winner**:
   - Điểm số (0–100) là tín hiệu ưu tiên nghiên cứu (**Research Priority Signal**), không phải bảo chứng bán chạy ("Guaranteed Winner").
4. **Xử lý Missing Data (`NULL != 0`)**:
   - Trường dữ liệu chưa biết (`NULL`) không bao giờ được ép thành `0`. Thuật toán chấm điểm chuẩn hóa theo các chiều đã có dữ liệu thực tế.
5. **Chống trùng lặp (Duplicate Detection)**:
   - Level 1: `platform + external_product_id` (Chặn/Cảnh báo đỏ).
   - Level 2: `normalized_url` (Bỏ UTM, affiliate tracking, query rác).
   - Level 3: `normalized_name + brand_hint` (Cảnh báo tương đồng).

---

## 2. TRỌNG SỐ CHẤM ĐIỂM (SCORING WEIGHTS)

* Mặc định:
  - **Demand (Nhu cầu)**: 30% (Lượt bán, Đánh giá sao, Số review, GMV)
  - **Content Potential (Tiềm năng Video/TikTok)**: 25% (Lượt view video, Creator count, Pain point giải quyết)
  - **Commission (Tiềm năng hoa hồng)**: 20% (Tỷ lệ hoa hồng %, Giá trị hoa hồng/đơn)
  - **Competition (Cơ hội cạnh tranh)**: 15% (Điểm cao = Ít bão hòa / nhiều đất diễn)
  - **SEO Opportunity (Cơ hội SEO)**: 10% (Từ khóa chính, Search intent)
* Tổng: **100%** (Admin có thể tùy chỉnh tại `index.php?com=product_research&act=weights`).

---

## 3. QUY TRÌNH ÁNH XẠ SẢN PHẨM (CANDIDATE → PRODUCT)

Khi Admin nhấn "Tạo sản phẩm" từ ứng viên đã duyệt (`APPROVED`):
1. Thêm bản ghi mới vào `table_product` với `type = 'san-pham'`, `status = ''` (chưa bật `hienthi`).
2. Map `name` → `namevi`, `price` → `regular_price`, `problem_solved` → `descvi`, `research_notes` → `specs`.
3. Ánh xạ danh mục thực (`id_list`, `id_cat`) và thương hiệu thực (`id_brand`).
4. Khởi tạo ưu đãi đầu tiên trong `table_product_affiliate` với platform và link affiliate.
5. Cập nhật `table_product_research.id_product` và trạng thái `PRODUCT_CREATED`.
6. Ngăn chặn tạo duplicate sản phẩm nếu `id_product` đã tồn tại.

---

## 4. QUY TẮC CODE (CODING RULES)

* **PHP 7.4 Compatibility**: Toàn bộ class, helper, controller phải chạy mượt mà trên PHP 7.4. Không dùng cú pháp PHP 8+.
* Sử dụng class `ProductResearch` (`libraries/class/class.ProductResearch.php`) cho toàn bộ logic chấm điểm, chuẩn hóa URL/tên, kiểm tra duplicate, và tạo sản phẩm.
