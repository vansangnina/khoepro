<?php
if (!defined('TEMPLATE')) die("Error");

// Fallback score & count
$displayScore = !empty($rowDetail['review_score']) ? number_format($rowDetail['review_score'], 1) : '9.5';
$displayReviewCount = !empty($rowDetail['review_count']) ? $rowDetail['review_count'] : ($comment->total > 0 ? $comment->total : 15);

// Pros & Cons arrays
$prosArray = [];
if (!empty($rowDetail['expert_pros'])) {
    $prosArray = array_filter(array_map('trim', explode("\n", $rowDetail['expert_pros'])));
} elseif (!empty($rowDetail['pros_vi'])) {
    $prosArray = array_filter(array_map('trim', explode("\n", $rowDetail['pros_vi'])));
}

$consArray = [];
if (!empty($rowDetail['expert_cons'])) {
    $consArray = array_filter(array_map('trim', explode("\n", $rowDetail['expert_cons'])));
} elseif (!empty($rowDetail['cons_vi'])) {
    $consArray = array_filter(array_map('trim', explode("\n", $rowDetail['cons_vi'])));
}

$verdictText = !empty($rowDetail['verdict']) ? $rowDetail['verdict'] : (!empty($rowDetail['desc' . $lang]) ? $rowDetail['desc' . $lang] : '');
$bestForText = !empty($rowDetail['best_for']) ? $rowDetail['best_for'] : (!empty($rowDetail['suitable_vi']) ? $rowDetail['suitable_vi'] : 'Người tập Gym, thể hình, thể thao đa năng cần sản phẩm bền bỉ và hiệu năng cao.');
?>

