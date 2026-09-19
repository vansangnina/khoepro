# BẢN ĐỒ CƠ SỞ DỮ LIỆU FITNADO (DATABASE SCHEMA & MAP)

> [!NOTE]
> Thông tin dưới đây được trích xuất từ database dump thực tế: `dem22y2024_master.sql`.
> Database engine: `MyISAM` / `InnoDB`, Collation: `utf8mb4_unicode_ci`, Prefix mặc định: `table_` (trong query viết là `#_`).

---

## 1. QUY ƯỚC CHUNG (GENERAL CONVENTIONS)

1. **Quy ước trạng thái (`status`)**:
   * Lưu dưới dạng chuỗi phân tách dấu phẩy (CSV), ví dụ: `'hienthi,noibat'` hoặc `'hienthi'`.
   * Kiểm tra trong SQL: `find_in_set('hienthi', status)` hoặc `find_in_set('noibat', status)`.
2. **Quy ước đa ngôn ngữ**:
   * Tên: `namevi`, `nameen`
   * Mô tả: `descvi`, `descen`
   * Nội dung chi tiết: `contentvi`, `contenten`
   * Slug đường dẫn: `slugvi`, `slugen`
3. **Quy ước phân loại (`type`)**:
   * Xác định dữ liệu thuộc nhóm nghiệp vụ nào (ví dụ: `san-pham`, `thu-vien-anh`, `tin-tuc`, `chinh-sach`, `slide`, `video`, `gioi-thieu`...).
4. **Quy ước thứ tự & thời gian**:
   * `numb` (int): Thứ tự sắp xếp tăng dần (thường `order by numb, id desc`).
   * `date_created`, `date_updated`: Lưu dưới dạng Unix timestamp (int 11).

---

## 2. BẢNG DỮ LIỆU SẢN PHẨM (PRODUCT MODULE)

### `table_product` (Sản phẩm chính)
* **Mục đích**: Lưu thông tin sản phẩm, vật dụng gym, dụng cụ tập luyện hoặc thư viện ảnh.
* **Các cột quan trọng**:
  * `id` (int unsigned, PK, Auto Increment)
  * `id_list` (int): Danh mục cấp 1 (`table_product_list`)
  * `id_cat` (int): Danh mục cấp 2 (`table_product_cat`)
  * `id_item` (int): Danh mục cấp 3 (`table_product_item`)
  * `id_sub` (int): Danh mục cấp 4 (`table_product_sub`)
  * `id_brand` (int): Thương hiệu / Hãng (`table_product_brand`)
  * `code` (varchar 30): Mã sản phẩm (SKU)
  * `namevi`, `nameen` (varchar 255): Tên sản phẩm
  * `slugvi`, `slugen` (varchar 255): Slug URL
  * `photo` (varchar 255): Tên file ảnh đại diện chính (nằm trong `upload/product/`)
  * `options` (mediumtext): JSON metadata kích thước/dung lượng ảnh
  * `regular_price` (double): Giá gốc / Giá niêm yết
  * `sale_price` (double): Giá khuyến mãi / Giá bán thực tế
  * `discount` (double): Tỷ lệ % giảm giá
  * `descvi`, `descen` (mediumtext): Mô tả ngắn
  * `contentvi`, `contenten` (mediumtext): Nội dung đánh giá/hướng dẫn chi tiết
  * `review_score` (decimal 3,1): Điểm đánh giá Fitnado Score (thang điểm 10.0)
  * `review_count` (int): Số lượng đánh giá / nhận xét
  * `expert_pros` (text): Danh sách ưu điểm (Pros)
  * `expert_cons` (text): Danh sách nhược điểm (Cons)
  * `verdict` (text): Đánh giá tổng quan từ chuyên gia Fitnado
  * `specs` (text / json): Thông số kỹ thuật chi tiết
  * `best_for` (varchar 255): Đối tượng phù hợp nhất (Suitable for)
  * `affiliate_note` (varchar 255): Ghi chú tiếp thị liên kết & cập nhật giá
  * `status` (varchar 255): Trạng thái (`hienthi`, `noibat`...)
  * `type` (varchar 30): Loại (`san-pham`, `thu-vien-anh`...)
  * `view` (int): Lượt xem
  * `icon` (varchar 255): File icon nếu có
  * `date_created`, `date_updated` (int)

