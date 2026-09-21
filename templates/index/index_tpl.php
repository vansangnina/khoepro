<main class="fitnado-wrap">
    <!-- Category Quick Grid -->
    <?php if (!empty($productListMenu)) {
        $iconList = array('🏋️', '〰️', '🥤', '🎒', '🏠', '⌚', '🧘', '👕');
    ?>
        <div class="fitnado-cats">
            <?php foreach ($productListMenu as $k => $v) {
                $icon = isset($iconList[$k % count($iconList)]) ? $iconList[$k % count($iconList)] : '🏋️';
            ?>
                <a href="<?= $v[$sluglang] ?>" class="fitnado-cat" title="<?= $v['name' . $lang] ?>">
                    <span class="ico">
                        <?php if (!empty($v['photo'])) { ?>
                            <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';" data-src="<?= THUMBS ?>/50x50x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                            <span style="display:none;"><?= $icon ?></span>
                        <?php } else { ?>
                            <span><?= $icon ?></span>
                        <?php } ?>
                    </span>
                    <?= $v['name' . $lang] ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- Featured Products -->
    <?php if (!empty($productHot)) { ?>
        <section class="fitnado-section">
            <div class="fitnado-sectionHead">
                <div>
                    <h2>🔥 SẢN PHẨM ĐANG ĐƯỢC QUAN TÂM</h2>
                    <p>Những sản phẩm được cộng đồng gymmer quan tâm và đánh giá cao.</p>
                </div>
                <a href="san-pham" class="fitnado-more">Xem tất cả →</a>
            </div>

            <div class="fitnado-products">
                <?php foreach ($productHot as $k => $v) {
                    if ($k >= 10) break;
                    $emojiIcons = array('➰', '🥊', '〰️', '🥤', '🎒', '💪', '⚡', '🏋️');
                    $cardEmoji = $emojiIcons[$k % count($emojiIcons)];
                ?>
                    <div class="fitnado-card">
                        <a href="<?= $v[$sluglang] ?>" class="pic" title="<?= $v['name' . $lang] ?>">
                            <?php if (!empty($v['photo'])) { ?>
                                <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                                <span style="display:none;"><?= $cardEmoji ?></span>
                            <?php } else { ?>
                                <span><?= $cardEmoji ?></span>
                            <?php } ?>
                        </a>
                        <div class="fitnado-cardBody">
                            <?php if (!empty($v['review_score']) && $v['review_score'] > 0) { ?>
                                <span class="fitnado-rating">★ <?= number_format($v['review_score'], 1) ?></span>
                            <?php } ?>
                            <h3>
                                <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                            </h3>
                            <div class="fitnado-price">
                                <?php if (!empty($v['discount'])) { ?>
                                    <span><?= $func->formatMoney($v['sale_price']) ?></span>
                                    <span class="fitnado-price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                                <?php } else { ?>
                                    <span><?= (!empty($v['regular_price'])) ? $func->formatMoney($v['regular_price']) : lienhe ?></span>
                                <?php } ?>
                            </div>
                            <small><?= (!empty($v['code'])) ? 'Mã: ' . $v['code'] : 'Tập gym | Thể thao | Chính hãng' ?></small>
                            <a href="<?= $v[$sluglang] ?>" class="fitnado-btn">Xem review →</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <!-- 30s Review Videos -->
    <?php if (!empty($videoHot)) { ?>
        <section class="fitnado-section">
            <div class="fitnado-sectionHead">
                <div>
                    <h2>▶ 30 GIÂY REVIEW</h2>
                    <p>Video ngắn, trực quan về sản phẩm gym.</p>
                </div>
                <a href="video" class="fitnado-more">Xem thêm video →</a>
            </div>

            <div class="fitnado-videos">
                <?php foreach ($videoHot as $k => $v) {
                    if ($k >= 6) break;
                    $youtubeId = !empty($v['link_video']) ? $func->getYoutube($v['link_video']) : '';
                    $videoTitle = !empty($v['name' . $lang]) ? $v['name' . $lang] : 'Video Review Sản Phẩm';
                ?>
                    <a class="fitnado-video" data-fancybox="video-gallery" href="<?= $v['link_video'] ?>" title="<?= $videoTitle ?>">
                        <div class="pic">
                            <?php if (!empty($youtubeId)) { ?>
                                <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="https://img.youtube.com/vi/<?= $youtubeId ?>/hqdefault.jpg" alt="<?= $videoTitle ?>" />
                                <span style="display:none;">▶</span>
                            <?php } elseif (!empty($v['photo'])) { ?>
                                <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/320x220x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $videoTitle ?>" />
                                <span style="display:none;">▶</span>
                            <?php } else { ?>
                                <span>▶</span>
                            <?php } ?>
                            <div class="fitnado-play-badge">▶</div>
                        </div>
                        <b><?= $videoTitle ?></b>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <!-- Training Goals -->
    <section class="fitnado-section">
        <div class="fitnado-sectionHead">
            <div>
                <h2>🎯 TÌM THEO MỤC TIÊU</h2>
                <p>Chọn mục tiêu, chúng tôi gợi ý sản phẩm phù hợp.</p>
            </div>
        </div>

        <div class="fitnado-goals">
            <?php
            $defaultGoals = array(
                array('title' => 'TĂNG CƠ', 'link' => 'san-pham'),
                array('title' => 'GIẢM MỠ', 'link' => 'san-pham'),
                array('title' => 'TẬP TẠI NHÀ', 'link' => 'san-pham'),
                array('title' => 'NGƯỜI MỚI', 'link' => 'san-pham'),
                array('title' => 'POWERLIFTING', 'link' => 'san-pham'),
                array('title' => 'CHẠY BỘ', 'link' => 'san-pham')
            );

            if (!empty($tagsProduct) && count($tagsProduct) >= 4) {
                foreach ($tagsProduct as $kg => $vg) {
                    if ($kg >= 6) break; ?>
                    <a href="<?= $vg[$sluglang] ?>" class="fitnado-goal" title="<?= $vg['name' . $lang] ?>">
                        <?= mb_strtoupper($vg['name' . $lang], 'UTF-8') ?>
                    </a>
                <?php }
            } elseif (!empty($productListMenu) && count($productListMenu) >= 4) {
                foreach ($productListMenu as $kg => $vg) {
                    if ($kg >= 6) break; ?>
                    <a href="<?= $vg[$sluglang] ?>" class="fitnado-goal" title="<?= $vg['name' . $lang] ?>">
                        <?= mb_strtoupper($vg['name' . $lang], 'UTF-8') ?>
                    </a>
                <?php }
            } else {
                foreach ($defaultGoals as $goal) { ?>
                    <a href="<?= $goal['link'] ?>" class="fitnado-goal" title="<?= $goal['title'] ?>">
                        <?= $goal['title'] ?>
                    </a>
                <?php }
            } ?>
        </div>
    </section>

    <!-- Product Comparison -->
    <?php if (!empty($productHot) && count($productHot) >= 2) {
        $p1 = $productHot[0];
        $p2 = $productHot[1];
    ?>
        <section class="fitnado-section">
            <div class="fitnado-sectionHead">
                <div>
                    <h2>⚖ SO SÁNH SẢN PHẨM</h2>
                    <p>Đặt lên bàn cân để thấy sự khác biệt.</p>
                </div>
                <a href="san-pham" class="fitnado-more">Xem tất cả so sánh →</a>
            </div>

            <div class="fitnado-compare">
                <div class="fitnado-compare-side">
                    <h3><?= $p1['name' . $lang] ?></h3>
                    <div class="price">
                        <?= (!empty($p1['sale_price'])) ? $func->formatMoney($p1['sale_price']) : ((!empty($p1['regular_price'])) ? $func->formatMoney($p1['regular_price']) : 'Liên hệ') ?>
                    </div>
                    <div class="pic">
                        <?php if (!empty($p1['photo'])) { ?>
                            <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/140x140x2/<?= UPLOAD_PRODUCT_L . $p1['photo'] ?>" alt="<?= $p1['name' . $lang] ?>" />
                            <span style="display:none;">➰</span>
                        <?php } else { ?>
                            <span>➰</span>
                        <?php } ?>
                    </div>
                </div>

                <div class="fitnado-compare-mid">
                    <div class="fitnado-compare-row">
                        <b><?= (!empty($p1['code'])) ? $p1['code'] : 'Sản phẩm 1' ?></b>
                        <b>Tiêu chí</b>
                        <b><?= (!empty($p2['code'])) ? $p2['code'] : 'Sản phẩm 2' ?></b>
                    </div>
                    <div class="fitnado-compare-row">
                        <span><?= (!empty($p1['sale_price'])) ? $func->formatMoney($p1['sale_price']) : ((!empty($p1['regular_price'])) ? $func->formatMoney($p1['regular_price']) : 'Liên hệ') ?></span>
                        <b>Giá bán</b>
                        <span><?= (!empty($p2['sale_price'])) ? $func->formatMoney($p2['sale_price']) : ((!empty($p2['regular_price'])) ? $func->formatMoney($p2['regular_price']) : 'Liên hệ') ?></span>
                    </div>
                    <div class="fitnado-compare-row">
                        <span><?= (!empty($p1['review_type'])) ? $p1['review_type'] : 'Chính hãng' ?></span>
                        <b>Kiểm định</b>
                        <span><?= (!empty($p2['review_type'])) ? $p2['review_type'] : 'Chính hãng' ?></span>
                    </div>
                    <div class="fitnado-compare-row">
                        <span><?= (!empty($p1['review_score']) && $p1['review_score'] > 0) ? $p1['review_score'] . '/10' : 'Đang cập nhật' ?></span>
                        <b>Điểm đánh giá</b>
                        <span><?= (!empty($p2['review_score']) && $p2['review_score'] > 0) ? $p2['review_score'] . '/10' : 'Đang cập nhật' ?></span>
                    </div>
                    <div class="fitnado-compare-row">
                        <span><a href="<?= $p1[$sluglang] ?>" style="color:var(--primary); font-weight:600;">Xem chi tiết →</a></span>
                        <b>Chi tiết</b>
                        <span><a href="<?= $p2[$sluglang] ?>" style="color:var(--primary); font-weight:600;">Xem chi tiết →</a></span>
                    </div>
                </div>

                <div class="fitnado-compare-side">
                    <h3><?= $p2['name' . $lang] ?></h3>
                    <div class="price">
                        <?= (!empty($p2['sale_price'])) ? $func->formatMoney($p2['sale_price']) : ((!empty($p2['regular_price'])) ? $func->formatMoney($p2['regular_price']) : 'Liên hệ') ?>
                    </div>
                    <div class="pic">
                        <?php if (!empty($p2['photo'])) { ?>
                            <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/140x140x2/<?= UPLOAD_PRODUCT_L . $p2['photo'] ?>" alt="<?= $p2['name' . $lang] ?>" />
                            <span style="display:none;">➰</span>
                        <?php } else { ?>
                            <span>➰</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>

    <!-- Knowledge & Guide (News) -->
    <?php if (!empty($newsHot)) {
        $articleIcons = array('🏋️', '🥤', '〰️', '🎒', '💪', '🏃');
    ?>
        <section class="fitnado-section">
            <div class="fitnado-sectionHead">
                <div>
                    <h2>▣ KIẾN THỨC & HƯỚNG DẪN</h2>
                    <p>Giúp bạn hiểu rõ hơn, tập luyện hiệu quả hơn.</p>
                </div>
                <a href="tin-tuc" class="fitnado-more">Xem tất cả bài viết →</a>
            </div>

            <div class="fitnado-articles">
                <?php foreach ($newsHot as $k => $v) {
                    if ($k >= 5) break;
                    $aIcon = $articleIcons[$k % count($articleIcons)];
                ?>
                    <a href="<?= $v[$sluglang] ?>" class="fitnado-article" title="<?= $v['name' . $lang] ?>">
                        <div class="pic">
                            <?php if (!empty($v['photo'])) { ?>
                                <img class="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" data-src="<?= THUMBS ?>/280x180x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                                <span style="display:none;"><?= $aIcon ?></span>
                            <?php } else { ?>
                                <span><?= $aIcon ?></span>
                            <?php } ?>
                        </div>
                        <div class="fitnado-article-info">
                            <b><?= $v['name' . $lang] ?></b>
                            <?php if (!empty($v['desc' . $lang])) { ?>
                                <p><?= strip_tags($v['desc' . $lang]) ?></p>
                            <?php } ?>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <!-- Newsletter -->
    <div class="fitnado-newsletter">
        <div>
            <h2>Nhận kiến thức & ưu đãi mới nhất</h2>
            <p>Đăng ký email để không bỏ lỡ bài review và hướng dẫn tập luyện bổ ích.</p>
        </div>
        <form class="fitnado-newsletter-form form-newsletter" method="post" action="">
            <input type="email" name="dataNewsletter[email]" placeholder="Nhập email của bạn..." required />
            <input type="hidden" name="dataNewsletter[type]" value="dangkynhantin" />
            <input type="hidden" name="dataNewsletter[date_created]" value="<?= time() ?>" />
            <button type="submit" name="submit-newsletter" value="1" class="fitnado-btn">Đăng ký</button>
        </form>
    </div>
</main>