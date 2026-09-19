<?php
if (!defined('TEMPLATE')) die("Error");
?>
<div class="fitnado-compare-page wrap">
    <div class="sectionHead mb-4">
        <div>
            <h1 class="compare-page-title"><i class="fas fa-balance-scale text-primary mr-2"></i>SO SÁNH SẢN PHẨM</h1>
            <p class="text-muted">Đặt lên bàn cân so sánh khách quan thông số, ưu nhược điểm và nơi mua uy tín.</p>
        </div>
    </div>

    <!-- Product Selector Bar -->
    <div class="compare-selector-bar p-3 mb-4 rounded border bg-light">
        <form action="so-sanh" method="get" class="row align-items-center">
            <div class="col-md-5 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold text-muted mb-1">Sản phẩm thứ nhất (A):</label>
                <select name="id1" class="form-control form-control-sm" onchange="this.form.submit()">
                    <?php if (!empty($allProductsForCompare)) {
                        foreach ($allProductsForCompare as $ap) { ?>
                            <option value="<?= $ap['id'] ?>" <?= (!empty($product1) && $product1['id'] == $ap['id']) ? 'selected' : '' ?>><?= htmlspecialchars($ap['name' . $lang]) ?></option>
                    <?php } } ?>
                </select>
            </div>
            <div class="col-md-2 col-12 text-center my-2 my-md-0 font-weight-bold text-primary">
                <span class="badge badge-pill badge-primary px-3 py-2 text-sm">VS</span>
            </div>
            <div class="col-md-5 col-12">
                <label class="text-xs font-weight-bold text-muted mb-1">Sản phẩm thứ hai (B):</label>
                <select name="id2" class="form-control form-control-sm" onchange="this.form.submit()">
                    <?php if (!empty($allProductsForCompare)) {
                        foreach ($allProductsForCompare as $ap) { ?>
                            <option value="<?= $ap['id'] ?>" <?= (!empty($product2) && $product2['id'] == $ap['id']) ? 'selected' : '' ?>><?= htmlspecialchars($ap['name' . $lang]) ?></option>
                    <?php } } ?>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($product1) && !empty($product2)) { ?>
        <!-- Comparison Matrix Table Desktop -->
        <div class="compare-matrix-desktop d-none d-lg-block mb-5">
            <div class="compare-grid-header row no-gutters border rounded-top overflow-hidden bg-white shadow-sm">
                <!-- Product 1 Header -->
                <div class="col-5 p-4 text-center border-right">
                    <div class="compare-card-pic mb-3 mx-auto" style="max-width:220px;">
                        <a href="<?= $product1['slugvi'] ?>" title="<?= htmlspecialchars($product1['name']) ?>">
                            <img src="<?= THUMBS ?>/220x220x1/<?= UPLOAD_PRODUCT_L . $product1['photo'] ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product1['name']) ?>">
                        </a>
                    </div>
                    <h3 class="compare-card-title mb-2">
                        <a href="<?= $product1['slugvi'] ?>" class="text-dark font-weight-bold"><?= htmlspecialchars($product1['name']) ?></a>
                    </h3>
                    <div class="compare-card-price text-primary font-weight-bold h4 mb-2">
                        <?php if ($product1['sale_price'] > 0) { ?>
                            <?= $func->formatMoney($product1['sale_price']) ?>
                            <small class="text-muted text-decoration-line-through text-sm"><?= $func->formatMoney($product1['regular_price']) ?></small>
                        <?php } else { ?>
                            <?= ($product1['regular_price'] > 0) ? $func->formatMoney($product1['regular_price']) : 'Liên hệ' ?>
                        <?php } ?>
                    </div>
                    <?php if ($product1['total_reviews'] > 0) { ?>
                        <div class="compare-card-rating text-warning font-weight-bold text-sm mb-3">
                            <i class="fas fa-star"></i> <?= $product1['avg_rating'] ?> / 5 <span class="text-muted font-weight-normal">(<?= $product1['total_reviews'] ?> nhận xét)</span>
                        </div>
                    <?php } ?>
                    <?php if (!empty($product1['best_offer'])) { ?>
                        <a href="go/<?= $product1['best_offer']['id'] ?>?src=comparison" target="_blank" rel="nofollow sponsored" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-shopping-cart mr-1"></i><?= htmlspecialchars(getAffiliatePlatformInfo($product1['best_offer']['platform'])['btn_label']) ?>
                        </a>
                    <?php } else { ?>
                        <a href="<?= $product1['slugvi'] ?>" class="btn btn-outline-primary btn-block">Xem chi tiết</a>
                    <?php } ?>
                </div>

                <!-- Criteria Column Header -->
                <div class="col-2 p-3 text-center bg-light d-flex align-items-center justify-content-center border-right">
                    <span class="font-weight-bold text-uppercase text-secondary text-sm">Tiêu chí so sánh</span>
                </div>

                <!-- Product 2 Header -->
                <div class="col-5 p-4 text-center">
                    <div class="compare-card-pic mb-3 mx-auto" style="max-width:220px;">
                        <a href="<?= $product2['slugvi'] ?>" title="<?= htmlspecialchars($product2['name']) ?>">
                            <img src="<?= THUMBS ?>/220x220x1/<?= UPLOAD_PRODUCT_L . $product2['photo'] ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product2['name']) ?>">
                        </a>
                    </div>
                    <h3 class="compare-card-title mb-2">
                        <a href="<?= $product2['slugvi'] ?>" class="text-dark font-weight-bold"><?= htmlspecialchars($product2['name']) ?></a>
                    </h3>
                    <div class="compare-card-price text-primary font-weight-bold h4 mb-2">
                        <?php if ($product2['sale_price'] > 0) { ?>
                            <?= $func->formatMoney($product2['sale_price']) ?>
                            <small class="text-muted text-decoration-line-through text-sm"><?= $func->formatMoney($product2['regular_price']) ?></small>
                        <?php } else { ?>
                            <?= ($product2['regular_price'] > 0) ? $func->formatMoney($product2['regular_price']) : 'Liên hệ' ?>
                        <?php } ?>
                    </div>
                    <?php if ($product2['total_reviews'] > 0) { ?>
                        <div class="compare-card-rating text-warning font-weight-bold text-sm mb-3">
                            <i class="fas fa-star"></i> <?= $product2['avg_rating'] ?> / 5 <span class="text-muted font-weight-normal">(<?= $product2['total_reviews'] ?> nhận xét)</span>
                        </div>
                    <?php } ?>
                    <?php if (!empty($product2['best_offer'])) { ?>
                        <a href="go/<?= $product2['best_offer']['id'] ?>?src=comparison" target="_blank" rel="nofollow sponsored" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-shopping-cart mr-1"></i><?= htmlspecialchars(getAffiliatePlatformInfo($product2['best_offer']['platform'])['btn_label']) ?>
                        </a>
                    <?php } else { ?>
                        <a href="<?= $product2['slugvi'] ?>" class="btn btn-outline-primary btn-block">Xem chi tiết</a>
                    <?php } ?>
                </div>
            </div>

            <!-- Criteria Rows -->
            <div class="compare-rows border-left border-right border-bottom rounded-bottom bg-white">
                <!-- Row: Review Type / Real Test -->
                <div class="row no-gutters border-bottom align-items-center py-3">
                    <div class="col-5 px-4 text-center">
                        <?php if (!empty($product1['is_real_test'])) { ?>
                            <span class="badge badge-success px-3 py-2 text-sm"><i class="fas fa-dumbbell mr-1"></i>Đã test thực tế tại Gym</span>
                        <?php } else { ?>
                            <span class="badge badge-light border text-muted px-2 py-1 text-xs"><?= htmlspecialchars($product1['review_type'] ?: 'Đánh giá ban biên tập') ?></span>
                        <?php } ?>
                    </div>
                    <div class="col-2 px-2 text-center font-weight-bold text-secondary text-sm">Kiểm định thực tế</div>
                    <div class="col-5 px-4 text-center">
                        <?php if (!empty($product2['is_real_test'])) { ?>
                            <span class="badge badge-success px-3 py-2 text-sm"><i class="fas fa-dumbbell mr-1"></i>Đã test thực tế tại Gym</span>
                        <?php } else { ?>
                            <span class="badge badge-light border text-muted px-2 py-1 text-xs"><?= htmlspecialchars($product2['review_type'] ?: 'Đánh giá ban biên tập') ?></span>
                        <?php } ?>
                    </div>
                </div>

                <!-- Row: Pros -->
                <?php if (!empty($product1['pros_vi']) || !empty($product2['pros_vi'])) { ?>
                    <div class="row no-gutters border-bottom align-items-center py-3 bg-light-tint">
                        <div class="col-5 px-4">
                            <?php if (!empty($product1['pros_vi'])) { ?>
                                <ul class="mb-0 pl-3 text-sm text-success font-weight-500">
                                    <?php foreach (explode("\n", trim($product1['pros_vi'])) as $pro) {
                                        if (trim($pro)) echo "<li class='mb-1'><i class='fas fa-check-circle mr-1'></i>" . htmlspecialchars(trim($pro)) . "</li>";
                                    } ?>
                                </ul>
                            <?php } else { echo "<span class='text-muted text-xs font-italic'>Chưa có dữ liệu</span>"; } ?>
                        </div>
                        <div class="col-2 px-2 text-center font-weight-bold text-success text-sm">
                            <i class="fas fa-plus-circle mr-1"></i>Ưu điểm (Pros)
                        </div>
                        <div class="col-5 px-4">
                            <?php if (!empty($product2['pros_vi'])) { ?>
                                <ul class="mb-0 pl-3 text-sm text-success font-weight-500">
                                    <?php foreach (explode("\n", trim($product2['pros_vi'])) as $pro) {
                                        if (trim($pro)) echo "<li class='mb-1'><i class='fas fa-check-circle mr-1'></i>" . htmlspecialchars(trim($pro)) . "</li>";
                                    } ?>
                                </ul>
                            <?php } else { echo "<span class='text-muted text-xs font-italic'>Chưa có dữ liệu</span>"; } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Row: Cons -->
                <?php if (!empty($product1['cons_vi']) || !empty($product2['cons_vi'])) { ?>
                    <div class="row no-gutters border-bottom align-items-center py-3">
                        <div class="col-5 px-4">
                            <?php if (!empty($product1['cons_vi'])) { ?>
                                <ul class="mb-0 pl-3 text-sm text-danger font-weight-500">
                                    <?php foreach (explode("\n", trim($product1['cons_vi'])) as $con) {
                                        if (trim($con)) echo "<li class='mb-1'><i class='fas fa-minus-circle mr-1'></i>" . htmlspecialchars(trim($con)) . "</li>";
                                    } ?>
                                </ul>
                            <?php } else { echo "<span class='text-muted text-xs font-italic'>Chưa có dữ liệu</span>"; } ?>
                        </div>
                        <div class="col-2 px-2 text-center font-weight-bold text-danger text-sm">
                            <i class="fas fa-minus-circle mr-1"></i>Nhược điểm (Cons)
                        </div>
                        <div class="col-5 px-4">
                            <?php if (!empty($product2['cons_vi'])) { ?>
                                <ul class="mb-0 pl-3 text-sm text-danger font-weight-500">
                                    <?php foreach (explode("\n", trim($product2['cons_vi'])) as $con) {
                                        if (trim($con)) echo "<li class='mb-1'><i class='fas fa-minus-circle mr-1'></i>" . htmlspecialchars(trim($con)) . "</li>";
                                    } ?>
                                </ul>
                            <?php } else { echo "<span class='text-muted text-xs font-italic'>Chưa có dữ liệu</span>"; } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Row: Suitable For -->
                <?php if (!empty($product1['suitable_vi']) || !empty($product2['suitable_vi'])) { ?>
                    <div class="row no-gutters border-bottom align-items-center py-3">
                        <div class="col-5 px-4 text-sm text-dark font-weight-500">
                            <?= !empty($product1['suitable_vi']) ? nl2br(htmlspecialchars($product1['suitable_vi'])) : '<span class="text-muted text-xs font-italic">Chưa có dữ liệu</span>' ?>
                        </div>
                        <div class="col-2 px-2 text-center font-weight-bold text-warning text-sm">
                            <i class="fas fa-bullseye mr-1"></i>Phù hợp cho
                        </div>
                        <div class="col-5 px-4 text-sm text-dark font-weight-500">
                            <?= !empty($product2['suitable_vi']) ? nl2br(htmlspecialchars($product2['suitable_vi'])) : '<span class="text-muted text-xs font-italic">Chưa có dữ liệu</span>' ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Row: Specifications -->
                <?php if (!empty($product1['specifications_vi']) || !empty($product2['specifications_vi'])) { ?>
                    <div class="row no-gutters border-bottom align-items-center py-3">
                        <div class="col-5 px-4 text-sm">
                            <?= !empty($product1['specifications_vi']) ? $func->decodeHtmlChars($product1['specifications_vi']) : '<span class="text-muted text-xs font-italic">Chưa có thông số</span>' ?>
                        </div>
                        <div class="col-2 px-2 text-center font-weight-bold text-info text-sm">
                            <i class="fas fa-list-ul mr-1"></i>Thông số kỹ thuật
                        </div>
                        <div class="col-5 px-4 text-sm">
                            <?= !empty($product2['specifications_vi']) ? $func->decodeHtmlChars($product2['specifications_vi']) : '<span class="text-muted text-xs font-italic">Chưa có thông số</span>' ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Row: All Affiliate Offers -->
                <div class="row no-gutters align-items-center py-3">
                    <div class="col-5 px-4">
                        <?php if (!empty($product1['offers'])) { ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($product1['offers'] as $off) {
                                    $platInfo = getAffiliatePlatformInfo($off['platform']); ?>
                                    <a href="go/<?= $off['id'] ?>?src=comparison" target="_blank" rel="nofollow sponsored" class="btn btn-sm text-white font-weight-bold mr-2 mb-2" style="background-color: <?= $platInfo['bg'] ?>;">
                                        <i class="<?= $platInfo['icon'] ?> mr-1"></i><?= htmlspecialchars($platInfo['name']) ?>: <?= ($off['price'] > 0) ? $func->formatMoney($off['price']) : 'Xem giá' ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } else { echo "<span class='text-muted text-xs'>Chưa có nơi bán</span>"; } ?>
                    </div>
                    <div class="col-2 px-2 text-center font-weight-bold text-secondary text-sm">
                        <i class="fas fa-store mr-1"></i>Nơi bán sẵn có
                    </div>
                    <div class="col-5 px-4">
                        <?php if (!empty($product2['offers'])) { ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($product2['offers'] as $off) {
                                    $platInfo = getAffiliatePlatformInfo($off['platform']); ?>
                                    <a href="go/<?= $off['id'] ?>?src=comparison" target="_blank" rel="nofollow sponsored" class="btn btn-sm text-white font-weight-bold mr-2 mb-2" style="background-color: <?= $platInfo['bg'] ?>;">
                                        <i class="<?= $platInfo['icon'] ?> mr-1"></i><?= htmlspecialchars($platInfo['name']) ?>: <?= ($off['price'] > 0) ? $func->formatMoney($off['price']) : 'Xem giá' ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } else { echo "<span class='text-muted text-xs'>Chưa có nơi bán</span>"; } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Stacked Comparison (<= 991px) -->
        <div class="compare-matrix-mobile d-lg-none mb-5">
            <!-- Top comparison cards 2-col -->
            <div class="row row-10 mb-3">
                <div class="col-6 mg-col-10">
                    <div class="card p-2 text-center h-100 border shadow-sm">
                        <img src="<?= THUMBS ?>/140x140x1/<?= UPLOAD_PRODUCT_L . $product1['photo'] ?>" class="img-fluid rounded mx-auto mb-2" style="max-height:110px;">
                        <h4 class="font-weight-bold text-sm mb-1 line-clamp-2"><?= htmlspecialchars($product1['name']) ?></h4>
                        <div class="text-primary font-weight-bold text-sm mb-2"><?= ($product1['sale_price'] > 0) ? $func->formatMoney($product1['sale_price']) : ($product1['regular_price'] > 0 ? $func->formatMoney($product1['regular_price']) : 'Liên hệ') ?></div>
                        <?php if (!empty($product1['best_offer'])) { ?>
                            <a href="go/<?= $product1['best_offer']['id'] ?>?src=comparison" target="_blank" rel="nofollow sponsored" class="btn btn-xs btn-primary btn-block font-weight-bold">Mua ngay</a>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-6 mg-col-10">
                    <div class="card p-2 text-center h-100 border shadow-sm">
                        <img src="<?= THUMBS ?>/140x140x1/<?= UPLOAD_PRODUCT_L . $product2['photo'] ?>" class="img-fluid rounded mx-auto mb-2" style="max-height:110px;">
                        <h4 class="font-weight-bold text-sm mb-1 line-clamp-2"><?= htmlspecialchars($product2['name']) ?></h4>
                        <div class="text-primary font-weight-bold text-sm mb-2"><?= ($product2['sale_price'] > 0) ? $func->formatMoney($product2['sale_price']) : ($product2['regular_price'] > 0 ? $func->formatMoney($product2['regular_price']) : 'Liên hệ') ?></div>
                        <?php if (!empty($product2['best_offer'])) { ?>
                            <a href="go/<?= $product2['best_offer']['id'] ?>?src=comparison" target="_blank" rel="nofollow sponsored" class="btn btn-xs btn-primary btn-block font-weight-bold">Mua ngay</a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Mobile Table Accordion / Blocks -->
            <div class="card border rounded overflow-hidden shadow-sm">
                <!-- Rating -->
                <div class="p-3 border-bottom bg-light">
                    <strong class="d-block text-xs text-uppercase text-secondary mb-2"><i class="fas fa-star text-warning mr-1"></i>Đánh giá thực tế</strong>
                    <div class="row text-sm">
                        <div class="col-6 border-right">
                            <?= ($product1['total_reviews'] > 0) ? "<strong class='text-warning'>" . $product1['avg_rating'] . "/5</strong> (" . $product1['total_reviews'] . " đánh giá)" : "<span class='text-muted text-xs'>Chưa có đánh giá</span>" ?>
                        </div>
                        <div class="col-6">
                            <?= ($product2['total_reviews'] > 0) ? "<strong class='text-warning'>" . $product2['avg_rating'] . "/5</strong> (" . $product2['total_reviews'] . " đánh giá)" : "<span class='text-muted text-xs'>Chưa có đánh giá</span>" ?>
                        </div>
                    </div>
                </div>

                <!-- Pros -->
                <?php if (!empty($product1['pros_vi']) || !empty($product2['pros_vi'])) { ?>
                    <div class="p-3 border-bottom">
                        <strong class="d-block text-xs text-uppercase text-success mb-2"><i class="fas fa-plus-circle mr-1"></i>Ưu điểm nổi bật</strong>
                        <div class="row text-xs">
                            <div class="col-6 border-right text-success font-weight-500">
                                <?= !empty($product1['pros_vi']) ? nl2br(htmlspecialchars($product1['pros_vi'])) : "<span class='text-muted'>--</span>" ?>
                            </div>
                            <div class="col-6 text-success font-weight-500">
                                <?= !empty($product2['pros_vi']) ? nl2br(htmlspecialchars($product2['pros_vi'])) : "<span class='text-muted'>--</span>" ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Cons -->
                <?php if (!empty($product1['cons_vi']) || !empty($product2['cons_vi'])) { ?>
                    <div class="p-3 border-bottom">
                        <strong class="d-block text-xs text-uppercase text-danger mb-2"><i class="fas fa-minus-circle mr-1"></i>Nhược điểm lưu ý</strong>
                        <div class="row text-xs">
                            <div class="col-6 border-right text-danger font-weight-500">
                                <?= !empty($product1['cons_vi']) ? nl2br(htmlspecialchars($product1['cons_vi'])) : "<span class='text-muted'>--</span>" ?>
                            </div>
                            <div class="col-6 text-danger font-weight-500">
                                <?= !empty($product2['cons_vi']) ? nl2br(htmlspecialchars($product2['cons_vi'])) : "<span class='text-muted'>--</span>" ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Suitable -->
                <?php if (!empty($product1['suitable_vi']) || !empty($product2['suitable_vi'])) { ?>
                    <div class="p-3 border-bottom bg-light">
                        <strong class="d-block text-xs text-uppercase text-warning mb-2"><i class="fas fa-bullseye mr-1"></i>Đối tượng phù hợp</strong>
                        <div class="row text-xs">
                            <div class="col-6 border-right">
                                <?= !empty($product1['suitable_vi']) ? nl2br(htmlspecialchars($product1['suitable_vi'])) : "<span class='text-muted'>--</span>" ?>
                            </div>
                            <div class="col-6">
                                <?= !empty($product2['suitable_vi']) ? nl2br(htmlspecialchars($product2['suitable_vi'])) : "<span class='text-muted'>--</span>" ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center py-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
            <strong>Không đủ dữ liệu sản phẩm để so sánh.</strong> Vui lòng chọn lại danh sách sản phẩm ở trên.
        </div>
    <?php } ?>
</div>