### `table_product_affiliate` (Ưu đãi tiếp thị liên kết đa sàn)
* **Mục đích**: Quản lý đa nền tảng affiliate (Shopee, Lazada, TikTok Shop, Tiki, Brand Store...) cho từng sản phẩm.
* **Các cột quan trọng**:
  * `id` (int unsigned, PK, Auto Increment)
  * `id_product` (int unsigned, FK to `table_product.id`): ID sản phẩm cha
  * `platform` (varchar 50): Nền tảng (`shopee`, `lazada`, `tiktok_shop`, `tiki`, `brand`, `other`)
  * `seller_name` (varchar 255): Tên gian hàng (Ví dụ: Shopee Mall, LazMall, Website chính hãng)
  * `original_price` (decimal 15,2): Giá niêm yết gốc trên sàn
  * `price` (double): Giá bán ưu đãi thực tế
  * `affiliate_url` (mediumtext): Đường dẫn tiếp thị liên kết (Affiliate Target URL)
  * `original_url` (mediumtext): Link gốc sản phẩm
  * `coupon_code` (varchar 100): Mã giảm giá độc quyền (Ví dụ: FITNADO10K)
  * `is_best_deal` (tinyint 1): Cờ ưu đãi tốt nhất (1: Best Deal, hiển thị nút chính)
  * `priority` (int): Thứ tự ưu tiên hiển thị
  * `status` (varchar 50): Trạng thái (`hienthi`, `tamngung`)
  * `date_created`, `date_updated` (int)

### `table_affiliate_click` (Nhật ký theo dõi chuyển hướng Click Tracking)
* **Mục đích**: Ghi nhận toàn bộ lượt click chuyển hướng sang các sàn thương mại để thống kê tỷ lệ chuyển đổi và doanh thu.
* **Các cột quan trọng**:
  * `id` (bigint unsigned, PK, Auto Increment)
  * `id_product` (int unsigned): ID sản phẩm được click
  * `id_affiliate` (int unsigned): ID ưu đãi / nơi bán được click
  * `platform` (varchar 50): Sàn thương mại tương ứng
  * `source_page` (varchar 50): Vị trí click (`product_detail_hero`, `product_detail_list`, `comparison`, `mobile_sticky_bar`...)
  * `device_type` (varchar 20): Thiết bị người dùng (`desktop`, `mobile`, `tablet`)
  * `ip_hash` (varchar 64): Mã băm SHA-256 ẩn danh địa chỉ IP
  * `user_agent` (varchar 255): Thông tin trình duyệt
  * `referer` (varchar 500): URL trang nguồn trước khi click
  * `date_created` (int): Unix timestamp thời điểm click

### `table_product_list`, `table_product_cat`, `table_product_item`, `table_product_sub`
* **Mục đích**: Cây danh mục 4 cấp cho sản phẩm.
* **Cột chính**: `id`, `namevi`, `nameen`, `slugvi`, `slugen`, `photo`, `descvi`, `descen`, `numb`, `status`, `type`.

### `table_product_brand` (Thương hiệu / Hãng)
* **Mục đích**: Danh mục thương hiệu đồ tập gym (Nike, Adidas, Rogue, Gymshark, MyProtein...).
* **Cột chính**: `id`, `namevi`, `nameen`, `slugvi`, `slugen`, `photo`, `numb`, `status`, `type`.

---

## 3. BẢNG DỮ LIỆU BÀI VIẾT & TIN TỨC (NEWS MODULE)

### `table_news` (Bài viết, Tin tức, Hướng dẫn, Chính sách)
* **Mục đích**: Lưu trữ bài viết review, kiến thức gym, cẩm nang tập luyện, chính sách.
* **Các cột quan trọng**:
  * `id`, `id_list`, `id_cat`, `id_item`, `id_sub`
  * `namevi`, `nameen`, `slugvi`, `slugen`
  * `photo`, `options`
  * `descvi`, `descen`: Đoạn trích dẫn / tóm tắt
  * `contentvi`, `contenten`: Nội dung chi tiết chuẩn HTML (CKEditor)
  * `status` (`hienthi`, `noibat`...)
  * `type` (`tin-tuc`, `tuyen-dung`, `chinh-sach`, `hinh-thuc-thanh-toan`)
  * `view`, `numb`, `date_created`, `date_updated`

