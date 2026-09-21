<?php
/* Template: Bài viết tĩnh / Giới thiệu - Khỏe Pro Pro Standard */
?>

<div class="fitnado-static-page">
    <!-- Breadcrumb -->
    <div class="fitnado-breadcrumb-bar">
        <div class="fitnado-wrap">
            <nav aria-label="breadcrumb">
                <ol class="fitnado-breadcrumb">
                    <li class="breadcrumb-item"><a href=""><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $static['name' . $lang] ?? ($titleMain ?? 'Giới thiệu') ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Static Hero Header -->
    <div class="static-hero-section">
        <div class="fitnado-wrap">
            <div class="static-hero-content">
                <div class="static-badge-pill">
                    <i class="fa-solid fa-shield-halved"></i> Tôn Chỉ Khỏe Pro • Minh Bạch • Chuyên Sâu
                </div>
                <h1 class="static-hero-title"><?= $static['name' . $lang] ?? 'Giới Thiệu Về Khỏe Pro' ?></h1>
                <?php if (!empty($static['desc' . $lang])) { ?>
                    <p class="static-hero-desc"><?= nl2br($static['desc' . $lang]) ?></p>
                <?php } else { ?>
                    <p class="static-hero-desc">Khỏe Pro (khoepro.com) là nền tảng nội dung chuyên sâu, đánh giá độc lập và so sánh khách quan các dụng cụ tập gym, thiết bị thể thao và giải pháp phục hồi cơ bắp.</p>
                <?php } ?>
                
                <div class="static-hero-meta">
                    <div class="meta-item">
                        <i class="fa-regular fa-calendar-check text-primary"></i>
                        <span>Cập nhật: <strong><?= date("d/m/Y", !empty($static['date_updated']) ? $static['date_updated'] : ($static['date_created'] ?? time())) ?></strong></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa-solid fa-user-check text-success"></i>
                        <span>Biên tập: <strong>Ban Biên Tập Khỏe Pro</strong></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa-solid fa-circle-check text-warning"></i>
                        <span>Tiêu chuẩn: <strong>Kiểm duyệt độc lập</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="static-main-section">
        <div class="fitnado-wrap">
            <div class="static-grid-layout">
                <!-- Left Column: Rich Editorial HTML Content from Admin -->
                <div class="static-content-column">
                    <div class="static-article-card">
                        <?php if (!empty($static['content' . $lang])) { ?>
                            <article class="static-html-body fitnado-typography">
                                <?= $func->decodeHtmlChars($static['content' . $lang]) ?>
                            </article>

                            <!-- Article Footer / Share -->
                            <div class="static-article-footer">
                                <div class="article-share-box">
                                    <span class="share-label"><i class="fa-solid fa-share-nodes"></i> Chia sẻ thông tin:</span>
                                    <div class="share-buttons">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($func->getCurrentPageURL()) ?>" target="_blank" rel="nofollow" class="btn-share-item btn-share-fb" title="Chia sẻ qua Facebook">
                                            <i class="fa-brands fa-facebook-f"></i> Facebook
                                        </a>
                                        <a href="https://zalo.me/share?url=<?= urlencode($func->getCurrentPageURL()) ?>" target="_blank" rel="nofollow" class="btn-share-item btn-share-zalo" title="Chia sẻ qua Zalo">
                                            <i class="fa-solid fa-comment"></i> Zalo
                                        </a>
                                        <button type="button" class="btn-share-item btn-share-copy" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết vào bộ nhớ tạm!');" title="Sao chép liên kết">
                                            <i class="fa-solid fa-link"></i> Sao chép link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning static-empty-alert" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <div>
                                    <strong>Nội dung đang được cập nhật.</strong>
                                    <p class="mb-0 text-sm">Vui lòng quay lại sau hoặc liên hệ ban biên tập để biết thêm chi tiết.</p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Right Column: Sidebar Widgets -->
                <aside class="static-sidebar-column">
                    <!-- Widget 1: Editorial Standards -->
                    <div class="sidebar-card-widget">
                        <div class="widget-header">
                            <i class="fa-solid fa-certificate text-primary"></i>
                            <h3 class="widget-title">Tôn Chỉ Khỏe Pro</h3>
                        </div>
                        <ul class="widget-principles-list">
                            <li>
                                <i class="fa-solid fa-check text-success"></i>
                                <div>
                                    <strong>Đánh giá độc lập 100%</strong>
                                    <p>Không nhận tiền nâng khống điểm số sản phẩm.</p>
                                </div>
                            </li>
                            <li>
                                <i class="fa-solid fa-check text-success"></i>
                                <div>
                                    <strong>Thông số kỹ thuật thực tế</strong>
                                    <p>Đo lường độ chịu tải, độ bền, chất liệu thực tế.</p>
                                </div>
                            </li>
                            <li>
                                <i class="fa-solid fa-check text-success"></i>
                                <div>
                                    <strong>An toàn chuyển động</strong>
                                    <p>Ưu tiên bảo vệ xương khớp và kỹ thuật người tập.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Widget 2: Fast Navigation Hub -->
                    <div class="sidebar-card-widget">
                        <div class="widget-header">
                            <i class="fa-solid fa-compass text-warning"></i>
                            <h3 class="widget-title">Khám Phá Khỏe Pro</h3>
                        </div>
                        <div class="widget-quick-links">
                            <a href="san-pham" class="quick-link-item">
                                <span class="quick-link-icon icon-prod"><i class="fa-solid fa-dumbbell"></i></span>
                                <div class="quick-link-text">
                                    <strong>Danh Mục Đồ Tập</strong>
                                    <span>Đai lưng, dây kháng lực, súng massage</span>
                                </div>
                                <i class="fa-solid fa-chevron-right quick-link-arrow"></i>
                            </a>
                            <a href="danh-gia" class="quick-link-item">
                                <span class="quick-link-icon icon-rev"><i class="fa-solid fa-star"></i></span>
                                <div class="quick-link-text">
                                    <strong>Bài Viết Đánh Giá</strong>
                                    <span>Review chuyên sâu ưu nhược điểm</span>
                                </div>
                                <i class="fa-solid fa-chevron-right quick-link-arrow"></i>
                            </a>
                            <a href="so-sanh" class="quick-link-item">
                                <span class="quick-link-icon icon-comp"><i class="fa-solid fa-code-compare"></i></span>
                                <div class="quick-link-text">
                                    <strong>So Sánh Đối Đầu</strong>
                                    <span>Đặt lên bàn cân các loại thiết bị gym</span>
                                </div>
                                <i class="fa-solid fa-chevron-right quick-link-arrow"></i>
                            </a>
                            <a href="huong-dan" class="quick-link-item">
                                <span class="quick-link-icon icon-guide"><i class="fa-solid fa-book-open"></i></span>
                                <div class="quick-link-text">
                                    <strong>Hướng Dẫn Mua Hàng</strong>
                                    <span>Bộ tiêu chí chọn đúng món đồ tập</span>
                                </div>
                                <i class="fa-solid fa-chevron-right quick-link-arrow"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Widget 3: Contact & Partnership CTA -->
                    <div class="sidebar-card-widget widget-cta-box">
                        <div class="widget-cta-badge"><i class="fa-solid fa-headset"></i> Hỗ Trợ Bạn Đọc</div>
                        <h4 class="widget-cta-title">Cần Tư Vấn Thiết Bị Tập Gym?</h4>
                        <p class="widget-cta-desc">Đội ngũ chuyên viên Khỏe Pro luôn sẵn sàng giải đáp thắc mắc và hỗ trợ bạn chọn đúng dụng cụ tập luyện.</p>
                        <div class="widget-cta-actions">
                            <a href="lien-he" class="btn-sidebar-cta">
                                <i class="fa-solid fa-envelope"></i> Liên Hệ Ban Biên Tập
                            </a>
                            <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline'] ?? '0867508149') ?>" class="btn-sidebar-call">
                                <i class="fa-solid fa-phone"></i> <?= $optsetting['hotline'] ?? '086 750 8149' ?>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
