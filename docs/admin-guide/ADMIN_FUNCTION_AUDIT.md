# AUDIT TOÀN BỘ CHỨC NĂNG HỆ THỐNG ADMIN KHOEPRO

## 1. TỔNG QUAN HỆ THỐNG
- **Hệ thống**: KhoePro Admin (Nina PHP CMS + AI Marketing Operations Suite)
- **Công nghệ**: PHP 7.4/8.x, MySQL (PDO), AdminLTE 3 UI, JavaScript/AJAX, Bootstrap 4, FontAwesome 5
- **Kiến trúc**: Module Dispatcher thông qua `index.php?com={module}&act={action}&type={type}`
- **Thời gian thực hiện audit**: 22/09/2026

---

## 2. DANH MỤC TOÀN BỘ CHỨC NĂNG ADMIN

### NHÓM 1: BẢNG ĐIỀU KHIỂN & TRUNG TÂM VẬN HÀNH (OPERATIONS)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 1.1 | Dashboard Tổng quan | Menu chính → Dashboard | `index.php` | Thống kê nhanh KPI truy cập, đơn hàng, bình luận, cảnh báo hệ thống |
| 1.2 | Tổng quan Vận hành | Trung tâm Vận hành → Tổng quan hệ thống | `index.php?com=operations&act=overview` | Dashboard vận hành thời gian thực: Health Status, Tác vụ, Chi phí |
| 1.3 | Pipeline & Hàng đợi | Trung tâm Vận hành → Pipeline & Hàng đợi | `index.php?com=operations&act=pipeline` | Theo dõi dòng chảy dữ liệu tự động từ Discovery → Content → Video → Post |
| 1.4 | Quản lý Tác vụ & Queues | Trung tâm Vận hành → Quản lý Tác vụ | `index.php?com=operations&act=jobs` | Quản lý trạng thái background workers, chạy lại job lỗi (Retry), xem log |
| 1.5 | Nhà cung cấp & API | Trung tâm Vận hành → Nhà cung cấp & API | `index.php?com=operations&act=providers` | Giám sát trạng thái kết nối Gemini AI, AccessTrade, Beeknoee, TikTok |
| 1.6 | Chi phí API & Ngân sách | Trung tâm Vận hành → Chi phí API & Ngân sách | `index.php?com=operations&act=costs` | Thống kê chi phí tiêu hao theo ngày/tháng, cảnh báo vượt ngưỡng ngân sách |
| 1.7 | Cảnh báo & Sự cố | Trung tâm Vận hành → Cảnh báo & Sự cố | `index.php?com=operations&act=alerts` | Xem danh sách cảnh báo (Warning/Critical), xác nhận xử lý sự cố (Acknowledge) |
| 1.8 | Nhật ký hệ thống | Trung tâm Vận hành → Nhật ký hệ thống | `index.php?com=operations&act=logs` | Tra cứu Audit Logs chi tiết về mọi thay đổi cấu hình, tác vụ |
| 1.9 | Cấu hình Vận hành | Trung tâm Vận hành → Cấu hình Vận hành | `index.php?com=operations&act=settings` | Bật/tắt chế độ Safe Mode, Maintenance Mode, cấu hình Telegram Alerts |

---

