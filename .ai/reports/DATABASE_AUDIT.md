# BÁO CÁO KIỂM TOÁN CƠ SỞ DỮ LIỆU (DATABASE AUDIT REPORT)

* **Tệp dữ liệu phân tích**: `dem22y2024_master.sql` (1.65 MB)
* **Tổng số bảng dữ liệu**: 48 bảng
* **Charset / Collation**: `utf8mb4` / `utf8mb4_unicode_ci`

---

## 1. CÁC BẢNG DỮ LIỆU CỐT LÕI (CORE TABLES AUDIT)

| Bảng | Mục đích | Số lượng bản ghi mẫu | Ghi chú & Đánh giá khả năng tái sử dụng cho FITNADO |
| :--- | :--- | :--- | :--- |
| `table_product` | Sản phẩm chính & Thư viện | ~15 bản ghi | Lưu trữ đầy đủ: tên, slug, giá gốc, giá KM, giảm giá, mô tả, nội dung chi tiết, trạng thái `hienthi,noibat`, type `san-pham`. |
| `table_product_list` | Danh mục sản phẩm cấp 1 | 2 bản ghi | Dùng cho 8 danh mục chính trong prototype (Tập tạ, Dây kháng lực, Shaker, Túi gym...). |
| `table_product_cat` | Danh mục sản phẩm cấp 2 | 2 bản ghi | Phân loại chi tiết theo nhu cầu tập. |
| `table_product_brand` | Thương hiệu sản phẩm | 7 bản ghi | Quản lý các hãng đồ tập gym (Nike, Adidas, Gymshark, Rogue...). |
| `table_news` | Bài viết & Tin tức | ~18 bản ghi | Phân loại theo `type`: `tin-tuc` (Kiến thức/Review), `chinh-sach`, `hinh-thuc-thanh-toan`. |
| `table_gallery` | Album ảnh & Video đính kèm | ~26 bản ghi | Lưu ảnh chi tiết và video link đính kèm cho sản phẩm hoặc bài viết (`id_parent`). |
| `table_photo` | Slider, Banner, Video độc lập | ~25 bản ghi | Quản lý slideshow trang chủ, đối tác, icon mạng xã hội và danh sách video YouTube/MP4 ngắn. |
| `table_static` | Trang tĩnh | 5 bản ghi | Lưu trữ: `gioi-thieu` (Về chúng tôi, đính kèm video), `slogan`, `lienhe`, `footer`, `copyright`. |
| `table_comment` | Bình luận & Đánh giá | ~22 bản ghi | Lưu đánh giá sao (1-5 sao), tiêu đề, nội dung nhận xét, phản hồi của Admin. |
| `table_comment_photo` | Ảnh đính kèm đánh giá | ~12 bản ghi | Lưu ảnh mở hộp/chụp thực tế từ người dùng đánh giá. |
| `table_comment_video` | Video đính kèm đánh giá | 4 bản ghi | Lưu video test sản phẩm đính kèm bình luận. |
| `table_setting` | Cấu hình website | 1 bản ghi | Chứa thông tin tên website, email, hotline, zalo, fanpage, tọa độ bản đồ. |
| `table_seopage` | SEO cho các trang tĩnh | ~7 bản ghi | Quản lý meta title, keywords, description cho trang chủ, sản phẩm, tin tức, liên hệ... |

---

## 2. KẾT LUẬN KIỂM TOÁN DATABASE

1. **Khả năng đáp ứng**: Cấu trúc database hiện tại hoàn toàn đáp ứng được 100% các khối hiển thị trong prototype của FITNADO (Sản phẩm nổi bật, Danh mục đồ tập, Video 30s review, Bài viết kiến thức, Đánh giá thực tế).
2. **Không cần can thiệp database trong Task 00**: Không thêm, sửa hoặc xóa bất kỳ bảng hay cột nào trong database.
