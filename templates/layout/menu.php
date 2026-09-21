<nav class="fitnado-nav">
    <div class="fitnado-wrap">
        <ul class="fitnado-nav-list">
            <li class="<?= ($com == '' || $com == 'index') ? 'active' : '' ?>">
                <a href="" title="<?= trangchu ?>"><i class="fa-solid fa-house mr-1"></i> <?= trangchu ?></a>
            </li>

            <li class="<?= ($com == 'san-pham') ? 'active' : '' ?>">
                <a href="san-pham" title="<?= sanpham ?>"><i class="fa-solid fa-dumbbell mr-1"></i> Đồ tập <?= (!empty($productListMenu)) ? '<i class="fa-solid fa-chevron-down nav-arrow"></i>' : '' ?></a>
                <?php if (!empty($productListMenu)) { ?>
                    <ul class="dropdown-menu-fitnado">
                        <?php foreach ($productListMenu as $klist => $vlist) {
                            $productCatMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>" title="<?= $vlist['name' . $lang] ?>"><i class="fa-solid fa-angle-right mr-2 text-muted"></i> <?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li class="<?= ($com == 'danh-gia-review' || ($com == 'tin-tuc' && !empty($act) && $act == 'review')) ? 'active' : '' ?>">
                <a href="danh-gia-review" title="Đánh giá & Review"><i class="fa-solid fa-star-half-stroke mr-1"></i> Review <?= (!empty($newsListMenu)) ? '<i class="fa-solid fa-chevron-down nav-arrow"></i>' : '' ?></a>
                <?php if (!empty($newsListMenu)) { ?>
                    <ul class="dropdown-menu-fitnado">
                        <?php foreach ($newsListMenu as $klist => $vlist) { ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>" title="<?= $vlist['name' . $lang] ?>"><i class="fa-solid fa-angle-right mr-2 text-muted"></i> <?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li class="<?= ($com == 'so-sanh-san-pham') ? 'active' : '' ?>">
                <a href="so-sanh-san-pham" title="So sánh sản phẩm"><i class="fa-solid fa-scale-balanced mr-1"></i> So sánh <?= (!empty($productListMenu)) ? '<i class="fa-solid fa-chevron-down nav-arrow"></i>' : '' ?></a>
                <?php if (!empty($productListMenu)) { ?>
                    <ul class="dropdown-menu-fitnado">
                        <?php foreach ($productListMenu as $vlist) { ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>" title="So sánh <?= $vlist['name' . $lang] ?>"><i class="fa-solid fa-angle-right mr-2 text-muted"></i> So sánh <?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li class="<?= ($com == 'huong-dan-chon-mua') ? 'active' : '' ?>">
                <a href="huong-dan-chon-mua" title="Hướng dẫn chọn mua"><i class="fa-solid fa-compass mr-1"></i> Hướng dẫn chọn mua</a>
            </li>

            <li class="<?= ($com == 'kien-thuc-tap-luyen' || $com == 'tin-tuc') ? 'active' : '' ?>">
                <a href="kien-thuc-tap-luyen" title="Kiến thức thể hình"><i class="fa-solid fa-book-open-reader mr-1"></i> Kiến thức</a>
            </li>

            <li class="<?= ($com == 'video') ? 'active' : '' ?>">
                <a href="video" title="Video 30s Review"><i class="fa-solid fa-circle-play mr-1"></i> Video</a>
            </li>

            <li class="<?= ($com == 'gioi-thieu') ? 'active' : '' ?>">
                <a href="gioi-thieu" title="Về chúng tôi"><i class="fa-solid fa-circle-info mr-1"></i> Về chúng tôi</a>
            </li>

            <li class="<?= ($com == 'lien-he') ? 'active' : '' ?>">
                <a href="lien-he" title="<?= lienhe ?>"><i class="fa-solid fa-paper-plane mr-1"></i> <?= lienhe ?></a>
            </li>
        </ul>
    </div>
</nav>