### NHÓM 2: NGHIÊN CỨU SẢN PHẨM & AI DISCOVERY (PHASE 03 - 04)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 2.1 | Ứng viên nghiên cứu | Nghiên cứu sản phẩm → Ứng viên nghiên cứu | `index.php?com=product_research&act=man` | Duyệt danh sách sản phẩm tiềm năng cào từ sàn/AccessTrade/TikTok |
| 2.2 | Thêm ứng viên thủ công | Ứng viên nghiên cứu → Nút "Thêm mới" | `index.php?com=product_research&act=add` | Nhập URL/sản phẩm thủ công để AI phân tích tiềm năng |
| 2.3 | Duyệt ứng viên & Tạo sản phẩm | Ứng viên nghiên cứu → Nút "Tạo sản phẩm" | `index.php?com=product_research&act=create_product` | Chuyển ứng viên đã nghiên cứu thành sản phẩm chính thức trong kho |
| 2.4 | Từ khóa & Seeds | Nghiên cứu sản phẩm → Từ khóa & Seeds | `index.php?com=product_research&act=seeds` | Quản lý danh sách từ khóa ngách Fitness để bot quét tự động |
| 2.5 | Hàng đợi Discovery Jobs | Nghiên cứu sản phẩm → Hàng đợi Jobs | `index.php?com=product_research&act=jobs` | Theo dõi tiến độ chạy cào dữ liệu và phân tích AI |
| 2.6 | Cấu hình AI & API | Nghiên cứu sản phẩm → Cấu hình AI & API | `index.php?com=product_research&act=provider_config` | Cấu hình API Key Gemini/OpenAI, AccessTrade Key, giới hạn request |
| 2.7 | Trọng số chấm điểm | Nghiên cứu sản phẩm → Trọng số chấm điểm | `index.php?com=product_research&act=weights` | Thiết lập công thức tính điểm nhu cầu (Demand), cạnh tranh, hoa hồng |

---

### NHÓM 3: AI CONTENT ENGINE (PHASE 05)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 3.1 | Kho nội dung (Library) | AI Content Engine → Kho nội dung | `index.php?com=ai_content&act=man` | Xem danh sách các gói nội dung AI tạo (Analysis, Hooks, Script, SEO) |
| 3.2 | Tạo nội dung AI mới | AI Content Engine → Tạo nội dung mới | `index.php?com=ai_content&act=generate` | Chọn sản phẩm, ngôn ngữ, góc độ để kích hoạt AI viết bài tự động |
| 3.3 | So sánh phiên bản (Diff) | Kho nội dung → Nút "So sánh phiên bản" | `index.php?com=ai_content&act=diff` | Đối chiếu văn bản giữa các version v1, v2, v3 trước khi duyệt |
| 3.4 | Duyệt & Áp dụng nội dung | Kho nội dung → Chi tiết bản ghi | `index.php?com=ai_content&act=view` | Phê duyệt (Approve) và ghi đè nội dung AI vào trang sản phẩm với backup |
| 3.5 | Hàng đợi Content Jobs | AI Content Engine → Hàng đợi Jobs | `index.php?com=ai_content&act=jobs` | Quản lý các lệnh sinh nội dung hàng loạt chạy nền |
| 3.6 | Cấu hình & Prompts | AI Content Engine → Cấu hình & Prompts | `index.php?com=ai_content&act=settings` | Tùy chỉnh Tone giọng, System Prompt tuân thủ Guardrail 21 nguyên tắc |

---

### NHÓM 4: AI VIDEO ENGINE (PHASE 06)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 4.1 | Danh sách Dự án Video | AI Video Engine → Dự án Video | `index.php?com=ai_video&act=man` | Xem và quản lý các video marketing đã render (Economy/Hybrid/Veo) |
| 4.2 | Tạo dự án Video mới | AI Video Engine → Tạo Video mới | `index.php?com=ai_video&act=create` | Thiết lập kịch bản phân cảnh, lồng tiếng Voiceover, chèn phụ đề tự động |
| 4.3 | Chi tiết & Duyệt Video | Dự án Video → Nút "Xem chi tiết" | `index.php?com=ai_video&act=view` | Xem trước (Preview) video, kiểm tra chất lượng, duyệt video đạt chuẩn |
| 4.4 | Hàng đợi Render | AI Video Engine → Hàng đợi Render | `index.php?com=ai_video&act=jobs` | Quản lý tiến trình ghép video bằng FFmpeg server và gọi API Veo |
| 4.5 | Kho tài nguyên (Assets) | AI Video Engine → Kho tài nguyên | `index.php?com=ai_video&act=assets` | Quản lý ảnh sản phẩm, nhạc nền miễn phí bản quyền, font chữ phụ đề |
| 4.6 | Cấu hình Video Providers | AI Video Engine → Cấu hình Providers | `index.php?com=ai_video&act=settings` | Thiết lập ngân sách video tối đa, chọn giọng đọc TTS, API Beeknoee |