### `table_news_list`, `table_news_cat`, `table_news_item`, `table_news_sub`
* **Mục đích**: Cây phân cấp danh mục bài viết kiến thức và hướng dẫn.

---

## 4. BẢNG DỮ LIỆU THƯ VIỆN ĐA PHƯƠNG TIỆN (GALLERY)

### `table_gallery` (Thư viện ảnh/video phụ đính kèm)
* **Mục đích**: Đính kèm nhiều hình ảnh hoặc video cho sản phẩm, danh mục, bài viết.
* **Các cột quan trọng**:
  * `id` (int unsigned, PK)
  * `id_parent` (int): ID của bản ghi cha (ví dụ `table_product.id` hoặc `table_news.id`)
  * `photo` (varchar 255): File ảnh phụ
  * `namevi`, `nameen` (varchar 255): Tiêu đề ảnh/video
  * `id_color` (int): Liên kết với màu sắc sản phẩm (`table_color`)
  * `file_attach` (varchar 255): File đính kèm (PDF, tài liệu)
  * `link_video` (mediumtext): Link video YouTube / external video
  * `com` (varchar 255): Tên module cha (`product`, `news`, `static`...)
  * `kind` (varchar 30): Phân loại (`man`, `man_list`...)
  * `val` (varchar 30): Giá trị type (`san-pham`, `tin-tuc`...)
  * `type` (varchar 30): Phân loại gallery
  * `numb`, `status`, `date_created`, `date_updated`

---

## 5. BẢNG DỮ LIỆU HÌNH ẢNH & MEDIA ĐỘC LẬP (PHOTO MODULE)

### `table_photo` (Slider, Banner, Logo, Social, Video độc lập)
* **Mục đích**: Quản lý hình ảnh tĩnh hoặc danh sách ảnh/video độc lập không gắn trực tiếp vào một sản phẩm cụ thể.
* **Các cột quan trọng**:
  * `id` (int unsigned, PK)
  * `photo` (varchar 255): Đường dẫn file ảnh
  * `namevi`, `nameen`: Tên/tiêu đề
  * `descvi`, `descen`: Mô tả
  * `link` (mediumtext): Đường dẫn click khi bấm vào ảnh (dùng cho banner, đối tác, social)
  * `link_video` (mediumtext): URL video YouTube / MP4
  * `type` (varchar 30): `logo`, `favicon`, `banner`, `slide`, `social`, `video`, `doitac`, `popup`, `watermark`...
  * `act` (varchar 30): `photo_static` (ảnh đơn) hoặc `photo_multi` (nhiều ảnh)
  * `options` (mediumtext): JSON cấu hình watermark, background
  * `numb`, `status`, `date_created`, `date_updated`

---

## 6. BẢNG TRANG TĨNH (STATIC MODULE)

### `table_static`
* **Mục đích**: Lưu các bài viết tĩnh độc nhất (Giới thiệu, Slogan, Copyright, Liên hệ, Footer).
* **Các cột quan trọng**:
  * `id`, `type` (`gioi-thieu`, `slogan`, `copyright`, `lienhe`, `footer`)
  * `namevi`, `nameen`
  * `photo`, `photo1`, `options`
  * `descvi`, `descen`, `contentvi`, `contenten`
  * `video` (varchar 255): File video MP4 giới thiệu
  * `file_attach` (varchar 255)
  * `status`, `date_created`, `date_updated`

---

## 7. BẢNG BÌNH LUẬN & ĐÁNH GIÁ (COMMENTS & REVIEWS)

### `table_comment`
* **Mục đích**: Lưu bình luận, đánh giá sao của người dùng về sản phẩm hoặc bài viết.
* **Các cột quan trọng**:
  * `id`, `id_parent` (0 nếu là comment gốc, > 0 nếu là phản hồi reply)
  * `id_variant` (int): ID sản phẩm hoặc bài viết được đánh giá
  * `star` (int): Số sao đánh giá (1 - 5 sao)
  * `title` (varchar 255): Tiêu đề đánh giá
  * `content` (text): Nội dung nhận xét chi tiết
  * `fullname`, `phone`, `email`
  * `poster` (varchar 255): 'admin' hoặc để trống
  * `status` (`hienthi`...)
  * `type` (`san-pham`...)
  * `date_posted` (int)

