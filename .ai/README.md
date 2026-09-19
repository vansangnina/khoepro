# BỘ NÃO AI FITNADO (.ai/)

> [!IMPORTANT]
> **MỌI AI AGENT LÀM VIỆC TRÊN PROJECT FITNADO PHẢI ĐỌC FILE NÀY TRƯỚC.**
> Tuyệt đối không thay đổi business logic, cấu trúc database production, hoặc refactor framework khi chưa có yêu cầu và kế hoạch được duyệt.

---

## 1. TỔNG QUAN HỆ THỐNG TÀI LIỆU & QUY TẮC

Tất cả các hướng dẫn, kiến trúc, database schema, quy chuẩn code và design system của dự án FITNADO được lưu trữ tập trung trong thư mục `.ai/`:

| Tài liệu / Thư mục | Mục đích & Nội dung chính |
| :--- | :--- |
| [PROJECT.md](file:///Volumes/CD/web_2026/masterpdo/.ai/PROJECT.md) | Bối cảnh dự án, mục tiêu kinh doanh, ràng buộc kỹ thuật (PHP 7.4, MySQL, Nina MasterPDO). |
| [ARCHITECTURE.md](file:///Volumes/CD/web_2026/masterpdo/.ai/ARCHITECTURE.md) | Vòng đời request, AltoRouter, Requick, mô hình MVC/Source-Template, luồng dữ liệu thực tế. |
| [DATABASE.md](file:///Volumes/CD/web_2026/masterpdo/.ai/DATABASE.md) | Bản đồ cấu trúc các bảng MySQL (`table_product*`, `table_news*`, `table_gallery`, `table_comment*`...). |
| [BUSINESS_RULES.md](file:///Volumes/CD/web_2026/masterpdo/.ai/BUSINESS_RULES.md) | Quy tắc nghiệp vụ FITNADO: Product Discovery, Review thực tế, Affiliate, AI Content/Video. |
| [DESIGN_SYSTEM.md](file:///Volumes/CD/web_2026/masterpdo/.ai/DESIGN_SYSTEM.md) | Design tokens (`#0256AA`), typography, component cards, breakpoints, spacing trích xuất từ Prototype. |
| [ADMIN_RULES.md](file:///Volumes/CD/web_2026/masterpdo/.ai/ADMIN_RULES.md) | Quy chuẩn phát triển Admin: routing `com/act/type`, CRUD, upload ảnh/file, phân quyền, SEO. |
| [CODING_RULES.md](file:///Volumes/CD/web_2026/masterpdo/.ai/CODING_RULES.md) | Ràng buộc code nghiêm ngặt: PHP 7.4 compatibility, không hardcode frontend, config-first. |
| [TESTING.md](file:///Volumes/CD/web_2026/masterpdo/.ai/TESTING.md) | Tiêu chuẩn kiểm thử: `php -l`, browser test, multi-device viewport (360px - 1920px), regression. |
| [CHANGELOG.md](file:///Volumes/CD/web_2026/masterpdo/.ai/CHANGELOG.md) | Nhật ký thay đổi hệ thống thực hiện bởi AI Agents. |
| [knowledge/](file:///Volumes/CD/web_2026/masterpdo/.ai/knowledge/) | Tri thức tích lũy qua các task: `ERRORS.md`, `DECISIONS.md`, `LESSONS_LEARNED.md`. |
| [skills/](file:///Volumes/CD/web_2026/masterpdo/.ai/skills/) | Tập hợp kỹ năng chuyên môn: Core, Frontend, Admin, Affiliate, AI Content, Regression. |
| [plans/](file:///Volumes/CD/web_2026/masterpdo/.ai/plans/) | Kế hoạch chi tiết theo từng Phase (Phase 01, Phase 02...). |
| [reports/](file:///Volumes/CD/web_2026/masterpdo/.ai/reports/) | Báo cáo kiểm định thực tế (`SOURCE_AUDIT.md`, `DATABASE_AUDIT.md`...). |

---

## 2. QUY TRÌNH LÀM VIỆC BẮT BUỘC CHO AI AGENT

Mỗi khi nhận một task mới trên FITNADO, AI Agent phải tuân thủ nghiêm ngặt chu trình 7 bước sau:

```text
       ┌──────────────┐
       │   1. READ    │ ── Đọc .ai/README.md, rules, architecture, database & knowledge
       └──────┬───────┘
              ▼
       ┌──────────────┐
       │   2. PLAN    │ ── Lập Implementation Plan (viết vào .ai/plans/ nếu là task lớn)
       └──────┬───────┘
              ▼
       ┌──────────────┐
       │ 3. IMPLEMENT │ ── Thực hiện thay đổi tối thiểu, tương thích PHP 7.4, config-first
       └──────┬───────┘
              ▼
       ┌──────────────┐
       │   4. TEST    │ ── Syntax check (php -l), functional test, UI responsive test
       └──────┬───────┘
              ▼
       ┌──────────────┐
       │5. REGRESSION │ ── Kiểm tra toàn diện các module liên quan (Detail, List, Admin, SEO)
       └──────┬───────┘
              ▼
       ┌──────────────┐
       │ 6. KNOWLEDGE │ ── Cập nhật bài học, lỗi phát hiện, quyết định kỹ thuật mới
       └──────┬───────┘
              ▼
       ┌──────────────┐
       │ 7. CHANGELOG │ ── Ghi nhận chi tiết vào .ai/CHANGELOG.md
       └──────────────┘
```

---

## 3. NGUYÊN TẮC CỐT LÕI (CORE PRINCIPLES)

1. **Phát triển dựa trên source hiện tại**: Tuyệt đối không tự ý chuyển đổi sang Laravel, Vue, React, Next.js. Tận dụng triệt để nền tảng kiến trúc Nina MasterPDO hiện hữu.
2. **PHP 7.4 Hard Requirement**: Không dùng syntax PHP 8+ (`match`, `str_contains`, `str_starts_with`, `?->`, `union types`, `named arguments`, `property promotion`, `attributes`).
3. **Dữ liệu thật (Backend & Database Truth)**: UI frontend chỉ hiển thị khi có dữ liệu từ Database/Config. Cấm hardcode dữ liệu giả vào template PHP.
4. **Không bịa thông tin**: Nếu một module, helper hay bảng dữ liệu chưa tồn tại, phải ghi rõ `NOT FOUND` hoặc `NEEDS VERIFICATION`.