---

### NHÓM 5: XUẤT BẢN & PHÂN PHỐI TIKTOK (PHASE 07)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 5.1 | Danh sách bài đăng (Posts) | Xuất bản & TikTok → Danh sách bài đăng | `index.php?com=publishing&act=man` | Quản lý các gói bài đăng mạng xã hội (TikTok, FB Reels, YT Shorts) |
| 5.2 | Tạo Post Package mới | Xuất bản & TikTok → Tạo Post Package | `index.php?com=publishing&act=create` | Tạo bài đăng từ Video đã duyệt, gắn Caption, Hashtag, Link UTM Affiliate |
| 5.3 | Kiểm duyệt Pre-publish | Chi tiết bài đăng → Nút "Kiểm tra điều kiện" | `index.php?com=publishing&act=view` | Chạy 8 tiêu chí kiểm định và Guardrail an toàn trước khi cho phép đăng |
| 5.4 | Hàng đợi & Lịch xuất bản | Xuất bản & TikTok → Hàng đợi / Lịch | `index.php?com=publishing&act=calendar` | Lên lịch hẹn giờ đăng bài tự động, kéo thả lịch trực quan |
| 5.5 | Tài khoản kênh mạng xã hội | Xuất bản & TikTok → Tài khoản kênh | `index.php?com=publishing&act=accounts` | Quản lý các kênh TikTok, Fanpage liên kết trong hệ sinh thái |
| 5.6 | Cấu hình Xuất bản | Xuất bản & TikTok → Cấu hình & TikTok API | `index.php?com=publishing&act=settings` | Quản lý Webhook, Token xuất bản an toàn |

---

### NHÓM 6: ĐO LƯỜNG, CHUYỂN ĐỔI & WINNER DETECTION (PHASE 08 - 09)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 6.1 | Tổng quan hiệu suất | Đo lường & Winner → Tổng quan hiệu suất | `index.php?com=analytics&act=overview` | Báo cáo Clicks, Chuyển đổi, Doanh thu, ROI theo kênh và thời gian |
| 6.2 | Báo cáo theo Sản phẩm | Đo lường & Winner → Hiệu quả sản phẩm | `index.php?com=analytics&act=products` | Xem sản phẩm nào mang lại nhiều click và hoa hồng nhất |
| 6.3 | Báo cáo theo Bài đăng | Đo lường & Winner → Hiệu quả bài đăng | `index.php?com=analytics&act=posts` | Đánh giá tỷ lệ giữ chân người xem và CTR của từng post video |
| 6.4 | Đơn hàng & Chuyển đổi | Đo lường & Winner → Đơn hàng & Chuyển đổi | `index.php?com=analytics&act=conversions` | Danh sách đơn hàng đối soát từ AccessTrade/Shopee/TikTok Shop |
| 6.5 | Nhập CSV Đối soát | Đo lường & Winner → Nhập CSV đối soát | `index.php?com=analytics&act=conversion_import` | Tải lên file Excel/CSV báo cáo hoa hồng từ sàn để hệ thống đối chiếu tự động |
| 6.6 | Phát hiện Winner | Đo lường & Winner → Winner Detection | `index.php?com=analytics&act=winner_detection` | Thuật toán tự động tìm ra sản phẩm "thắng lớn" để đề xuất tăng ngân sách |
| 6.7 | Khuyến nghị tối ưu (A/B) | Tối ưu hóa (A/B) → Khuyến nghị tối ưu | `index.php?com=optimization&act=recommendations`| Xem gợi ý của AI về việc đổi Hook video, đổi giờ đăng, thay link ưu đãi |
| 6.8 | Thử nghiệm A/B Experiments | Tối ưu hóa (A/B) → Thử nghiệm A/B | `index.php?com=optimization&act=experiments` | Tạo và theo dõi hiệu quả giữa 2 biến thể content/video cùng sản phẩm |