### `table_comment_photo` & `table_comment_video`
* **Mục đích**: Lưu ảnh thực tế và video mở hộp / test sản phẩm của người đánh giá.
* `table_comment_photo`: `id`, `id_parent` (trỏ về `table_comment.id`), `photo`
* `table_comment_video`: `id`, `id_parent`, `photo` (poster), `video` (file video đính kèm)

---

## 8. BẢNG CẤU HÌNH & SEO

### `table_setting`
* **Mục đích**: Cấu hình thông tin chung toàn website (tên công ty, địa chỉ, hotline, email, zalo, fanpage, tọa độ bản đồ, options JSON).

### `table_seo` & `table_seopage`
* **Mục đích**: Quản lý meta title, keywords, description, schema cấu trúc cho từng đối tượng hoặc từng trang tĩnh.

### `table_tags` & `table_product_tags` / `table_news_tags`
* **Mục đích**: Gắn thẻ từ khóa tìm kiếm cho sản phẩm và bài viết.

---

## 9. BẢNG NGHIÊN CỨU SẢN PHẨM & CANDIDATE PIPELINE (PHASE 03)

### `table_product_research`
* **Mục đích**: Lưu trữ toàn bộ dữ liệu ứng viên nghiên cứu sản phẩm Gym/Fitness, điểm số tiềm năng 5 chiều, lịch sử duyệt và liên kết sản phẩm.
* **Cấu trúc chi tiết**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `name`: Tên sản phẩm ứng viên VARCHAR(255) NOT NULL
  * `normalized_name`: Tên chuẩn hóa (bỏ dấu, lowercase) VARCHAR(255)
  * `category_hint`: Gợi ý danh mục VARCHAR(255)
  * `brand_hint`: Gợi ý thương hiệu VARCHAR(255)
  * `platform`: Nền tảng phát hiện VARCHAR(50) (tiktok, shopee, lazada, brand, google, manual, other)
  * `source_url`: URL liên kết nguồn TEXT NOT NULL
  * `normalized_url`: URL chuẩn hóa bỏ query tracking VARCHAR(500)
  * `external_product_id`: ID sản phẩm trên sàn/nền tảng ngoài VARCHAR(100)
  * `image_url`: Link ảnh đại diện ứng viên TEXT
  * `price`, `original_price`: Giá bán và giá gốc DOUBLE
  * `currency`: Đơn vị tiền tệ VARCHAR(10) DEFAULT 'VND'
  * `sales_count`: Lượt bán INT(11)
  * `rating`: Đánh giá sao DOUBLE (0.0 - 5.0)
  * `review_count`: Số lượng review INT(11)
  * `commission_rate`, `commission_value`: Tỷ lệ (%) và giá trị hoa hồng ước tính (VND)
  * `estimated_gmv`: Doanh số ước tính DOUBLE
  * `creator_count`, `video_count`, `top_video_views`: Chỉ số video & Creator
  * `problem_solved`: Pain point và giải pháp sản phẩm TEXT
  * `target_audience`: Đối tượng khách hàng TEXT
  * `research_notes`: Ghi chú kỹ thuật TEXT
  * `primary_keyword`: Từ khóa chính SEO VARCHAR(255)
  * `demand_score`, `content_score`, `commission_score`, `competition_score`, `seo_score`: Điểm thành phần 5 chiều (0 - 100)
  * `total_score`: Tổng điểm tiềm năng có trọng số DOUBLE
  * `score_breakdown`: JSON phân rã điểm & lý do giải thích
  * `status`: Trạng thái (DISCOVERED, RESEARCHED, APPROVED, REJECTED, PRODUCT_CREATED)
  * `reject_reason`: Lý do từ chối VARCHAR(255)
  * `id_product`: Khóa ngoại INT(11) liên kết với `table_product.id` sau khi duyệt
  * `discovery_source`: Nguồn phát hiện (ai_agent, tiktok_crawler, shopee_crawler, lazada_crawler, csv_import, manual) VARCHAR(100)
  * `ai_analysis`: Phân tích chuyên sâu từ AI (JSON: content_angles, visual_demo_potential, comparison_potential, risk_notes, confidence)
  * `ai_confidence`: Độ tin cậy của AI (0 - 100)
  * `first_seen_at`, `last_seen_at`: Thời điểm phát hiện lần đầu và lần quét gần nhất
  * `raw_data`: JSON dữ liệu thô từ nguồn
  * `history`: JSON lịch sử thay đổi trạng thái và audit
  * `date_created`, `date_updated`: Timestamp tạo và cập nhật INT(11)
