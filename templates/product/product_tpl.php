<main class="fitnado-wrap py-4">
    <!-- Category & Brand Filters -->
    <?php if (!empty($productListMenu)) {
        $iconList = array('🏋️', '〰️', '🥤', '🎒', '🏠', '⌚', '🧘', '👕');
    ?>
        <div class="fitnado-cats mb-4">
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

    <!-- Product Listing Section -->
    <section class="fitnado-section">
        <div class="fitnado-sectionHead">
            <div>
                <h2><?= (!empty($titleCate)) ? '🏷️ ' . mb_strtoupper($titleCate, 'UTF-8') : '🔥 TẤT CẢ SẢN PHẨM & DỤNG CỤ TẬP GYM' ?></h2>
                <?php if ($com == 'tim-kiem') { ?>
                    <p>Kết quả tìm kiếm cho từ khóa: <strong>"<?= htmlspecialchars($tukhoa_show ?? '') ?>"</strong> (<?= $total ?? 0 ?> sản phẩm)</p>
                <?php } else { ?>
                    <p>Dụng cụ tập gym, thiết bị thể thao & phụ kiện chất lượng cao được tuyển chọn và đánh giá chuyên sâu.</p>
                <?php } ?>
            </div>
            <?php if (!empty($total)) { ?>
                <span class="fitnado-more" style="cursor: default;"><?= number_format($total) ?> sản phẩm</span>
            <?php } ?>
        </div>

        <?php if (!empty($product)) { ?>
            <div class="fitnado-products fitnado-products-grid">
                <?php foreach ($product as $k => $v) {
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
                            <?php } else { ?>
                                <span class="fitnado-rating">★ 9.0</span>
                            <?php } ?>
                            <h3>
                                <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                            </h3>
                            <div class="fitnado-price">
                                <?php if (!empty($v['discount'])) { ?>
                                    <span><?= $func->formatMoney($v['sale_price']) ?></span>
                                    <span class="fitnado-price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                                <?php } else { ?>
                                    <span><?= (!empty($v['regular_price'])) ? $func->formatMoney($v['regular_price']) : ((!empty($v['sale_price'])) ? $func->formatMoney($v['sale_price']) : lienhe) ?></span>
                                <?php } ?>
                            </div>
                            <small><?= (!empty($v['code'])) ? 'Mã: ' . $v['code'] : 'Tập gym | Thể thao | Chính hãng' ?></small>
                            <a href="<?= $v[$sluglang] ?>" class="fitnado-btn">Xem review →</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning w-100 my-4 text-center" role="alert">
                <strong><i class="fa-solid fa-triangle-exclamation"></i> <?= khongtimthayketqua ?></strong>
                <p class="mb-0 mt-2">Vui lòng thử tìm kiếm với từ khóa khác hoặc duyệt danh mục sản phẩm bên trên.</p>
            </div>
        <?php } ?>

        <!-- Pagination -->
        <?php if (!empty($paging)) { ?>
            <div class="pagination-home w-100 my-4 d-flex justify-content-center">
                <?= $paging ?>
            </div>
        <?php } ?>
    </section>
</main>
