---
name: fitnado-ai-content
description: FITNADO AI Content Engine Skill - Quy trình sinh nội dung AI đa kênh, kiểm định chất lượng và áp dụng vào sản phẩm
---

# FITNADO AI CONTENT ENGINE SKILL

## 1. TỔNG QUAN HỆ THỐNG
**AI Content Engine (Phase 05)** là hệ thống sinh nội dung AI chuyên nghiệp cho FITNADO, tổng hợp dữ liệu thực tế từ:
- `table_product` (Thuộc tính, phân loại, thông số kỹ thuật)
- `table_product_research` (Dữ liệu nghiên cứu thị trường, điểm số 5 chiều, pain point giải pháp)
- `table_product_research_evidence` (Bằng chứng thực tế từ sàn/crawler)
- `table_product_affiliate` (Ưu đãi đa sàn, Best Deal, mã giảm giá)
- `table_comment` (Đánh giá người dùng thật đã được duyệt)

---

## 2. QUY TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE CORE RULES)

1. **FACT > AI ANALYSIS**:
   - Dữ liệu thực tế sàn là chân lý tối thượng.
   - AI không bao giờ được tự bịa đặt số liệu lượt bán (`sales_count`), đánh giá sao (`rating`), số lượng review (`review_count`), tỷ lệ hoa hồng (`commission_rate`).
   - Nếu dữ liệu nguồn không có, bắt buộc để `NULL`.
2. **UNKNOWN = UNKNOWN**:
   - Thông tin chưa biết phải giữ nguyên là chưa biết, không được suy diễn thành sự thật.
3. **CẤM TỰ XƯNG TRẢI NGHIỆM CÁ NHÂN (NO FAKE REAL-TEST CLAIM)**:
   - Cấm các câu: *"Tôi đã dùng sản phẩm 30 ngày..."*, *"FITNADO đã test thực tế..."* trừ khi hệ thống có bằng chứng `REAL_TEST` trong `table_product_research_evidence`.
4. **CẤM TẠO LỜI CHỨNG THỰC GIẢ (NO FAKE REVIEWS / TESTIMONIALS)**:
   - Trích dẫn nhận xét người dùng chỉ được lấy từ `table_comment` thật đã duyệt `hienthi`.
5. **AN TOÀN Y TẾ & THỂ LỰC (FITNESS SAFETY)**:
   - Tuyệt đối không đưa ra cam kết điều trị y khoa: *"chữa đau lưng"*, *"trị dứt điểm thoát vị đĩa đệm"*, *"cam kết tăng 5kg cơ bắp"*.
   - Ưu tiên các từ ngữ chuẩn mực: *"hỗ trợ bảo vệ"*, *"phù hợp cho"*, *"giúp giảm áp lực lên cổ tay"*.
6. **CỔNG DUYỆT CON NGƯỜI (STRICT HUMAN GATE)**:
   ```text
   GENERATED → REVIEW_REQUIRED → APPROVED → APPLIED
   ```
   - Nội dung mới tạo luôn dừng ở `REVIEW_REQUIRED`.
   - Tuyệt đối không auto-publish ra ngoài website nếu chưa có phê duyệt từ Admin.
7. **PHIÊN BẢN HÓA & KHÔNG GHI ĐÈ (VERSION EVERYTHING & ZERO OVERWRITE)**:
   - Mỗi lần generate tạo ra version mới (`v1, v2, v3...`), giữ nguyên các version cũ trong lịch sử.
   - Chỉ duy nhất 1 version được kích hoạt `is_active = 1` tại một thời điểm.
8. **SAO LƯU TRƯỚC KHI ÁP DỤNG (REVERSIBLE BACKUP)**:
   - Khi áp dụng nội dung AI vào sản phẩm, hệ thống bắt buộc tự động sao lưu nội dung cũ vào `table_product_content_backup`.
9. **OUTDATED DETECTION**:
   - Sử dụng `source_hash` SHA-256 xác định tính toàn vẹn của dữ liệu đầu vào. Khi thông tin nghiên cứu thay đổi lớn, nội dung cũ tự động gắn cờ `is_outdated = 1`.

---

## 3. DANH MỤC GÓI NỘI DUNG (CONTENT PACKAGE)

| Loại nội dung | Prompt Version | Output & Mục tiêu |
|---|---|---|
| `product_analysis` | `product-analysis-v1` | Phân tích 12 khía cạnh: problem_solved, target_audience, key_benefits, limitations, pros, cons, suitable_for, not_suitable_for, buying_considerations, comparison_angles, risk_notes, confidence |
| `tiktok_hooks` | `tiktok-hooks-v1` | 7 loại hook chiến lược: Problem, Mistake, Comparison, Curiosity, Demo, Buyer Warning, Value |
| `tiktok_script` | `tiktok-script-v1` | Kịch bản 15s/30s/45s/60s kèm bảng phân cảnh Video Shot Plan chi tiết từng cảnh (Cầu nối Phase 06) |
| `seo_content` | `seo-v1` | Primary/Secondary Keywords, Search Intent, SEO Title, Meta Description, Outline, FAQ Schema |
| `review_draft` | `review-draft-v1` | Bản thảo bài viết đánh giá chuyên sâu hoàn chỉnh với các Heading H2/H3 |
| `faq` | `faq-v1` | Bộ câu hỏi thường gặp và hướng dẫn sử dụng / bảo quản |

---

## 4. QUY TRÌNH ÁP DỤNG VÀO SẢN PHẨM (APPLY TO PRODUCT WORKFLOW)

```text
Admin chọn nội dung đã APPROVED
       ↓
Màn hình So Sánh Trực Quan (Diff View: Current Content vs AI Content)
       ↓
Admin nhấn "Xác nhận Áp Dụng"
       ↓
Tạo bản sao lưu vào table_product_content_backup
       ↓
Cập nhật table_product hoặc table_seo
       ↓
Chuyển trạng thái nội dung thành APPLIED
```
