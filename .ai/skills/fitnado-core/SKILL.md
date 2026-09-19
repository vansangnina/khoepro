---
name: fitnado-core
description: Core skill bắt buộc phải nạp và tuân thủ cho tất cả các tác vụ lập trình trên dự án FITNADO.
---

# FITNADO CORE SKILL

Skill này định hình toàn bộ quy trình làm việc, tư duy và nguyên tắc nền tảng khi can thiệp vào mã nguồn FITNADO.

## 1. TÀI LIỆU BẮT BUỘC PHẢI ĐỌC TRƯỚC KHI LÀM VIỆC

Khi thực thi bất kỳ task nào, AI Agent **PHẢI** đọc qua các tài liệu sau trong thư mục `.ai/`:
1. `.ai/README.md`
2. `.ai/PROJECT.md`
3. `.ai/ARCHITECTURE.md`
4. `.ai/DATABASE.md`
5. `.ai/BUSINESS_RULES.md`
6. `.ai/CODING_RULES.md`
7. `.ai/knowledge/ERRORS.md`
8. `.ai/knowledge/DECISIONS.md`
9. `.ai/knowledge/LESSONS_LEARNED.md`

---

## 2. QUY TRÌNH BẮT BUỘC 8 BƯỚC (THE MANDATORY WORKFLOW)

```text
1. UNDERSTAND       -> Hiểu rõ yêu cầu, phạm vi và mục tiêu kinh doanh
2. LOCATE           -> Xác định chính xác file source, config, database table và template liên quan
3. PLAN             -> Tạo kế hoạch thực thi rõ ràng (Implementation Plan)
4. MINIMUM CHANGE   -> Chỉ chỉnh sửa đúng phạm vi cần thiết, tương thích 100% PHP 7.4
5. TEST             -> Chạy kiểm tra cú pháp php -l và kiểm tra chức năng
6. REGRESSION       -> Kiểm tra các module phụ thuộc xung quanh
7. UPDATE KNOWLEDGE -> Ghi nhận bài học hoặc lỗi mới vào .ai/knowledge/
8. CHANGELOG        -> Cập nhật nhật ký công việc vào .ai/CHANGELOG.md
9. GIT SYNC         -> Tự động commit và push toàn bộ thay đổi lên GitHub (origin/main)
```

---

## 3. RÀNG BUỘC KỸ THUẬT KHÔNG THỂ THƯƠNG LƯỢNG

* **PHP 7.4 Hard Requirement**: Không dùng cú pháp PHP 8+ (`match`, `str_contains`, `str_starts_with`, `?->`, `union types`, `named arguments`...).
* **Không làm gãy kiến trúc cũ**: Sử dụng `$d` (PDODb), `$func` (Functions), `$cache` (Cache), AltoRouter và template includes.
* **No Mockup in PHP**: Không bao giờ hardcode dữ liệu giả trên Frontend.

---

## 4. QUY TẮC TỰ ĐỘNG ĐỒNG BỘ GITHUB (MANDATORY GIT AUTO-PUSH)

* **Bắt buộc đồng bộ**: Sau khi hoàn thành bất kỳ tác vụ nào (tính năng mới, sửa lỗi, cập nhật tài liệu, test...), Agent **BẮT BUỘC** phải tự động chạy lệnh commit và đẩy lên GitHub remote:
  ```bash
  git add .
  git commit -m "<type>: <mô tả thay đổi súc tích>"
  git push origin main
  ```
* **Repository đích**: `https://github.com/vansangnina/khoepro` (branch `main`).
* **Không để đọng thay đổi (Zero Uncommitted Changes)**: Kết thúc mỗi phiên làm việc, working tree phải hoàn toàn sạch (`working tree clean`).
