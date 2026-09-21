<?php if (!empty($static)) { ?>
    <?php if ($type == 'gioi-thieu') { ?>
        <!-- PRO ABOUT US PAGE (FITNADO / KHỎE PRO) -->
        <div class="fitnado-about-page">
            <!-- Hero Banner -->
            <div class="about-hero-section">
                <div class="about-hero-badge">
                    <i class="fa-solid fa-shield-halved"></i> Độc Lập · Khách Quan · Minh Bạch
                </div>
                <h1 class="about-hero-title"><?= !empty($static['name' . $lang]) ? $static['name' . $lang] : 'Về Chúng Tôi – Khỏe Pro' ?></h1>
                <p class="about-hero-subtitle">
                    Nền tảng nghiên cứu, đánh giá độc lập và so sánh chuyên sâu các thiết bị tập gym, dụng cụ thể thao và giải pháp phục hồi cơ bắp hàng đầu tại Việt Nam.
                </p>
                <div class="about-hero-stats">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
                        <div class="stat-meta">
                            <strong>100%</strong>
                            <span>Độc lập & Khách quan</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-dumbbell"></i></div>
                        <div class="stat-meta">
                            <strong>500+</strong>
                            <span>Sản phẩm đánh giá</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                        <div class="stat-meta">
                            <strong>Khoa Học</strong>
                            <span>Chuẩn an toàn chuyển động</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                        <div class="stat-meta">
                            <strong>Minh Bạch</strong>
                            <span>Đối tác & Khuyến nghị</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision Cards -->
            <div class="about-mission-section">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="about-feature-box">
                            <div class="box-icon icon-blue">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <h3>Sứ mệnh của Khỏe Pro</h3>
                            <p>
                                Giúp bạn đọc thoát khỏi <strong>"ma trận"</strong> hàng ngàn sản phẩm dụng cụ tập luyện được quảng cáo thổi phồng trên thị trường. Chúng tôi cung cấp những bài phân tích chi tiết, chỉ rõ ưu điểm và nhược điểm thực tế để bạn chọn đúng sản phẩm phù hợp với thể trạng và mục tiêu tập luyện.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-feature-box">
                            <div class="box-icon icon-green">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <h3>Tầm nhìn phát triển</h3>
                            <p>
                                Trở thành <strong>điểm tựa tri thức và thư viện đánh giá thiết bị thể thao uy tín số 1 tại Việt Nam</strong>. Mọi người tập từ người mới bắt đầu (Beginner) đến vận động viên thể hình chuyên nghiệp đều có thể tìm thấy lời khuyên giá trị trước khi ra quyết định đầu tư đồ tập.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Core Pillars -->
            <div class="about-pillars-section">
                <div class="text-center mb-4">
                    <span class="section-tag"><i class="fa-solid fa-layer-group"></i> 4 TRỤ CỘT NỘI DUNG</span>
                    <h2 class="section-heading">Chúng tôi mang lại giá trị gì cho bạn?</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="pillar-card">
                            <div class="pillar-num">01</div>
                            <div class="pillar-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                            <h4>Đánh giá Chuyên sâu</h4>
                            <p>Phân tích chất liệu (da bò, nylon, mút EVA, bọt TPE), độ dày, đường may chịu lực và độ hoàn thiện cơ khí.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="pillar-card">
                            <div class="pillar-num">02</div>
                            <div class="pillar-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                            <h4>So sánh Đối đầu</h4>
                            <p>Đặt các dòng sản phẩm cùng phân khúc lên bàn cân so sánh trực quan về hiệu năng, độ bền và chi phí đầu tư.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="pillar-card">
                            <div class="pillar-num">03</div>
                            <div class="pillar-icon"><i class="fa-solid fa-ruler-combined"></i></div>
                            <h4>Hướng dẫn Chọn Size</h4>
                            <p>Bộ tiêu chí chọn size đai lưng, kích thước dây kháng lực, độ êm của thảm tập chuẩn theo công thái học y sinh.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="pillar-card">
                            <div class="pillar-num">04</div>
                            <div class="pillar-icon"><i class="fa-solid fa-tag"></i></div>
                            <h4>Săn Giá Tốt Chính Hãng</h4>
                            <p>Cập nhật giá ưu đãi thực tế từ các gian hàng chính hãng Shopee Mall, LazMall, TikTok Shop và NPP ủy quyền.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editorial Workflow / Methodology -->
            <div class="about-process-section">
                <div class="process-inner">
                    <div class="text-center mb-5">
                        <span class="section-tag text-white"><i class="fa-solid fa-flask-vial"></i> QUY TRÌNH BIÊN TẬP</span>
                        <h2 class="section-heading text-white">Tiêu Chuẩn Đánh Giá Nghiêm Ngặt</h2>
                        <p class="text-light opacity-75">Quy trình 4 bước thẩm định trước khi xuất bản bất kỳ bài viết review nào</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="process-step">
                                <div class="step-badge">Bước 1</div>
                                <h5>Nghiên cứu & Mẫu thử</h5>
                                <p>Thu thập thông số kỹ thuật từ nhà sản xuất, mua mẫu thực tế và kiểm tra chất liệu bao bì.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="process-step">
                                <div class="step-badge">Bước 2</div>
                                <h5>Thử nghiệm Cơ học</h5>
                                <p>Đo lường độ chịu lực, độ co giãn của sợi thun, độ bám sàn của thảm và biên độ dao động của súng massage.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="process-step">
                                <div class="step-badge">Bước 3</div>
                                <h5>Chấm điểm 5 Tiêu chí</h5>
                                <p>Đánh giá theo thang điểm 10 dựa trên: Chất liệu, Hiệu năng, Độ bền, Công thái học và Giá trị mang lại.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="process-step">
                                <div class="step-badge">Bước 4</div>
                                <h5>Phản biện & Tái kiểm tra</h5>
                                <p>Tiếp nhận phản hồi từ cộng đồng tập luyện thực tế và cập nhật lại bài viết sau 3 - 6 tháng sử dụng.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dynamic Content from Database if present -->
            <?php if (!empty($static['content' . $lang])) { ?>
                <div class="about-body-content">
                    <div class="content-box">
                        <?= $func->decodeHtmlChars($static['content' . $lang]) ?>
                    </div>
                </div>
            <?php } ?>

            <!-- Contact & CTA Banner -->
            <div class="about-cta-section">
                <div class="cta-card">
                    <div class="cta-content">
                        <h3>Bạn có thắc mắc hoặc cần tư vấn chọn thiết bị?</h3>
                        <p>Đội ngũ chuyên viên Khỏe Pro luôn sẵn sàng giải đáp và hỗ trợ bạn tìm được món đồ tập ưng ý nhất.</p>
                        <div class="cta-actions">
                            <a href="san-pham" class="btn-cta-primary"><i class="fa-solid fa-compass"></i> Khám phá sản phẩm</a>
                            <a href="lien-he" class="btn-cta-secondary"><i class="fa-solid fa-envelope"></i> Liên hệ ban biên tập</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Share block -->
            <div class="about-share-block">
                <span class="share-label"><i class="fa-solid fa-share-nodes"></i> Chia sẻ trang này:</span>
                <div class="share-social-links">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($func->getCurrentPageURL()) ?>" target="_blank" class="share-btn fb" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($func->getCurrentPageURL()) ?>" target="_blank" class="share-btn tw" title="Twitter/X"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://zalo.me/share?url=<?= urlencode($func->getCurrentPageURL()) ?>" target="_blank" class="share-btn zalo" title="Zalo"><strong>Z</strong></a>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- STANDARD STATIC PAGE TEMPLATE -->
        <main class="fitnado-wrap py-4">
            <div class="fitnado-static-page">
                <div class="fitnado-sectionHead mb-4">
                    <div>
                        <h2><?= $static['name' . $lang] ?></h2>
                        <p>Thông tin chính sách và điều khoản từ Khỏe Pro.</p>
                    </div>
                </div>
                <div class="content-main static-content-box">
                    <?= $func->decodeHtmlChars($static['content' . $lang]) ?>
                </div>
            </div>
        </main>
    <?php } ?>
<?php } else { ?>
    <main class="fitnado-wrap py-4">
        <div class="alert alert-warning w-100 text-center my-4" role="alert">
            <strong><i class="fa-solid fa-triangle-exclamation"></i> <?= dangcapnhatdulieu ?></strong>
        </div>
    </main>
<?php } ?>
