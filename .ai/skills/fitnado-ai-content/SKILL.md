---
name: fitnado-ai-content
description: AI Content skill quy định luồng xử lý bài viết và kịch bản video AI cho FITNADO.
---

# FITNADO AI CONTENT SKILL

## 1. QUY TRÌNH SẢN XUẤT NỘI DUNG AI (AI CONTENT WORKFLOW)

```text
1. Product Research     -> Thu thập dữ liệu kỹ thuật, thành phần, công năng, giá cả
2. Hook & Angle         -> Xác định góc tiếp cận hấp dẫn (Ví dụ: "3 Sai Lầm Khi Mua Lifting Straps")
3. Draft Content/Script -> Sinh bài viết review hoặc kịch bản video 30s trực quan
4. Review & Moderate    -> Biên tập viên kiểm duyệt độ chính xác thông số kỹ thuật
5. Store in Database    -> Lưu vào table_news hoặc table_photo/video với trạng thái kiểm duyệt
6. Publish              -> Đưa lên Website & Phân phối lên kênh TikTok
```

---

## 2. NGUYÊN TẮC MINH BẠCH & KIỂM DUYỆT

1. **Không tự động xuất bản mà không qua database**: Nội dung sinh ra phải được lưu vào database và quản lý qua Admin, không được inject trực tiếp vào file mã nguồn.
2. **Không giả mạo trải nghiệm thực tế (`REAL_TEST`)**: Nếu bài viết được tạo hoàn toàn bằng phân tích dữ liệu AI (`AI_ANALYSIS`), cấm tự xưng là đã cầm/nắm/thử nghiệm trực tiếp tại phòng gym.
3. **Cấu trúc kịch bản video 30s chuẩn**:
   * **0-3s**: Hook gây chú ý (Vấn đề thường gặp của gymmer).
   * **3-15s**: Trực quan hóa sản phẩm / Test nhanh điểm mấu chốt.
   * **15-25s**: So sánh hoặc chỉ ra ưu/nhược điểm rõ ràng.
   * **25-30s**: Call to action (Xem review chi tiết tại link bio / website FITNADO).