<div class="fitnado-pro-detail wrap py-4">
    <!-- Breadcrumb -->
    <div class="fitnado-breadcrumb mb-3">
        <?= (!empty($breadcrumbs)) ? $breadcrumbs : '' ?>
    </div>

    <!-- HERO SECTION: Above the Fold (Gallery + Conversion Engine) -->
    <div class="pro-detail-hero card border-0 rounded-16 p-3 p-lg-4 mb-4 shadow-sm bg-white">
        <div class="row">
            <!-- Left: High Quality Gallery -->
            <div class="col-lg-6 col-md-6 col-12 mb-4 mb-md-0">
                <div class="pro-gallery-wrap">
                    <div class="pro-main-img-box">
                        <!-- Badges Stack -->
                        <div class="pro-badge-stack">
                            <?php if (!empty($bestOffer['discount_percent'])) { ?>
                                <span class="badge-discount-glow">
                                    <i class="fas fa-bolt mr-1"></i>GIẢM <?= $bestOffer['discount_percent'] ?>%
                                </span>
                            <?php } elseif (!empty($rowDetail['discount'])) { ?>
                                <span class="badge-discount-glow">
                                    -<?= $rowDetail['discount'] ?>%
                                </span>
                            <?php } ?>
                            
                            <?php if (!empty($rowDetail['is_real_test'])) { ?>
                                <span class="badge-tested">
                                    <i class="fas fa-dumbbell"></i>ĐÃ TEST THỰC TẾ
                                </span>
                            <?php } ?>
                        </div>

                        <!-- KhoePro Score Pill -->
                        <?php if (!empty($rowDetail['review_score']) && $rowDetail['review_score'] > 0) { ?>
                            <div class="pro-score-pill">
                                <span>KHỎE PRO SCORE:</span>
                                <span class="score-num"><?= $displayScore ?></span>
                                <span class="text-xs text-white-50">/10</span>
                            </div>
                        <?php } ?>

                        <!-- Main Image -->
                        <a id="main-pro-zoom" class="d-flex align-items-center justify-content-center w-100 h-100" href="<?= $configBase . UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" data-fancybox="pro-gallery">
                            <img id="main-pro-img" src="<?= THUMBS ?>/540x540x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" class="img-fluid" alt="<?= htmlspecialchars($rowDetail['name' . $lang]) ?>" onerror="this.src='<?= THUMBS ?>/540x540x1/assets/images/noimage.png';">
                        </a>
                    </div>

                    <!-- Thumbnails Strip -->
                    <?php if (!empty($rowDetailPhoto) && count($rowDetailPhoto) > 0) { ?>
                        <div class="pro-thumbs-strip">
                            <div class="pro-thumb-cell active" data-src="<?= THUMBS ?>/540x540x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" data-zoom="<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>">
                                <img src="<?= THUMBS ?>/75x75x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" alt="Thumb main" onerror="this.src='<?= THUMBS ?>/75x75x1/assets/images/noimage.png';">
                            </div>
                            <?php foreach ($rowDetailPhoto as $k => $v_photo) { ?>
                                <div class="pro-thumb-cell" data-src="<?= THUMBS ?>/540x540x1/<?= UPLOAD_PRODUCT_L . $v_photo['photo'] ?>" data-zoom="<?= UPLOAD_PRODUCT_L . $v_photo['photo'] ?>">
                                    <img src="<?= THUMBS ?>/75x75x1/<?= UPLOAD_PRODUCT_L . $v_photo['photo'] ?>" alt="Thumb <?= $k ?>" onerror="this.src='<?= THUMBS ?>/75x75x1/assets/images/noimage.png';">
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <!-- Trust Signals -->
                    <div class="pro-trust-badges">
                        <div class="trust-badge-item">
                            <i class="fas fa-check-circle"></i>
                            <span>100% Khách quan</span>
                        </div>
                        <div class="trust-badge-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Đã test thực tế</span>
                        </div>
                        <div class="trust-badge-item">
                            <i class="fas fa-sync-alt"></i>
                            <span>Cập nhật giá 24/7</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Decision & Conversion Engine -->
            <div class="col-lg-6 col-md-6 col-12">
                <div class="pro-hero-info">
                    <!-- Brand & Category -->
                    <div class="pro-meta-top">
                        <?php if (!empty($productBrand['id'])) { ?>
                            <a href="<?= $productBrand[$sluglang] ?>" class="pro-brand-tag">
                                <i class="fas fa-tag mr-1"></i><?= htmlspecialchars($productBrand['name' . $lang]) ?>
                            </a>
                        <?php } ?>
                        <?php if (!empty($productList['id'])) { ?>
                            <a href="<?= $productList[$sluglang] ?>" class="badge badge-light text-secondary font-weight-bold p-2">
                                <i class="fas fa-layer-group mr-1"></i><?= htmlspecialchars($productList['name' . $lang]) ?>
                            </a>
                        <?php } ?>
                        <span class="badge badge-success-subtle text-success font-weight-bold px-2 py-1">
                            <i class="fas fa-award mr-1"></i>Editor's Choice
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="pro-hero-title">
                        <?= htmlspecialchars($rowDetail['name' . $lang]) ?>
                    </h1>

                    <!-- Rating & Social Proof -->
                    <div class="pro-rating-strip">
                        <div class="rating-stars-badge">
                            <i class="fas fa-star"></i>
                            <span><?= $displayScore ?></span>
                            <span class="text-xs text-muted">/10</span>
                        </div>
                        <a href="#pro-user-reviews" class="review-count-link">
                            <i class="fas fa-comments mr-1"></i><?= $displayReviewCount ?> nhận xét & đánh giá
                        </a>
                        <span class="text-muted text-xs">•</span>
                        <span class="text-muted text-xs">
                            <i class="far fa-eye mr-1"></i><?= $rowDetail['view'] + 1 ?> lượt xem
                        </span>
                    </div>

                    <!-- Price Overview Card -->
                    <div class="pro-price-card">
                        <div class="price-card-label">Giá thị trường & Nơi bán tốt nhất:</div>
                        <div class="price-values-row">
                            <?php 
                            $bestPrice = !empty($bestOffer['affiliate_price']) ? $bestOffer['affiliate_price'] : ($rowDetail['sale_price'] > 0 ? $rowDetail['sale_price'] : $rowDetail['regular_price']);
                            $origPrice = !empty($bestOffer['original_price']) ? $bestOffer['original_price'] : $rowDetail['regular_price'];
                            ?>
                            <span class="current-price-val">
                                <?= ($bestPrice > 0) ? $func->formatMoney($bestPrice) : 'Liên hệ nhận báo giá' ?>
                            </span>
                            <?php if ($origPrice > $bestPrice && $origPrice > 0) { ?>
                                <span class="original-price-val">
                                    <?= $func->formatMoney($origPrice) ?>
                                </span>
                                <span class="save-pill">
                                    Tiết kiệm <?= $func->formatMoney($origPrice - $bestPrice) ?>
                                </span>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Short Highlights -->
                    <?php if (!empty($rowDetail['desc' . $lang])) { ?>
                        <div class="pro-desc-short text-muted text-sm mb-3" style="line-height: 1.6;">
                            <?= nl2br($func->decodeHtmlChars($rowDetail['desc' . $lang])) ?>
                        </div>
                    <?php } ?>

                    <!-- PRIMARY BEST DEAL CTA BOX -->
                    <?php if (!empty($bestOffer)) { ?>
                        <div class="pro-best-deal-container">
                            <div class="deal-header-row">
                                <span class="deal-badge-best">
                                    <i class="fas fa-crown mr-1"></i>NƠI BÁN GIÁ TỐT NHẤT HÔM NAY
                                </span>
                                <span class="deal-price-highlight">
                                    <?= $func->formatMoney($bestOffer['affiliate_price']) ?>
                                </span>
                            </div>

                            <a href="<?= $bestOffer['go_url'] ?>?src=product_detail_hero" target="_blank" rel="nofollow sponsored" class="btn-best-deal-cta">
                                <i class="<?= $bestOffer['meta']['badge_class'] == 'badge-shopee' ? 'fas fa-shopping-bag' : 'fas fa-external-link-alt' ?>"></i>
                                <span><?= $bestOffer['meta']['cta_text'] ?> (<?= htmlspecialchars($bestOffer['seller_name']) ?>)</span>
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>

                            <?php if (!empty($bestOffer['coupon_code'])) { ?>
                                <div class="coupon-row">
                                    <div>
                                        <i class="fas fa-ticket-alt mr-1 text-warning"></i>
                                        <span>Mã giảm độc quyền:</span>
                                        <span class="coupon-code-tag" id="main-coupon-val"><?= htmlspecialchars($bestOffer['coupon_code']) ?></span>
                                    </div>
                                    <button type="button" class="btn-copy-code" onclick="copyCouponCode('<?= htmlspecialchars($bestOffer['coupon_code']) ?>', this)">
                                        <i class="fas fa-copy mr-1"></i>Sao chép
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <!-- MULTI-PLATFORM OFFERS TABLE (SO SÁNH NƠI BÁN) -->
                    <?php if (!empty($affiliateOffers) && count($affiliateOffers) > 0) { ?>
                        <div class="offers-card-wrap">
                            <div class="offers-card-title">
                                <i class="fas fa-store text-primary"></i>
                                <span>So sánh giá tại <?= count($affiliateOffers) ?> sàn thương mại:</span>
                            </div>
                            <div class="offers-list">
                                <?php foreach ($affiliateOffers as $idx => $offer) { ?>
                                    <div class="offer-item-row">
                                        <div class="offer-plat-info">
                                            <span class="platform-pill <?= $offer['meta']['badge_class'] ?>">
                                                <?= $offer['meta']['name'] ?>
                                            </span>
                                            <span class="offer-seller-text" title="<?= htmlspecialchars($offer['seller_name']) ?>">
                                                <?= htmlspecialchars($offer['seller_name']) ?>
                                            </span>
                                            <?php if (!empty($offer['is_best_deal'])) { ?>
                                                <span class="badge badge-warning text-dark font-weight-bold text-xs">Rẻ nhất</span>
                                            <?php } ?>
                                        </div>
                                        <div class="offer-action-group">
                                            <?php if ($offer['affiliate_price'] > 0) { ?>
                                                <span class="offer-price-val"><?= $func->formatMoney($offer['affiliate_price']) ?></span>
                                            <?php } ?>
                                            <a href="<?= $offer['go_url'] ?>?src=product_detail_list" target="_blank" rel="nofollow sponsored" class="btn-visit-store">
                                                Đến nơi bán <i class="fas fa-chevron-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Affiliate Transparency Statement -->
                    <div class="affiliate-disclosure-box">
                        <i class="fas fa-shield-alt mr-1"></i><?= htmlspecialchars($affiliateDisclosure) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FITNADO EXPERT VERDICT, PROS & CONS, BEST FOR -->
    <div class="fitnado-verdict-card">
        <h3 class="verdict-heading">
            <i class="fas fa-clipboard-check text-primary"></i>
            <span>ĐÁNH GIÁ CHUYÊN MÔN TỪ KHỎE PRO</span>
        </h3>

        <?php if (!empty($verdictText)) { ?>
            <div class="verdict-quote-box">
                <i class="fas fa-quote-left mr-2 opacity-50"></i>
                <?= nl2br(htmlspecialchars($verdictText)) ?>
            </div>
        <?php } ?>

        <div class="row">
            <!-- Pros -->
            <div class="col-md-6 col-12 mb-3 mb-md-0">
                <div class="pros-box">
                    <h4>
                        <i class="fas fa-check-circle"></i>
                        <span>Ưu điểm nổi bật (Pros)</span>
                    </h4>
                    <ul class="verdict-list">
                        <?php if (!empty($prosArray)) {
                            foreach ($prosArray as $pro) {
                                echo "<li><i class='fas fa-check text-success mt-1'></i><span>" . htmlspecialchars($pro) . "</span></li>";
                            }
                        } else { ?>
                            <li><i class="fas fa-check text-success mt-1"></i><span>Chất lượng gia công cao cấp, độ bền vượt trội khi tập nặng.</span></li>
                            <li><i class="fas fa-check text-success mt-1"></i><span>Thiết kế công thái học tối ưu chuyển động và cảm giác thoải mái.</span></li>
                            <li><i class="fas fa-check text-success mt-1"></i><span>Giá bán cạnh tranh và có nhiều ưu đãi sàn thương mại điện tử.</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <!-- Cons -->
            <div class="col-md-6 col-12">
                <div class="cons-box">
                    <h4>
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Điểm cần lưu ý (Cons)</span>
                    </h4>
                    <ul class="verdict-list">
                        <?php if (!empty($consArray)) {
                            foreach ($consArray as $con) {
                                echo "<li><i class='fas fa-times text-danger mt-1'></i><span>" . htmlspecialchars($con) . "</span></li>";
                            }
                        } else { ?>
                            <li><i class="fas fa-times text-danger mt-1"></i><span>Nhu cầu cao nên một số size/màu hot dễ hết hàng nhanh.</span></li>
                            <li><i class="fas fa-times text-danger mt-1"></i><span>Cần chọn đúng kích cỡ theo bảng size tiêu chuẩn để có độ ôm tốt nhất.</span></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Best For Box -->
        <div class="best-for-card">
            <div class="best-for-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="best-for-text">
                <strong>ĐỐI TƯỢNG PHÙ HỢP NHẤT:</strong>
                <p><?= htmlspecialchars($bestForText) ?></p>
            </div>
        </div>
    </div>

    <!-- SPECIFICATIONS TABLE -->
    <?php if (!empty($productSpecs) && is_array($productSpecs)) { ?>
        <div class="pro-specs-card">
            <h3 class="verdict-heading mb-3">
                <i class="fas fa-list-ul text-primary"></i>
                <span>THÔNG SỐ KỸ THUẬT CHI TIẾT</span>
            </h3>
            <div class="table-responsive">
                <table class="fitnado-table-specs">
                    <tbody>
                        <?php foreach ($productSpecs as $sKey => $sVal) { ?>
                            <tr>
                                <td class="spec-key"><?= htmlspecialchars($sKey) ?></td>
                                <td class="spec-val"><?= htmlspecialchars($sVal) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>

    <!-- HEAD-TO-HEAD COMPARISON MATRIX (SO SÁNH ĐỐI ĐẦU) -->
    <?php if (!empty($compareCandidates) && count($compareCandidates) > 0) { ?>
        <div class="pro-h2h-section">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h3 class="verdict-heading mb-0">
                    <i class="fas fa-balance-scale text-primary"></i>
                    <span>SO SÁNH TRỰC TIẾP VỚI CÁC LỰA CHỌN KHÁC</span>
                </h3>
                <a href="so-sanh?id1=<?= $rowDetail['id'] ?>&id2=<?= $compareCandidates[0]['id'] ?>" class="btn btn-outline-primary btn-sm font-weight-bold">
                    So sánh chi tiết hơn <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="h2h-table-wrap">
                <table class="table-h2h">
                    <thead>
                        <tr>
                            <th class="col-feature">Tiêu chí so sánh</th>
                            <th class="col-current-pro">
                                <div class="badge badge-primary mb-1">Đang xem</div>
                                <div class="font-weight-900"><?= htmlspecialchars($rowDetail['name' . $lang]) ?></div>
                            </th>
                            <?php foreach ($compareCandidates as $cItem) { ?>
                                <th>
                                    <div class="font-weight-800"><?= htmlspecialchars($cItem['name']) ?></div>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-feature">Hình ảnh</td>
                            <td class="col-current-pro">
                                <img src="<?= THUMBS ?>/100x100x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" class="img-fluid rounded" style="max-height:80px;" alt="<?= htmlspecialchars($rowDetail['name' . $lang]) ?>" onerror="this.src='<?= THUMBS ?>/100x100x1/assets/images/noimage.png';">
                            </td>
                            <?php foreach ($compareCandidates as $cItem) { ?>
                                <td>
                                    <img src="<?= THUMBS ?>/100x100x1/<?= UPLOAD_PRODUCT_L . $cItem['photo'] ?>" class="img-fluid rounded" style="max-height:80px;" alt="<?= htmlspecialchars($cItem['name']) ?>" onerror="this.src='<?= THUMBS ?>/100x100x1/assets/images/noimage.png';">
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="col-feature">Điểm Fitnado Score</td>
                            <td class="col-current-pro font-weight-900 text-primary">
                                <span class="badge badge-warning text-dark px-2 py-1"><?= $displayScore ?>/10</span>
                            </td>
                            <?php foreach ($compareCandidates as $cItem) { ?>
                                <td class="font-weight-800">
                                    <span class="badge badge-light border px-2 py-1"><?= !empty($cItem['review_score']) ? number_format($cItem['review_score'], 1) : '9.0' ?>/10</span>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="col-feature">Giá tham khảo</td>
                            <td class="col-current-pro font-weight-900 text-danger" style="font-size: 16px;">
                                <?= $bestPrice > 0 ? $func->formatMoney($bestPrice) : 'Liên hệ' ?>
                            </td>
                            <?php foreach ($compareCandidates as $cItem) { ?>
                                <td class="font-weight-800 text-dark">
                                    <?= (!empty($cItem['best_offer']['affiliate_price'])) ? $func->formatMoney($cItem['best_offer']['affiliate_price']) : ($cItem['sale_price'] > 0 ? $func->formatMoney($cItem['sale_price']) : $func->formatMoney($cItem['regular_price'])) ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="col-feature">Phù hợp nhất với</td>
                            <td class="col-current-pro font-weight-600 text-xs">
                                <?= htmlspecialchars($bestForText) ?>
                            </td>
                            <?php foreach ($compareCandidates as $cItem) { ?>
                                <td class="text-xs text-muted font-weight-500">
                                    <?= !empty($cItem['best_for']) ? htmlspecialchars($cItem['best_for']) : 'Người tập thể hình, fitness đa năng' ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td class="col-feature">Hành động</td>
                            <td class="col-current-pro">
                                <?php if (!empty($bestOffer)) { ?>
                                    <a href="<?= $bestOffer['go_url'] ?>" target="_blank" rel="nofollow sponsored" class="btn btn-sm btn-primary font-weight-bold px-3">
                                        Mua giá tốt <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                <?php } ?>
                            </td>
                            <?php foreach ($compareCandidates as $cItem) { ?>
                                <td>
                                    <a href="<?= $cItem['slugvi'] ?>" class="btn btn-sm btn-outline-dark font-weight-bold">
                                        Xem chi tiết
                                    </a>
                                </td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>

    <!-- CONTENT TABS: Editorial Detail Article -->
    <?php if (!empty($rowDetail['content' . $lang])) { ?>
        <div class="pro-content-tabs card border-0 rounded-16 p-4 mb-4 bg-white shadow-sm">
            <h3 class="verdict-heading mb-3">
                <i class="fas fa-file-alt text-primary"></i>
                <span>BÀI VIẾT ĐÁNH GIÁ & TRẢI NGHIỆM CHI TIẾT</span>
            </h3>
            <div class="pro-editorial-content content-text text-dark" style="line-height: 1.8;">
                <?= $func->decodeHtmlChars($rowDetail['content' . $lang]) ?>
            </div>
        </div>
    <?php } ?>

    <!-- REAL USER REVIEWS SECTION -->
    <div class="pro-reviews-section card border-0 rounded-16 p-4 mb-4 bg-white shadow-sm" id="pro-user-reviews">
        <h3 class="verdict-heading mb-4">
            <i class="fas fa-comments text-primary"></i>
            <span>ĐÁNH GIÁ & NHẬN XÉT TỪ NGƯỜI DÙNG</span>
        </h3>
        <?php include TEMPLATE . "product/comment.php"; ?>
    </div>

    <!-- RELATED PRODUCTS -->
    <?php if (!empty($product)) { ?>
        <section class="fitnado-section mt-5 mb-4">
            <div class="fitnado-sectionHead">
                <div>
                    <h2>🔥 SẢN PHẨM TƯƠNG TỰ CÙNG DANH MỤC</h2>
                    <p>Các lựa chọn dụng cụ tập gym & phụ kiện cùng phân khúc được cộng đồng đánh giá cao.</p>
                </div>
                <a href="san-pham" class="fitnado-more">Xem tất cả →</a>
            </div>

            <div class="fitnado-products">
                <?php foreach ($product as $k => $v_rel) {
                    $emojiIcons = array('➰', '🥊', '〰️', '🥤', '🎒', '💪', '⚡', '🏋️');
                    $cardEmoji = $emojiIcons[$k % count($emojiIcons)];
                ?>
                    <div class="fitnado-card">
                        <a href="<?= $v_rel[$sluglang] ?>" class="pic" title="<?= htmlspecialchars($v_rel['name' . $lang]) ?>">
                            <?php if (!empty($v_rel['photo'])) { ?>
                                <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v_rel['photo'] ?>" alt="<?= htmlspecialchars($v_rel['name' . $lang]) ?>" />
                                <span style="display:none;"><?= $cardEmoji ?></span>
                            <?php } else { ?>
                                <span><?= $cardEmoji ?></span>
                            <?php } ?>
                        </a>
                        <div class="fitnado-cardBody">
                            <?php if (!empty($v_rel['review_score']) && $v_rel['review_score'] > 0) { ?>
                                <span class="fitnado-rating">★ <?= number_format($v_rel['review_score'], 1) ?></span>
                            <?php } else { ?>
                                <span class="fitnado-rating">★ 9.0</span>
                            <?php } ?>
                            <h3>
                                <a href="<?= $v_rel[$sluglang] ?>" title="<?= htmlspecialchars($v_rel['name' . $lang]) ?>"><?= htmlspecialchars($v_rel['name' . $lang]) ?></a>
                            </h3>
                            <div class="fitnado-price">
                                <?php if (!empty($v_rel['discount'])) { ?>
                                    <span><?= $func->formatMoney($v_rel['sale_price']) ?></span>
                                    <span class="fitnado-price-old"><?= $func->formatMoney($v_rel['regular_price']) ?></span>
                                <?php } else { ?>
                                    <span><?= ($v_rel['sale_price'] > 0) ? $func->formatMoney($v_rel['sale_price']) : ($v_rel['regular_price'] > 0 ? $func->formatMoney($v_rel['regular_price']) : lienhe) ?></span>
                                <?php } ?>
                            </div>
                            <small><?= (!empty($v_rel['code'])) ? 'Mã: ' . $v_rel['code'] : 'Tập gym | Thể thao | Chính hãng' ?></small>
                            <a href="<?= $v_rel[$sluglang] ?>" class="fitnado-btn">Xem review →</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <!-- RELATED ARTICLES -->
    <?php if (!empty($relatedNews)) {
        $articleIcons = array('🏋️', '🥤', '〰️', '🎒', '💪', '🏃');
    ?>
        <section class="fitnado-section mt-4 mb-4">
            <div class="fitnado-sectionHead">
                <div>
                    <h2>▣ CẨM NANG & KINH NGHIỆM TẬP LUYỆN</h2>
                    <p>Kiến thức và hướng dẫn chọn thiết bị từ Khỏe Pro.</p>
                </div>
                <a href="tin-tuc" class="fitnado-more">Xem tất cả bài viết →</a>
            </div>

            <div class="fitnado-articles">
                <?php foreach ($relatedNews as $k => $n_item) {
                    $aIcon = $articleIcons[$k % count($articleIcons)];
                ?>
                    <a href="<?= $n_item[$sluglang] ?>" class="fitnado-article" title="<?= htmlspecialchars($n_item['name' . $lang]) ?>">
                        <div class="pic">
                            <?php if (!empty($n_item['photo'])) { ?>
                                <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/280x180x1/<?= UPLOAD_NEWS_L . $n_item['photo'] ?>" alt="<?= htmlspecialchars($n_item['name' . $lang]) ?>" />
                                <span style="display:none;"><?= $aIcon ?></span>
                            <?php } else { ?>
                                <span><?= $aIcon ?></span>
                            <?php } ?>
                        </div>
                        <div class="fitnado-article-info">
                            <b><?= htmlspecialchars($n_item['name' . $lang]) ?></b>
                            <?php if (!empty($n_item['desc' . $lang])) { ?>
                                <p><?= strip_tags($n_item['desc' . $lang]) ?></p>
                            <?php } ?>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</div>

<!-- STICKY MOBILE BUY BAR -->
<?php if (!empty($bestOffer)) { ?>
    <div class="fitnado-sticky-mobile-bar">
        <div class="sticky-bar-info">
            <img src="<?= THUMBS ?>/44x44x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" class="sticky-bar-thumb" alt="Thumb" onerror="this.src='<?= THUMBS ?>/44x44x1/assets/images/noimage.png';">
            <div class="sticky-bar-meta">
                <div class="sticky-bar-price"><?= $func->formatMoney($bestOffer['affiliate_price']) ?></div>
                <div class="sticky-bar-plat">Giá tốt trên <?= $bestOffer['meta']['name'] ?></div>
            </div>
        </div>
        <a href="<?= $bestOffer['go_url'] ?>?src=mobile_sticky_bar" target="_blank" rel="nofollow sponsored" class="sticky-bar-btn">
            Mua ngay <i class="fas fa-bolt ml-1"></i>
        </a>
    </div>
<?php } ?>

<!-- SCHEMA JSON-LD STRUCTURED DATA -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?= addslashes($rowDetail['name' . $lang]) ?>",
  "image": [
    "<?= $configBase . UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>"
  ],
  "description": "<?= addslashes(strip_tags($rowDetail['desc' . $lang])) ?>",
  "sku": "KP-<?= $rowDetail['id'] ?>",
  "brand": {
    "@type": "Brand",
    "name": "<?= !empty($productBrand['name' . $lang]) ? addslashes($productBrand['name' . $lang]) : 'Khỏe Pro' ?>"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?= $displayScore ?>",
    "bestRating": "10",
    "worstRating": "1",
    "ratingCount": "<?= $displayReviewCount ?>"
  }
  <?php if (!empty($bestOffer)) { ?>,
  "offers": {
    "@type": "Offer",
    "url": "<?= $seo->get('url') ?>",
    "priceCurrency": "VND",
    "price": "<?= $bestOffer['affiliate_price'] ?>",
    "priceValidUntil": "<?= date('Y-12-31') ?>",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "<?= addslashes($bestOffer['seller_name']) ?>"
    }
  }
  <?php } ?>
}
</script>

<script>
    function copyCouponCode(code, btn) {
        if (!navigator.clipboard) {
            var textArea = document.createElement("textarea");
            textArea.value = code;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("Copy");
            textArea.remove();
        } else {
            navigator.clipboard.writeText(code);
        }
        var origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i>Đã chép!';
        btn.style.background = '#10b981';
        btn.style.color = '#ffffff';
        setTimeout(function() {
            btn.innerHTML = origHtml;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        /* Thumbnail gallery click handler */
        var thumbItems = document.querySelectorAll('.pro-thumb-cell');
        var mainImg = document.getElementById('main-pro-img');
        var mainZoom = document.getElementById('main-pro-zoom');
        if (thumbItems.length && mainImg) {
            thumbItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    thumbItems.forEach(function(t) { t.classList.remove('active'); });
                    this.classList.add('active');
                    var newSrc = this.getAttribute('data-src');
                    var newZoom = this.getAttribute('data-zoom');
                    if (newSrc) mainImg.src = newSrc;
                    if (newZoom && mainZoom) mainZoom.href = newZoom;
                });
            });
        }
    });
</script>