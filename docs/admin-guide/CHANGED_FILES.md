# DANH SÁCH TỆP THAY ĐỔI & TẠO MỚI (CHANGED FILES)

## 1. TỆP TẠO MỚI (NEW FILES)

### 1.1 Thư mục Hướng dẫn cùng cấp `index.php` (`/huongdan/`)
- `huongdan/index.php`: Trang portal xem hướng dẫn sử dụng trực tiếp / standalone.
- `huongdan/data/guide_data.php`: Kho lưu trữ dữ liệu hướng dẫn, danh mục, cấu trúc các bước, bộ lọc tìm kiếm và từ khóa.
- `huongdan/generate_svg_screenshots.php`: Bộ sinh 44 ảnh chụp giao diện minh họa thực tế dạng SVG vector.
- `huongdan/images/` (44 files SVG):
  - `dashboard-overview.svg`
  - `dashboard-header-notify.svg`
  - `dashboard-quick-actions.svg`
  - `research-candidates-list.svg`
  - `research-candidate-detail.svg`
  - `research-create-product.svg`
  - `research-seed-add.svg`
  - `research-seed-form.svg`
  - `research-seed-save.svg`
  - `ai-content-filter.svg`
  - `ai-content-status-badges.svg`
  - `ai-content-approve-action.svg`
  - `ai-content-select-product.svg`
  - `ai-content-options.svg`
  - `ai-content-generate-btn.svg`
  - `ai-video-project-list.svg`
  - `ai-video-preview-player.svg`
  - `ai-video-approve-btn.svg`
  - `ai-video-create-step1.svg`
  - `ai-video-create-step2.svg`
  - `ai-video-create-step3.svg`
  - `publishing-create-step1.svg`
  - `publishing-create-step2.svg`
  - `publishing-create-step3.svg`
  - `publishing-checklist-scan.svg`
  - `publishing-checklist-pass.svg`
  - `publishing-snapshot-lock.svg`
  - `analytics-time-filter.svg`
  - `analytics-chart-trend.svg`
  - `analytics-funnel.svg`
  - `winner-leaderboard.svg`
  - `winner-criteria-detail.svg`
  - `winner-scale-action.svg`
  - `product-list-filter.svg`
  - `product-quick-status-toggle.svg`
  - `product-action-buttons.svg`
  - `product-add-step1-image.svg`
  - `product-add-step2-category-price.svg`
  - `product-add-step3-content.svg`
  - `product-add-step4-affiliate-seo.svg`
  - `product-add-step5-save.svg`
  - `operations-health-badge.svg`
  - `operations-workers-status.svg`
  - `operations-costs-summary.svg`

### 1.2 Module Hướng dẫn trong Admin (`/admin/`)
- `admin/sources/huongdan.php`: Controller điều hướng danh sách, tìm kiếm và chi tiết bài hướng dẫn.
- `admin/templates/huongdan/man/items_tpl.php`: Template giao diện tìm kiếm, danh mục và lưới bài viết.
- `admin/templates/huongdan/man/detail_tpl.php`: Template giao diện đọc chi tiết bài hướng dẫn, hiển thị các bước, ảnh minh họa thực tế, lưu ý và xử lý lỗi.

### 1.3 Tài liệu Báo cáo & Audit (`/docs/admin-guide/`)
- `docs/admin-guide/ADMIN_FUNCTION_AUDIT.md`: Báo cáo audit toàn bộ 58 chức năng trong Admin.
- `docs/admin-guide/ADMIN_GUIDE_COVERAGE.md`: Ma trận độ phủ 100% hướng dẫn.
- `docs/admin-guide/ADMIN_GUIDE_DISCOVERED_ISSUES.md`: Nhật ký ghi nhận các vấn đề & lưu ý kỹ thuật.
- `docs/admin-guide/CHANGED_FILES.md`: Bản ghi danh sách tệp thay đổi này.
- `docs/admin-guide/ADMIN_GUIDE_IMPLEMENTATION_REPORT.md`: Báo cáo hoàn thành và hướng dẫn triển khai.

---

## 2. TỆP CHỈNH SỬA (MODIFIED FILES)
- `admin/templates/layout/menu.php`: Thêm mục menu "HƯỚNG DẪN SỬ DỤNG" ngay sau Dashboard với icon nổi bật.
- `admin/templates/layout/header.php`: Thêm nút bấm nhanh "Hướng Dẫn Sử Dụng" trên thanh Top Navbar để truy cập tức thì từ bất kỳ trang nào.

---

## 3. THAY ĐỔI CƠ SỞ DỮ LIỆU (DATABASE CHANGES)
- **Database Schema Changes**: `NONE` (Không thay đổi cấu trúc bảng CSDL).
- **Database Data Changes**: `NONE` (Không tác động đến dữ liệu sản phẩm, đơn hàng hay người dùng).
- **Tính an toàn**: Hoàn toàn tương thích và không ảnh hưởng đến bất kỳ tính năng hiện hữu nào.