---

### NHÓM 7: QUẢN TRỊ NỘI DUNG THƯƠNG MẠI & SẢN PHẨM (CATALOG)
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 7.1 | Danh sách Sản phẩm | Quản lý sản phẩm → Sản phẩm | `index.php?com=product&act=man&type=san-pham` | Quản lý toàn bộ danh mục sản phẩm trên website |
| 7.2 | Thêm sản phẩm mới | Danh sách sản phẩm → Nút "Thêm mới" | `index.php?com=product&act=add&type=san-pham` | Nhập thông tin sản phẩm, giá bán, mô tả, thông số kỹ thuật specs |
| 7.3 | Chỉnh sửa sản phẩm | Danh sách sản phẩm → Click vào tên/icon sửa | `index.php?com=product&act=edit&type=san-pham&id=...` | Sửa giá, kho hàng, ưu đãi, gắn link Affiliate đa sàn |
| 7.4 | Sao chép sản phẩm | Danh sách sản phẩm → Icon Sao chép (Copy) | `index.php?com=product&act=copy&type=san-pham&id=...` | Nhân bản nhanh sản phẩm tương tự để tiết kiệm thời gian nhập liệu |
| 7.5 | Xóa sản phẩm | Danh sách sản phẩm → Icon Thùng rác | `index.php?com=product&act=delete&type=san-pham&id=...` | Xóa sản phẩm đơn lẻ hoặc xóa hàng loạt qua checkbox |
| 7.6 | Danh mục Cấp 1, 2, 3, 4 | Quản lý sản phẩm → Danh mục cấp 1/2/3/4 | `index.php?com=product&act=man_list&type=san-pham` | Phân tầng cây danh mục sản phẩm (Ví dụ: Đồ Gym → Phụ kiện → Đai lưng) |
| 7.7 | Quản lý Thương hiệu | Quản lý sản phẩm → Danh mục Hãng | `index.php?com=product&act=man_brand&type=san-pham` | Thêm/sửa các thương hiệu sản phẩm (Aolikes, Valeo, Harbinger,...) |
| 7.8 | Màu sắc & Kích thước | Quản lý sản phẩm → Màu sắc / Kích thước | `index.php?com=product&act=man_color&type=san-pham` | Quản lý biến thể màu sắc và bảng size (S, M, L, XL) |
| 7.9 | Import / Export Excel | Quản lý sản phẩm → Import / Export | `index.php?com=import&act=man&type=san-pham` | Nhập xuất danh sách sản phẩm hàng loạt qua file Excel |

---

### NHÓM 8: QUẢN TRỊ BÀI VIẾT, TIN TỨC & BÌNH LUẬN
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 8.1 | Quản lý Tin tức & Blog | Quản lý bài viết → Tin tức | `index.php?com=news&act=man&type=tin-tuc` | Đăng bài viết chia sẻ kiến thức thể hình, dinh dưỡng, review |
| 8.2 | Quản lý Chính sách | Quản lý bài viết → Chính sách | `index.php?com=news&act=man&type=chinh-sach` | Soạn thảo các trang: Chính sách đổi trả, Bảo mật, Điều khoản dịch vụ |
| 8.3 | Quản lý Bình luận | Quản lý bình luận → Danh sách | `index.php?com=comment&act=man` | Duyệt đánh giá sao của người dùng, trả lời và ẩn bình luận spam |
| 8.4 | Quản lý Đơn hàng | Quản lý đơn hàng → Đơn hàng | `index.php?com=order&act=man` | Tiếp nhận đơn đặt hàng trực tiếp, cập nhật trạng thái giao dịch |
| 8.5 | Quản lý Liên hệ | Quản lý liên hệ → Thông tin liên hệ | `index.php?com=contact&act=man` | Xem tin nhắn góp ý, liên hệ hợp tác từ khách hàng gửi qua web |
| 8.6 | Đăng ký nhận tin | Quản lý liên hệ → Đăng ký nhận tin | `index.php?com=newsletter&act=man` | Danh sách email khách hàng đăng ký nhận tin khuyến mãi |