* **Indexes**: `idx_status`, `idx_platform`, `idx_id_product`, `idx_total_score`, `idx_external_id`, `idx_normalized_url`, `idx_normalized_name`, `idx_date_created`.

---

## 10. BẢNG AUTOMATED RESEARCH & AI RESEARCH AGENT (PHASE 04)

### `table_product_research_seed` (Hạt giống nghiên cứu & Từ khóa khám phá)
* **Mục đích**: Lưu trữ danh mục hạt giống từ khóa, danh mục, URL shop/sàn để hệ thống tự động quét và phân tích định kỳ.
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `title`: Tiêu đề hạt giống VARCHAR(255) NOT NULL
  * `keyword`: Từ khóa hoặc chuỗi tìm kiếm VARCHAR(255) NOT NULL
  * `seed_type`: Phân loại hạt giống (`keyword`, `category`, `competitor_url`, `trend`, `hashtag`)
  * `platform`: Nền tảng đích (`tiktok`, `shopee`, `lazada`, `all`)
  * `priority`: Mức độ ưu tiên INT(11) DEFAULT 0
  * `depth`: Độ sâu phân tích AI (`QUICK`, `STANDARD`, `DEEP`)
  * `frequency`: Tần suất chạy (`daily`, `weekly`, `monthly`, `manual`)
  * `max_results`: Số lượng ứng viên tối đa mỗi lần quét INT(11) DEFAULT 10
  * `last_run_at`: Unix timestamp lần quét gần nhất INT(11)
  * `status`: Trạng thái (`active`, `paused`, `archived`)
  * `date_created`, `date_updated`: Unix timestamp

### `table_product_research_job` (Hàng đợi tác vụ nghiên cứu nền)
* **Mục đích**: Quản lý hàng đợi tác vụ Background Job Queue, đảm bảo xử lý bất đồng bộ không nghẽn/timeout HTTP request.
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `id_seed`: Khóa ngoại liên kết `table_product_research_seed.id` (hoặc NULL nếu job thủ công)
  * `provider`: Tên provider thực thi (`ai_agent`, `tiktok`, `shopee`, `lazada`, `csv`, `manual`)
  * `depth`: Mức độ phân tích (`QUICK`, `STANDARD`, `DEEP`)
  * `status`: Trạng thái xử lý (`PENDING`, `RUNNING`, `SUCCESS`, `FAILED`, `CANCELLED`)
  * `attempts`: Số lần thử lại INT(11) DEFAULT 0
  * `max_attempts`: Giới hạn retry INT(11) DEFAULT 3
  * `candidates_found`, `candidates_created`, `duplicates_count`: Thống kê kết quả
  * `error_message`: Nội dung lỗi nếu thất bại TEXT
  * `payload`: Dữ liệu đầu vào JSON
  * `started_at`, `completed_at`: Timestamp bắt đầu và hoàn tất
  * `duration`: Thời gian xử lý tính bằng giây DOUBLE
  * `date_created`, `date_updated`: Unix timestamp

### `table_product_research_evidence` (Nhật ký xuất xứ & Bằng chứng dữ liệu)
* **Mục đích**: Lưu vết minh bạch nguồn gốc từng trường dữ liệu (Fact vs AI Analysis), phân biệt rõ dữ liệu thực tế sàn vs suy luận AI.
* **Cấu trúc**:
  * `id`: Khóa chính BIGINT(20) UNSIGNED AUTO_INCREMENT
  * `id_research`: Khóa ngoại liên kết `table_product_research.id`
  * `id_job`: Khóa ngoại liên kết `table_product_research_job.id`
  * `provider`: Nguồn thu thập (tiktok, shopee, gemini, openai, csv...)
  * `source_url`: URL gốc chứa bằng chứng
  * `evidence_type`: Phân loại (`FACT`, `AI_ANALYSIS`, `RAW_PAYLOAD`, `USER_OVERRIDE`)
  * `field_name`: Tên trường dữ liệu (sales_count, price, rating, content_angles...)
  * `field_value`: Giá trị bằng chứng (chuỗi hoặc JSON)
  * `captured_at`: Unix timestamp thu thập

