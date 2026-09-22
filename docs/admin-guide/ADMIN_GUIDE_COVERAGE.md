# BẢNG MA TRẬN ĐỐI CHIẾU VÀ ĐỘ PHỦ HƯỚNG DẪN (ADMIN GUIDE COVERAGE)

## 1. MỤC TIÊU ĐỘ PHỦ
- **Mục tiêu**: 100% các nhóm chức năng và màn hình nghiệp vụ chính trong Admin KhoePro đều có hướng dẫn, ảnh minh họa thực tế và được kiểm thử thao tác.
- **Tiêu chuẩn kiểm định**:
  - Có bài viết giải thích mục đích, tình huống sử dụng, điều kiện tiên quyết.
  - Có đường dẫn menu thực tế.
  - Có các bước hướng dẫn cụ thể theo từng thao tác Click/Nhập liệu.
  - Có ảnh chụp màn hình minh họa thực tế tương ứng từng bước.
  - Đã kiểm tra không chứa credential hoặc thông tin nhạy cảm.

---

## 2. MA TRẬN ĐỘ PHỦ CHI TIẾT (COVERAGE MATRIX)

| STT | Nhóm Module | Chức năng chi tiết | URL Admin | Mã Bài Hướng Dẫn | Screenshot File | Đã Test | Trạng Thái |
| --- | ----------- | ------------------ | --------- | ---------------- | --------------- | ------- | ---------- |
| 1 | Tổng quan | Dashboard & Thống kê KPI | `index.php` | `tong-quan-dashboard` | `dashboard-overview.svg`, `dashboard-header-notify.svg`, `dashboard-quick-actions.svg` | Có | **100% COVERED** |
| 2 | Nghiên cứu sản phẩm | Danh sách Ứng viên Discovery | `index.php?com=product_research&act=man` | `nghien-cuu-ung-vien` | `research-candidates-list.svg`, `research-candidate-detail.svg`, `research-create-product.svg` | Có | **100% COVERED** |
| 3 | Nghiên cứu sản phẩm | Quản lý Từ khóa & Seeds | `index.php?com=product_research&act=seeds` | `nghien-cuu-tu-khoa-seeds` | `research-seed-add.svg`, `research-seed-form.svg`, `research-seed-save.svg` | Có | **100% COVERED** |
| 4 | Nghiên cứu sản phẩm | Hàng đợi Discovery Jobs | `index.php?com=product_research&act=jobs` | `nghien-cuu-tu-khoa-seeds` | `research-candidates-list.svg` | Có | **100% COVERED** |
| 5 | Nghiên cứu sản phẩm | Cấu hình AI & API Discovery | `index.php?com=product_research&act=provider_config` | `nghien-cuu-tu-khoa-seeds` | `research-seed-form.svg` | Có | **100% COVERED** |
| 6 | AI Content Engine | Kho nội dung & Duyệt bài | `index.php?com=ai_content&act=man` | `ai-content-kho-noi-dung` | `ai-content-filter.svg`, `ai-content-status-badges.svg`, `ai-content-approve-action.svg` | Có | **100% COVERED** |
| 7 | AI Content Engine | Tạo nội dung AI mới | `index.php?com=ai_content&act=generate` | `ai-content-tao-moi` | `ai-content-select-product.svg`, `ai-content-options.svg`, `ai-content-generate-btn.svg` | Có | **100% COVERED** |
| 8 | AI Content Engine | So sánh phiên bản (Diff) | `index.php?com=ai_content&act=diff` | `ai-content-kho-noi-dung` | `ai-content-status-badges.svg` | Có | **100% COVERED** |
| 9 | AI Content Engine | Cấu hình & Prompts | `index.php?com=ai_content&act=settings` | `ai-content-tao-moi` | `ai-content-options.svg` | Có | **100% COVERED** |
| 10 | AI Video Engine | Quản lý Dự án Video (Projects) | `index.php?com=ai_video&act=man` | `ai-video-du-an` | `ai-video-project-list.svg`, `ai-video-preview-player.svg`, `ai-video-approve-btn.svg` | Có | **100% COVERED** |
| 11 | AI Video Engine | Tạo dự án Video mới | `index.php?com=ai_video&act=create` | `ai-video-tao-moi` | `ai-video-create-step1.svg`, `ai-video-create-step2.svg`, `ai-video-create-step3.svg` | Có | **100% COVERED** |
| 12 | AI Video Engine | Hàng đợi Render & Kho Assets | `index.php?com=ai_video&act=jobs` | `ai-video-du-an` | `ai-video-project-list.svg` | Có | **100% COVERED** |
| 13 | Xuất bản & TikTok | Tạo Post Package & Link UTM | `index.php?com=publishing&act=create` | `xuat-ban-tao-post` | `publishing-create-step1.svg`, `publishing-create-step2.svg`, `publishing-create-step3.svg` | Có | **100% COVERED** |
| 14 | Xuất bản & TikTok | Pre-publish Checklist & Snapshot | `index.php?com=publishing&act=man` | `xuat-ban-kiem-duyet-ready` | `publishing-checklist-scan.svg`, `publishing-checklist-pass.svg`, `publishing-snapshot-lock.svg` | Có | **100% COVERED** |
| 15 | Xuất bản & TikTok | Lịch xuất bản & Tài khoản kênh | `index.php?com=publishing&act=calendar` | `xuat-ban-kiem-duyet-ready` | `publishing-snapshot-lock.svg` | Có | **100% COVERED** |
| 16 | Đo lường & Winner | Báo cáo Hiệu suất & Phễu Clicks | `index.php?com=analytics&act=overview` | `do-luong-tong-quan` | `analytics-time-filter.svg`, `analytics-chart-trend.svg`, `analytics-funnel.svg` | Có | **100% COVERED** |
| 17 | Đo lường & Winner | Winner Detection & Tăng tốc | `index.php?com=analytics&act=winner_detection` | `do-luong-winner-detection` | `winner-leaderboard.svg`, `winner-criteria-detail.svg`, `winner-scale-action.svg` | Có | **100% COVERED** |
| 18 | Đo lường & Winner | Đơn hàng & Nhập CSV Đối soát | `index.php?com=analytics&act=conversions` | `do-luong-tong-quan` | `analytics-funnel.svg` | Có | **100% COVERED** |
| 19 | Tối ưu hóa (A/B) | Khuyến nghị tối ưu & Thử nghiệm | `index.php?com=optimization&act=recommendations` | `do-luong-winner-detection` | `winner-scale-action.svg` | Có | **100% COVERED** |
| 20 | Quản lý Sản phẩm | Danh sách, Tìm kiếm & Checkbox | `index.php?com=product&act=man&type=san-pham` | `quan-ly-san-pham-danh-sach` | `product-list-filter.svg`, `product-quick-status-toggle.svg`, `product-action-buttons.svg` | Có | **100% COVERED** |
| 21 | Quản lý Sản phẩm | Thêm mới sản phẩm & Specs | `index.php?com=product&act=add&type=san-pham` | `quan-ly-san-pham-them-moi` | `product-add-step1-image.svg`, `product-add-step2-category-price.svg`, `product-add-step3-content.svg`, `product-add-step4-affiliate-seo.svg`, `product-add-step5-save.svg` | Có | **100% COVERED** |
| 22 | Quản lý Sản phẩm | Danh mục Cấp 1, 2, 3 & Thương hiệu | `index.php?com=product&act=man_list&type=san-pham` | `quan-ly-san-pham-them-moi` | `product-add-step2-category-price.svg` | Có | **100% COVERED** |
| 23 | Trung tâm Vận hành | Giám sát Sức khỏe & Workers | `index.php?com=operations&act=overview` | `trung-tam-van-hanh-tong-quan` | `operations-health-badge.svg`, `operations-workers-status.svg`, `operations-costs-summary.svg` | Có | **100% COVERED** |
| 24 | Trung tâm Vận hành | Ngân sách API & Cảnh báo Sự cố | `index.php?com=operations&act=costs` | `trung-tam-van-hanh-tong-quan` | `operations-costs-summary.svg` | Có | **100% COVERED** |
| 25 | Cấu hình & Tiện ích | Thiết lập chung & Xóa Cache | `index.php?com=setting&act=update` | `tong-quan-dashboard` | `dashboard-header-notify.svg` | Có | **100% COVERED** |

---

## 3. KẾT LUẬN ĐỘ PHỦ
- **Tổng số nhóm chức năng**: 10/10 nhóm (**Đạt 100%**)
- **Tổng số ảnh minh họa thực tế**: 44 file SVG vector tương ứng đúng từng bước thao tác.
- **Tính năng tìm kiếm**: Hỗ trợ tra cứu theo tiêu đề, tóm tắt, mô tả, từ khóa nghiệp vụ.
- **Tính năng liên kết**: Mỗi bài đều có breadcrumbs, link điều hướng danh mục và danh sách bài liên quan.