---

### NHÓM 9: HÌNH ẢNH, GIAO DIỆN & TRANG TĨNH
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 9.1 | Quản lý Slideshow | Quản lý hình ảnh → Slideshow | `index.php?com=photo&act=man_photo&type=slide` | Thay đổi banner slider chạy ngoài trang chủ |
| 9.2 | Quản lý Logo & Favicon | Quản lý hình ảnh → Logo / Favicon | `index.php?com=photo&act=photo_static&type=logo` | Tải lên logo nhận diện thương hiệu và icon trình duyệt |
| 9.3 | Quản lý Mạng xã hội | Quản lý hình ảnh → Mạng xã hội | `index.php?com=photo&act=man_photo&type=social` | Cấu hình liên kết icon Facebook, TikTok, Instagram, YouTube ở chân trang |
| 9.4 | Quản lý Watermark | Quản lý hình ảnh → Watermark | `index.php?com=photo&act=photo_static&type=watermark` | Đóng dấu bản quyền ảnh tự động khi tải lên sản phẩm |
| 9.5 | Trang Giới thiệu | Quản lý trang tĩnh → Giới thiệu | `index.php?com=static&act=update&type=gioi-thieu` | Soạn thảo nội dung bài giới thiệu về thương hiệu KhoePro |
| 9.6 | Thông tin Chân trang | Quản lý trang tĩnh → Footer | `index.php?com=static&act=update&type=footer` | Soạn thảo thông tin địa chỉ, hotline, bản quyền dưới chân trang |

---

### NHÓM 10: CẤU HÌNH HỆ THỐNG, TÀI KHOẢN & TIỆN ÍCH
| STT | Tên chức năng | Menu truy cập | URL | Mục đích & Nghiệp vụ |
| --- | ------------- | ------------- | --- | -------------------- |
| 10.1| Thiết lập thông tin chung | Menu chính → Thiết lập thông tin | `index.php?com=setting&act=update` | Cập nhật tên công ty, Hotline, Email, Tọa độ bản đồ, Google Analytics |
| 10.2| Quản lý Tài khoản Admin | Quản lý user → Tài khoản admin | `index.php?com=user&act=man_admin` | Tạo tài khoản nhân viên, đổi mật khẩu, kích hoạt/khóa tài khoản |
| 10.3| Thông tin cá nhân Admin | Top Header / User → Thông tin cá nhân | `index.php?com=user&act=info_admin` | Thay đổi họ tên, email, mật khẩu của tài khoản đang đăng nhập |
| 10.4| Quản lý Phân quyền | Quản lý user → Nhóm quyền | `index.php?com=user&act=permission_group` | Phân quyền chi tiết cho Biên tập viên, Kế toán, Quản trị viên |
| 10.5| Quản lý Ngôn ngữ | Top Header / Cấu hình → Ngôn ngữ | `index.php?com=lang&act=man&type=web` | Biên dịch các nhãn hiển thị Tiếng Việt / Tiếng Anh |
| 10.6| SEO Page Cố định | Menu chính → Quản lý SEO page | `index.php?com=seopage&act=update&type=san-pham` | Tối ưu thẻ Title, Description, Keywords cho từng trang tĩnh |
| 10.7| Xóa bộ nhớ đệm (Clear Cache) | Top Header → Icon Xóa Cache | `index.php?com=cache&act=delete` | Xóa cache hệ thống để cập nhật ngay lập tức các thay đổi ngoài frontend |

---

## 3. TỔNG KẾT SỐ LƯỢNG
- **Tổng số nhóm chức năng**: 10 nhóm
- **Tổng số module & màn hình chức năng chi tiết**: 58 chức năng
- **Tất cả các chức năng đã được xác minh trên mã nguồn thực tế và kiểm tra khả năng điều hướng.**