### `table_product_research_snapshot` (Lịch sử biến động tín hiệu thị trường)
* **Mục đích**: Ghi nhận biến động theo thời gian (giá bán, lượt bán, rating, review count, creator count, video count) mỗi khi quét lại sản phẩm.
* **Cấu trúc**:
  * `id`: Khóa chính BIGINT(20) UNSIGNED AUTO_INCREMENT
  * `id_research`: Khóa ngoại liên kết `table_product_research.id`
  * `id_job`: Khóa ngoại liên kết `table_product_research_job.id`
  * `price`, `sales_count`, `rating`, `review_count`, `commission_rate`, `creator_count`, `video_count`: Các chỉ số thị trường tại thời điểm snapshot
  * `date_created`: Unix timestamp thời điểm ghi nhận snapshot

---

## 11. BẢNG AI CONTENT ENGINE & CONTENT PACKAGE (PHASE 05)

### `table_ai_content` (Gói nội dung AI theo sản phẩm)
* **Mục đích**: Lưu trữ các phiên bản nội dung AI sinh ra cho sản phẩm (SEO, TikTok Script, Shot Plan, Hooks, Review Draft, FAQ, Tone Guide).
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `id_product`: Khóa ngoại liên kết `table_product.id` (bắt buộc)
  * `id_research`: Khóa ngoại liên kết `table_product_research.id` (nếu có)
  * `content_type`: Loại gói (`ALL`, `TIKTOK_SCRIPT`, `SEO_PACK`, `REVIEW_DRAFT`, `PRODUCT_ANALYSIS`, `SHORT_COPY`)
  * `version`: Phiên bản INT(11) DEFAULT 1
  * `is_current`: Cờ phiên bản hiện tại TINYINT(1) DEFAULT 1
  * `title`: Tiêu đề nội dung / Hook chính VARCHAR(255)
  * `product_analysis`: Phân tích chuyên sâu JSON (USP, đối tượng, pain point, góc khai thác)
  * `tiktok_hooks`: Mảng 7 loại Hook chiến lược JSON (Curiosity, Problem-Agitate, Transformation, Cost-Comparison, Direct-Review, Myth-Busting, FOMO)
  * `tiktok_scripts`: Kịch bản TikTok JSON (Hook, Body, Visual cue, Audio cue, CTA)
  * `shot_plan`: Kế hoạch cảnh quay theo phân đoạn JSON (Scene #, duration, visual instruction, voiceover, on-screen text, asset requirement - Cầu nối Phase 06)
  * `seo_metadata`: Gói SEO JSON (Meta title, meta desc, primary/secondary keywords, schema FAQ & Review, alt suggestions)
  * `review_draft`: Bài đánh giá/review chi tiết (Mô tả, Pros, Cons, Verdict, Best For, Specs, Hướng dẫn sử dụng)
  * `faq_list`: Danh sách câu hỏi thường gặp JSON
  * `tone_guide`: Chỉ dẫn giọng điệu JSON (Giọng tập trung chuyên gia, thể thao, năng động, trung thực)
  * `evidence_used`: Danh sách ID bằng chứng fact đã trích xuất sử dụng JSON
  * `source_hash`: SHA-256 hash của dữ liệu đầu vào sản phẩm & research để phát hiện nội dung lỗi thời
  * `prompt_version`: Mã phiên bản prompt template (ví dụ: `tiktok-script-v1`, `seo-pack-v1`)
  * `ai_model`: Model AI đã sinh nội dung (gemini-1.5-pro, gpt-4o, mock-content-engine...)
  * `quality_score`: Điểm chất lượng nội dung DOUBLE (0 - 100)
  * `quality_checks`: Kết quả kiểm tra Quality Gate JSON (Pass/Fail các tiêu chí factual, cấm claim giả mạo)
  * `status`: Trạng thái (`DRAFT`, `REVIEW_REQUIRED`, `APPROVED`, `REJECTED`, `APPLIED_TO_PRODUCT`, `ARCHIVED`)
  * `review_notes`: Ghi chú từ chối hoặc góp ý của Admin TEXT
  * `reviewed_by`: Tên hoặc ID admin kiểm duyệt VARCHAR(100)
  * `reviewed_at`: Unix timestamp thời điểm kiểm duyệt INT(11)
  * `applied_at`: Unix timestamp thời điểm áp dụng vào sản phẩm INT(11)
  * `date_created`, `date_updated`: Unix timestamp

### `table_ai_content_job` (Hàng đợi tác vụ sinh nội dung AI)
* **Mục đích**: Quản lý các job bất đồng bộ sinh nội dung AI đơn lẻ hoặc hàng loạt cho sản phẩm.
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `id_product`: Khóa ngoại liên kết `table_product.id`
  * `content_type`: Loại nội dung yêu cầu (`ALL`, `TIKTOK_SCRIPT`, `SEO_PACK`...)
  * `prompt_version`: Phiên bản prompt cấu hình VARCHAR(50)
  * `ai_model`: Tên model AI thực thi VARCHAR(50)
  * `status`: Trạng thái (`PENDING`, `RUNNING`, `SUCCESS`, `FAILED`, `CANCELLED`)
  * `attempts`: Số lần thử lại INT(11) DEFAULT 0
  * `max_attempts`: Giới hạn retry INT(11) DEFAULT 3
  * `id_content`: ID kết quả sinh ra liên kết `table_ai_content.id`
  * `error_message`: Nội dung lỗi nếu có TEXT
  * `started_at`, `completed_at`: Timestamp bắt đầu và kết thúc
  * `duration`: Thời gian xử lý (giây) DOUBLE
  * `date_created`, `date_updated`: Unix timestamp

### `table_product_content_backup` (Lưu trữ sao lưu nội dung sản phẩm khi áp dụng AI)
* **Mục đích**: Lưu lại trạng thái nội dung cũ của `table_product` trước khi áp dụng AI Content để đảm bảo tính hoàn nguyên (Reversible apply/rollback).
* **Cấu trúc**:
  * `id`: Khóa chính BIGINT(20) UNSIGNED AUTO_INCREMENT
  * `id_product`: ID sản phẩm `table_product.id`
  * `id_content`: ID gói nội dung AI `table_ai_content.id` đã áp dụng
  * `field_name`: Tên trường được ghi đè (descvi, contentvi, expert_pros, expert_cons, verdict, best_for, specs...)
  * `old_value`: Giá trị cũ trước khi ghi đè MEDIUMTEXT
  * `new_value`: Giá trị mới vừa được áp dụng MEDIUMTEXT
  * `created_by`: Admin thực hiện áp dụng VARCHAR(100)
  * `date_created`: Unix timestamp thời điểm sao lưu

---

## 12. BẢNG AI VIDEO PRODUCTION ENGINE (PHASE 06)

### `table_ai_video` (Dự án Video & Thành phẩm AI - Phase 06 & Phase 06.2)
* **Mục đích**: Lưu trữ thông tin các dự án video, phân cảnh, file video thành phẩm, thumbnail, trạng thái, chế độ sản xuất (`mode`), báo cáo chi phí chi tiết, và điểm kiểm định chất lượng Media QC.
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `id_product`: Khóa ngoại liên kết `table_product.id` (bắt buộc)
  * `id_content`: Khóa ngoại liên kết `table_ai_content.id` (bắt buộc status = 'APPROVED')
  * `title`: Tiêu đề dự án video VARCHAR(255)
  * `video_type`: Loại video (`TIKTOK_9_16`, `YOUTUBE_SHORTS`, `PRODUCT_SHOWCASE`)
  * `aspect_ratio`: Tỷ lệ khung hình (`9:16`, `1:1`, `16:9`) DEFAULT '9:16'
  * `target_duration`: Thời lượng mục tiêu (giây, vd: 15, 30, 45, 60)
  * `voice_id`: Giọng đọc TTS (`vi-VN-Standard-A`...)
  * `template_id`: Mẫu visual presentation (`PROBLEM_SOLUTION`, `PRODUCT_REVIEW`, `COMPARISON`...)
  * `mode`: Chế độ sản xuất VARCHAR(20) DEFAULT 'ECONOMY' (`ECONOMY` 0 VND default, `HYBRID` max 1-2 AI scenes, `PREMIUM`)
  * `scenes_data`: Mảng phân cảnh chuẩn hóa JSON (Scene 1..N kèm `purpose`, `render_method`, `motion_effect`, `voiceover`, `on_screen_text`, `asset_resolved`)
  * `script_hash`: Mã băm SHA-256 kịch bản tại thời điểm cấu hình dự án
  * `version`: Phiên bản video INT(11) DEFAULT 1
  * `is_active`: TINYINT(1) DEFAULT 1 - Cờ phiên bản chính
  * `is_outdated`: TINYINT(1) DEFAULT 0 - Đánh dấu khi kịch bản gốc bị sửa đổi
  * `provider`: Nhà cung cấp render (`mock`, `beeknoee`, `creatify`, `arcads`, `heygen`, `manual`)
  * `provider_job_id`: ID tác vụ phía provider bên ngoài
  * `status`: Trạng thái (`DRAFT`, `WAITING_ASSET`, `READY`, `QUEUED`, `PROCESSING`, `RENDERED`, `VALIDATING`, `REVIEW_REQUIRED`, `APPROVED`, `REJECTED`, `FAILED`, `ARCHIVED`)
  * `reject_reason`: Lý do từ chối của Admin VARCHAR(255)
  * `video_file`: Đường dẫn file video cục bộ (`upload/video/...mp4`)
  * `thumbnail`: Đường dẫn thumbnail cục bộ (`upload/video/...jpg`)
  * `duration_actual`: Thời lượng thực tế (giây) DOUBLE
  * `width`, `height`: Kích thước pixel (1080x1920)
  * `file_size`: Dung lượng file (bytes) BIGINT(20)
  * `cost_estimate`: Chi phí ước tính DOUBLE
  * `local_render_cost`: Chi phí render cục bộ DOUBLE DEFAULT 0 (0 VND)
  * `ai_video_seconds`: Tổng số giây AI Video clip đã sử dụng INT(11) DEFAULT 0
  * `ai_video_cost`: Chi phí AI Video clip phát sinh DOUBLE DEFAULT 0 (VND)
  * `tts_cost`: Chi phí TTS lồng tiếng DOUBLE DEFAULT 0 (VND)
  * `total_external_api_cost`: Tổng chi phí API đối tác bên ngoài DOUBLE DEFAULT 0 (VND)
  * `composer_log`: Nhật ký chi tiết tiến trình Video Composer MEDIUMTEXT NULL
  * `quality_report`: Kết quả kiểm định Media QC JSON
  * `reviewed_by`: Tên/ID Admin kiểm duyệt
  * `reviewed_at`: Unix timestamp thời điểm kiểm duyệt
  * `date_created`, `date_updated`: Unix timestamp

### `table_ai_video_job` (Hàng đợi tác vụ Render nền)
* **Mục đích**: Quản lý hàng đợi bất đồng bộ xử lý render video, polling trạng thái và retry khi gặp lỗi mạng.
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `id_video`: Khóa ngoại liên kết `table_ai_video.id`
  * `provider`: Tên provider thực thi
  * `provider_job_id`: ID tác vụ từ provider
  * `status`: Trạng thái (`PENDING`, `RUNNING`, `SUCCESS`, `FAILED`, `CANCELLED`)
  * `attempts`: Số lần thử lại INT(11) DEFAULT 0
  * `max_attempts`: Giới hạn retry INT(11) DEFAULT 3
  * `next_poll_at`: Unix timestamp thời điểm polling tiếp theo
  * `poll_count`: Số lần đã polling
  * `error_message`: Chi tiết lỗi nếu thất bại TEXT
  * `results_summary`: Tóm tắt kết quả
  * `started_at`, `completed_at`: Unix timestamp
  * `duration`: Thời gian render tính bằng giây DOUBLE
  * `date_created`, `date_updated`: Unix timestamp

### `table_ai_video_asset` (Kho Tài nguyên Dự án Video)
* **Mục đích**: Quản lý ánh xạ tài nguyên hình ảnh/video cho từng phân cảnh của dự án video.
* **Cấu trúc**:
  * `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
  * `id_video`: Khóa ngoại liên kết `table_ai_video.id`
  * `scene_number`: Thứ tự phân cảnh INT(11)
  * `asset_type`: Loại tài nguyên (`PRODUCT_PHOTO`, `PRODUCT_VIDEO`, `BROLL`, `LOGO`, `CTA_OVERLAY`, `VOICE_AUDIO`, `CAPTIONS`)
  * `source_type`: Nguồn gốc (`PRODUCT_DB`, `GALLERY_DB`, `MANUAL_UPLOAD`, `AI_GENERATED`)
  * `source_ref`: Đường dẫn file hoặc URL tham chiếu
  * `file_size`: Dung lượng bytes
  * `is_approved`: TINYINT(1) DEFAULT 1
  * `notes`: Ghi chú nguồn gốc
  * `date_created`: Unix timestamp



