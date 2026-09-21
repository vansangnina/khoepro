<footer class="fitnado-footer">
    <div class="fitnado-wrap">
        <div class="fitnado-foot">
            <!-- Column 1: Brand & Contact Info -->
            <div class="footer-col-brand">
                <a class="fitnado-logo" href="" title="<?= (!empty($setting['name' . $lang])) ? $setting['name' . $lang] : 'Khỏe Pro' ?>">
                    <?php if (!empty($logo['photo'])) { ?>
                        <img onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" src="<?= THUMBS ?>/150x60x2/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>" alt="<?= (!empty($setting['name' . $lang])) ? $setting['name' . $lang] : 'Khỏe Pro' ?>" />
                        <div class="logo-fallback" style="display:none;">
                            <span class="logo-text">Khỏe Pro</span>
                            <small><?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.' ?></small>
                        </div>
                    <?php } else { ?>
                        <span class="logo-text">Khỏe Pro</span>
                        <small><?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.' ?></small>
                    <?php } ?>
                </a>
                <p class="footer-brand-desc">
                    Khỏe Pro (khoepro.com) là nền tảng đánh giá, so sánh dụng cụ tập gym, thiết bị thể thao và chia sẻ kiến thức thể hình chuyên sâu. Chúng tôi giúp bạn lựa chọn đúng thiết bị, nâng cao hiệu quả tập luyện và phòng ngừa chấn thương.
                </p>
                <div class="footer-contact-list">
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?= !empty($optsetting['address']) ? $optsetting['address'] : '123 Huỳnh Thúc Kháng, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh' ?></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>Hotline: <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline'] ?? '0867508149') ?>"><?= !empty($optsetting['hotline']) ? $optsetting['hotline'] : '086 750 8149' ?></a></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Email: <a href="mailto:<?= !empty($optsetting['email']) ? $optsetting['email'] : 'contact@khoepro.com' ?>"><?= !empty($optsetting['email']) ? $optsetting['email'] : 'contact@khoepro.com' ?></a></span>
                    </div>
                </div>
            </div>

            <!-- Column 2: Về chúng tôi -->
            <div class="footer-col-nav">
                <h4 class="footer-heading">Về Khỏe Pro</h4>
                <ul class="footer-links">
                    <li><a href="gioi-thieu" title="Giới thiệu về Khỏe Pro">Giới thiệu chung</a></li>
                    <li><a href="phuong-phap-danh-gia" title="Phương pháp đánh giá & Tiêu chuẩn biên tập">Tiêu chuẩn biên tập</a></li>
                    <li><a href="cam-ket-khach-quan" title="Cam kết đánh giá khách quan & Trung thực">Cam kết khách quan</a></li>
                    <li><a href="lien-he" title="Liên hệ ban biên tập"><?= lienhe ?></a></li>
                </ul>
            </div>

            <!-- Column 3: Danh mục đồ tập -->
            <div class="footer-col-nav">
                <h4 class="footer-heading">Danh Mục Đồ Tập</h4>
                <ul class="footer-links">
                    <?php if (!empty($productListMenu)) {
                        foreach ($productListMenu as $k => $v) {
                            if ($k >= 4) break; ?>
                            <li><a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a></li>
                        <?php }
                    } else { ?>
                        <li><a href="san-pham">Đai lưng tập gym</a></li>
                        <li><a href="san-pham">Dây kháng lực & Phụ kiện</a></li>
                        <li><a href="san-pham">Súng massage phục hồi</a></li>
                        <li><a href="san-pham">Con lăn & Thảm tập</a></li>
                    <?php } ?>
                    <li><a href="san-pham" class="link-view-all">Tất cả sản phẩm →</a></li>
                </ul>
            </div>

            <!-- Column 4: Hỗ trợ & Cẩm nang -->
            <div class="footer-col-nav">
                <h4 class="footer-heading">Hỗ Trợ & Cẩm Nang</h4>
                <ul class="footer-links">
                    <li><a href="huong-dan-chon-mua" title="Hướng dẫn chọn mua đồ tập">Hướng dẫn chọn mua</a></li>
                    <li><a href="so-sanh-san-pham" title="So sánh đối đầu thiết bị">So sánh sản phẩm</a></li>
                    <li><a href="video" title="Video 30s Review thực tế">Video 30s Review</a></li>
                    <li><a href="chinh-sach-bao-mat" title="Chính sách bảo mật thông tin">Chính sách bảo mật</a></li>
                    <li><a href="dieu-khoan-su-dung" title="Điều khoản sử dụng">Điều khoản sử dụng</a></li>
                </ul>
            </div>

            <!-- Column 5: Kênh truyền thông & Kết nối -->
            <div class="footer-col-social">
                <h4 class="footer-heading">Kết Nối Truyền Thông</h4>
                <div class="footer-social-links">
                    <a href="<?= !empty($optsetting['fanpage']) ? $optsetting['fanpage'] : 'https://facebook.com/khoepro' ?>" target="_blank" rel="nofollow" class="footer-social-btn btn-fb">
                        <i class="fa-brands fa-facebook-f"></i> <span>Facebook Fanpage</span>
                    </a>
                    <a href="https://zalo.me/<?= preg_replace('/[^0-9]/', '', $optsetting['zalo'] ?? '0867508149') ?>" target="_blank" rel="nofollow" class="footer-social-btn btn-zl">
                        <i class="fa-solid fa-comment-dots"></i> <span>Zalo Chuyên Gia</span>
                    </a>
                    <a href="mailto:<?= !empty($optsetting['email']) ? $optsetting['email'] : 'contact@khoepro.com' ?>" class="footer-social-btn btn-em">
                        <i class="fa-regular fa-envelope"></i> <span>Gửi Thư Tòa Soạn</span>
                    </a>
                </div>
                <div class="footer-trust-badge">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <div>
                        <strong>100% Khách quan</strong>
                        <span>Nền tảng kiểm định & review độc lập</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Powered & Copyright Bar -->
        <div class="fitnado-footer-powered">
            <div class="footer-copyright-text">
                Copyright © <?= date("Y") ?> Khỏe Pro (khoepro.com). All rights reserved.
            </div>
            <div class="footer-analytics-text">
                <span><i class="fa-solid fa-circle text-success me-1" style="font-size:8px;"></i> Đang online: <strong><?= (!empty($online)) ? $online : 1 ?></strong></span>
                <span class="mx-2">|</span>
                <span><i class="fa-solid fa-chart-line text-primary me-1"></i> Tổng truy cập: <strong><?= (!empty($counter['total'])) ? number_format($counter['total']) : '10,641' ?></strong></span>
            </div>
        </div>
    </div>
</footer>
