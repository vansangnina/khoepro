# CSS TYPOGRAPHY & FONT-WEIGHT RULE

## Quy định bắt buộc về font-weight khi viết CSS:
1. **Font-weight tối đa là 600**:
   - Tuyệt đối KHÔNG sử dụng `font-weight: 700`, `font-weight: 800`, `font-weight: 900`, `font-weight: bold`, `font-weight: bolder`.
   - Mọi tiêu đề (H1, H2, H3, H4, H5, H6), giá tiền, badge, nút bấm, điểm nhấn, phần cần in đậm đều dùng tối đa `font-weight: 600` (Semi-bold).

2. **Các mức font-weight được phép sử dụng**:
   - `font-weight: 400` hoặc `normal`: Văn bản thông thường, đoạn văn bản (paragraphs).
   - `font-weight: 500`: Text phụ, nhãn nhỏ (labels), metadata, phụ lục.
   - `font-weight: 600`: Mức cao nhất cho toàn bộ tiêu đề, logo, tiêu đề card, nút bấm, số liệu nổi bật.

3. **Font family chuẩn**:
   - Luôn sử dụng `'Inter', Arial, sans-serif` làm font chính, ngoại trừ các icon FontAwesome được bảo vệ riêng.
