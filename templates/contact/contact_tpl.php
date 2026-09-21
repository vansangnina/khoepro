<?php
/* Template: Liên hệ - Khỏe Pro Pro Standard */
$message = $flash->get('message');
$flashStatus = '';
$flashMessages = [];
if (!empty($message)) {
    $decodedMsg = json_decode(base64_decode($message), true);
    if (!empty($decodedMsg['status'])) {
        $flashStatus = $decodedMsg['status'];
        $flashMessages = $decodedMsg['messages'] ?? [];
    }
}
?>

<div class="fitnado-contact-page">
    <!-- Breadcrumb -->
    <div class="fitnado-breadcrumb-bar">
        <div class="fitnado-wrap">
            <nav aria-label="breadcrumb">
                <ol class="fitnado-breadcrumb">
                    <li class="breadcrumb-item"><a href=""><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $titleMain ?? 'Liên hệ' ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Contact Hero Header -->
    <div class="contact-hero-section">
        <div class="fitnado-wrap">
            <div class="contact-hero-content">
                <div class="contact-badge-pill">
                    <i class="fa-solid fa-shield-halved"></i> Độc lập • Minh bạch • Khách quan
                </div>
                <h1 class="contact-hero-title">Liên Hệ Ban Biên Tập & Hợp Tác Khỏe Pro</h1>
                <p class="contact-hero-desc">
                    Khỏe Pro luôn sẵn sàng lắng nghe mọi đóng góp từ bạn đọc, chuyên gia thể hình, huấn luyện viên thể thao cũng như các thương hiệu phân phối dụng cụ, thiết bị gym chính hãng.
                </p>
                <div class="contact-trust-badges">
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-bolt-lightning text-warning"></i>
                        <span>Phản hồi trong <strong>24h</strong></span>
                    </div>
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span>Thẩm định <strong>chuẩn kỹ thuật</strong></span>
                    </div>
                    <div class="trust-badge-item">
                        <i class="fa-solid fa-user-shield text-primary"></i>
                        <span>Bảo mật thông tin <strong>100%</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Section -->
    <div class="contact-main-section">
        <div class="fitnado-wrap">
            <div class="contact-grid-container">
                <!-- Left Column: Direct Info & Editorial Standards -->
                <div class="contact-info-column">
                    <div class="contact-info-header">
                        <h2 class="contact-col-title">Kênh Thông Tin Trực Tiếp</h2>
                        <p class="contact-col-subtitle">Kết nối trực tiếp với đội ngũ quản trị, chuyên viên thẩm định dụng cụ và bộ phận quan hệ đối tác của Khỏe Pro.</p>
                    </div>

                    <div class="contact-cards-list">
                        <!-- Card 1: Địa chỉ -->
                        <div class="contact-card-item">
                            <div class="contact-card-icon icon-address">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="contact-card-body">
                                <span class="contact-card-label">Địa chỉ tòa soạn & Văn phòng</span>
                                <strong class="contact-card-value"><?= !empty($optsetting['address']) ? $optsetting['address'] : '123 Huỳnh Thúc Kháng, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh' ?></strong>
                                <span class="contact-card-note">Khu vực tiếp nhận mẫu sản phẩm & tài liệu kiểm định</span>
                            </div>
                        </div>

                        <!-- Card 2: Hotline & Zalo -->
                        <div class="contact-card-item">
                            <div class="contact-card-icon icon-phone">
                                <i class="fa-solid fa-phone-volume"></i>
                            </div>
                            <div class="contact-card-body">
                                <span class="contact-card-label">Hotline & Zalo Chuyên Gia</span>
                                <strong class="contact-card-value">
                                    <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline'] ?? '0867508149') ?>">
                                        <?= !empty($optsetting['hotline']) ? $optsetting['hotline'] : '086 750 8149' ?>
                                    </a>
                                </strong>
                                <span class="contact-card-note">Tư vấn chọn mua dụng cụ, phản hồi bài viết & hỗ trợ đối tác</span>
                            </div>
                        </div>

                        <!-- Card 3: Email -->
                        <div class="contact-card-item">
                            <div class="contact-card-icon icon-email">
                                <i class="fa-solid fa-envelope-open-text"></i>
                            </div>
                            <div class="contact-card-body">
                                <span class="contact-card-label">Email Tiếp Nhận Hồ Sơ</span>
                                <strong class="contact-card-value">
                                    <a href="mailto:<?= !empty($optsetting['email']) ? $optsetting['email'] : 'contact@khoepro.com' ?>">
                                        <?= !empty($optsetting['email']) ? $optsetting['email'] : 'contact@khoepro.com' ?>
                                    </a>
                                </strong>
                                <span class="contact-card-note">Gửi thông cáo báo chí, đề xuất thẩm định thiết bị</span>
                            </div>
                        </div>

                        <!-- Card 4: Giờ làm việc -->
                        <div class="contact-card-item">
                            <div class="contact-card-icon icon-clock">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="contact-card-body">
                                <span class="contact-card-label">Thời Gian Làm Việc</span>
                                <strong class="contact-card-value"><?= !empty($optsetting['worktime']) ? $optsetting['worktime'] : '08:00 - 18:00 (Thứ 2 - Thứ 7)' ?></strong>
                                <span class="contact-card-note">Chủ Nhật & ngày lễ tiếp nhận tự động qua Form/Email</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Direct Action Buttons -->
                    <div class="contact-action-pills">
                        <a href="https://zalo.me/<?= preg_replace('/[^0-9]/', '', $optsetting['zalo'] ?? '0867508149') ?>" target="_blank" rel="nofollow" class="btn-action-pill btn-zalo">
                            <i class="fa-solid fa-comment-dots"></i> Chat Zalo Ngay
                        </a>
                        <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline'] ?? '0867508149') ?>" class="btn-action-pill btn-call">
                            <i class="fa-solid fa-phone"></i> Gọi Tổng Đài
                        </a>
                        <?php if (!empty($optsetting['fanpage'])) { ?>
                            <a href="<?= $optsetting['fanpage'] ?>" target="_blank" rel="nofollow" class="btn-action-pill btn-facebook">
                                <i class="fa-brands fa-facebook"></i> Fanpage
                            </a>
                        <?php } ?>
                    </div>

                    <!-- Editorial Transparency Note -->
                    <div class="editorial-trust-box">
                        <div class="trust-box-icon">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <div class="trust-box-text">
                            <h4>Nguyên Tắc Biên Tập Của Khỏe Pro</h4>
                            <p>
                                Mọi đánh giá và bảng xếp hạng trên Khỏe Pro đều dựa trên tiêu chí đo lường độ bền, độ an toàn và cảm giác tập luyện thực tế. Chúng tôi <strong>không nhận tài trợ để làm sai lệch kết quả đánh giá</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Contact Form -->
                <div class="contact-form-column">
                    <div class="contact-form-card">
                        <div class="form-card-header">
                            <span class="form-card-badge"><i class="fa-solid fa-paper-plane"></i> Biểu Mẫu Trực Tuyến</span>
                            <h3 class="form-card-title">Gửi Thông Điệp Đến Ban Biên Tập</h3>
                            <p class="form-card-desc">Vui lòng điền đầy đủ thông tin bên dưới. Đội ngũ chuyên môn sẽ phản hồi sớm nhất qua Email hoặc Số điện thoại của bạn.</p>
                        </div>

                        <!-- Flash Alerts -->
                        <?php if (!empty($flashMessages)) { ?>
                            <div class="alert alert-<?= ($flashStatus == 'danger') ? 'danger' : 'success' ?> contact-alert mb-4">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <strong><?= ($flashStatus == 'danger') ? 'Vui lòng kiểm tra lại thông tin:' : 'Thông báo:' ?></strong>
                                </div>
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($flashMessages as $msg) { ?>
                                        <li><?= $msg ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>

                        <form class="contact-form-inner validation-contact" novalidate method="post" action="lien-he" enctype="multipart/form-data">
                            <div class="row g-3">
                                <!-- Họ tên -->
                                <div class="col-md-6">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="fullname">Họ và tên của bạn <span class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fa-regular fa-user"></i>
                                            <input type="text" class="contact-input-control" id="fullname" name="dataContact[fullname]" placeholder="Ví dụ: Nguyễn Văn Hùng" value="<?= $flash->get('fullname') ?>" required />
                                        </div>
                                        <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                                    </div>
                                </div>

                                <!-- Số điện thoại -->
                                <div class="col-md-6">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fa-solid fa-phone"></i>
                                            <input type="tel" class="contact-input-control" id="phone" name="dataContact[phone]" placeholder="Ví dụ: 0988 123 456" value="<?= $flash->get('phone') ?>" required />
                                        </div>
                                        <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ</div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="email">Địa chỉ Email <span class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fa-regular fa-envelope"></i>
                                            <input type="email" class="contact-input-control" id="email" name="dataContact[email]" placeholder="email@domain.com" value="<?= $flash->get('email') ?>" required />
                                        </div>
                                        <div class="invalid-feedback">Vui lòng nhập địa chỉ email hợp lệ</div>
                                    </div>
                                </div>

                                <!-- Địa chỉ / Khu vực -->
                                <div class="col-md-6">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="address">Địa chỉ / Tỉnh thành <span class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fa-solid fa-map-pin"></i>
                                            <input type="text" class="contact-input-control" id="address" name="dataContact[address]" placeholder="Ví dụ: Quận 1, TP. HCM" value="<?= $flash->get('address') ?>" required />
                                        </div>
                                        <div class="invalid-feedback">Vui lòng nhập địa chỉ hoặc tỉnh thành</div>
                                    </div>
                                </div>

                                <!-- Chủ đề -->
                                <div class="col-12">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="subject">Chủ đề liên hệ <span class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fa-solid fa-tag"></i>
                                            <input type="text" list="subject-suggestions" class="contact-input-control" id="subject" name="dataContact[subject]" placeholder="Chọn hoặc nhập chủ đề liên hệ..." value="<?= $flash->get('subject') ?>" required />
                                            <datalist id="subject-suggestions">
                                                <option value="Góp ý / Bổ sung thông tin bài đánh giá">
                                                <option value="Đề xuất gửi sản phẩm dụng cụ gym để review">
                                                <option value="Tư vấn chọn mua thiết bị thể thao phù hợp">
                                                <option value="Hợp tác truyền thông & Liên kết nội dung">
                                                <option value="Báo cáo lỗi kỹ thuật hoặc liên kết hỏng">
                                                <option value="Liên hệ hợp tác phân phối & Affiliate">
                                            </datalist>
                                        </div>
                                        <div class="invalid-feedback">Vui lòng nhập chủ đề liên hệ</div>
                                    </div>
                                </div>

                                <!-- Nội dung -->
                                <div class="col-12">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="content">Nội dung chi tiết <span class="text-danger">*</span></label>
                                        <textarea class="contact-input-control contact-textarea" id="content" name="dataContact[content]" rows="4" placeholder="Mô tả chi tiết nội dung, thắc mắc hoặc thông tin sản phẩm bạn muốn chia sẻ..." required><?= $flash->get('content') ?></textarea>
                                        <div class="invalid-feedback">Vui lòng nhập nội dung liên hệ</div>
                                    </div>
                                </div>

                                <!-- File đính kèm -->
                                <div class="col-12">
                                    <div class="contact-form-group">
                                        <label class="contact-form-label" for="file_attach">Đính kèm tài liệu / Hình ảnh (Nếu có)</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" class="form-control" name="file_attach" id="file_attach" accept=".doc,.docx,.pdf,.rar,.zip,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp">
                                            <small class="text-muted d-block mt-1">Định dạng hỗ trợ: PDF, DOC, XLS, ZIP, RAR, JPG, PNG, WEBP (Tối đa 10MB)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit & Action Buttons -->
                                <div class="col-12 pt-2">
                                    <div class="form-submit-actions">
                                        <button type="submit" class="btn-submit-contact" name="submit-contact" value="1">
                                            <span>Gửi Thông Điệp Ngay</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </button>
                                        <button type="reset" class="btn-reset-contact">
                                            <i class="fa-solid fa-arrow-rotate-left"></i> Nhập lại
                                        </button>
                                    </div>
                                    <p class="form-privacy-note mt-3 mb-0">
                                        <i class="fa-solid fa-lock text-success"></i> Thông tin của bạn được cam kết bảo mật tuyệt đối theo <a href="chinh-sach-bao-mat">Chính sách bảo mật Khỏe Pro</a>.
                                    </p>
                                </div>
                            </div>

                            <input type="hidden" name="recaptcha_response_contact" id="recaptchaResponseContact">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & FAQ Section -->
    <div class="contact-bottom-section">
        <div class="fitnado-wrap">
            <div class="contact-bottom-grid">
                <!-- Map / Office Location -->
                <div class="contact-map-card">
                    <div class="map-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-primary"></i>
                            <h3 class="mb-0 h5 font-weight-bold">Bản Đồ Chỉ Đường Đến Văn Phòng</h3>
                        </div>
                        <a href="https://maps.google.com/?q=<?= urlencode($optsetting['address'] ?? '123 Huỳnh Thúc Kháng, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh') ?>" target="_blank" class="map-direct-link">
                            Mở Google Maps <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                    <div class="map-card-iframe">
                        <?php if (!empty($optsetting['coords_iframe'])) { ?>
                            <?= $func->decodeHtmlChars($optsetting['coords_iframe']) ?>
                        <?php } else { ?>
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.5178293943967!2d106.69834837583842!3d10.771594959281692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3f1e944439%3A0xb3631efbe546a36c!2zMTIzIEh14buzbmggVGjDumMgS2jDoW5nLCBC4bq_biBOZ2jDqSwgUXXhuq1uIDEsIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2svn!4v1710000000000!5m2!1svi!2svn" width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <?php } ?>
                    </div>
                </div>

                <!-- FAQ Box -->
                <div class="contact-faq-card">
                    <div class="faq-card-header">
                        <i class="fa-solid fa-circle-question text-warning"></i>
                        <h3 class="mb-0 h5 font-weight-bold">Câu Hỏi Thường Gặp Khi Liên Hệ</h3>
                    </div>
                    <div class="contact-faq-list">
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fa-solid fa-chevron-right text-primary"></i>
                                <span>Khỏe Pro có bán hàng trực tiếp không?</span>
                            </div>
                            <div class="faq-answer">
                                Không. Khỏe Pro là nền tảng đánh giá, so sánh và thẩm định chất lượng độc lập. Chúng tôi cung cấp đường dẫn đến các gian hàng chính hãng được kiểm duyệt để bạn đọc tham khảo giá tốt nhất.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fa-solid fa-chevron-right text-primary"></i>
                                <span>Làm sao để gửi dụng cụ tập gym để Khỏe Pro thẩm định?</span>
                            </div>
                            <div class="faq-answer">
                                Bạn có thể điền form liên hệ trên hoặc gửi email đến <strong><?= $optsetting['email'] ?? 'contact@khoepro.com' ?></strong> đính kèm thông số kỹ thuật, chứng chỉ xuất xứ (CO/CQ). Bộ phận kỹ thuật sẽ phản hồi trong 24 giờ.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fa-solid fa-chevron-right text-primary"></i>
                                <span>Khỏe Pro có thu phí viết bài đánh giá theo yêu cầu không?</span>
                            </div>
                            <div class="faq-answer">
                                Mọi bài review của Khỏe Pro đều tuân thủ quy trình kiểm thử khách quan. Chúng tôi không nhận tiền để tâng bốc sản phẩm kém chất lượng nhằm đảm bảo sự minh bạch tối đa cho người tập luyện.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
