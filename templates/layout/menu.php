<nav class="fitnado-nav">
    <div class="fitnado-wrap">
        <ul class="fitnado-nav-list">
            <li class="<?= ($com == '' || $com == 'index') ? 'active' : '' ?>">
                <a href="" title="<?= trangchu ?>">⌂ <?= trangchu ?></a>
            </li>

            <li class="<?= ($com == 'san-pham') ? 'active' : '' ?>">
                <a href="san-pham" title="<?= sanpham ?>">Đồ tập <?= (!empty($productListMenu)) ? '⌄' : '' ?></a>
                <?php if (!empty($productListMenu)) { ?>
                    <ul class="dropdown-menu-fitnado">
                        <?php foreach ($productListMenu as $klist => $vlist) {
                            $productCatMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>" title="<?= $vlist['name' . $lang] ?>"><?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li class="<?= ($com == 'tin-tuc') ? 'active' : '' ?>">
                <a href="tin-tuc" title="Review">Review <?= (!empty($newsListMenu)) ? '⌄' : '' ?></a>
                <?php if (!empty($newsListMenu)) { ?>
                    <ul class="dropdown-menu-fitnado">
                        <?php foreach ($newsListMenu as $klist => $vlist) { ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>" title="<?= $vlist['name' . $lang] ?>"><?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li>
                <a href="san-pham" title="So sánh">So sánh ⌄</a>
                <?php if (!empty($productListMenu)) { ?>
                    <ul class="dropdown-menu-fitnado">
                        <?php foreach ($productListMenu as $vlist) { ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>" title="So sánh <?= $vlist['name' . $lang] ?>">So sánh <?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li>
                <a href="san-pham" title="Top sản phẩm">Top sản phẩm</a>
            </li>

            <li class="<?= ($com == 'tin-tuc') ? 'active' : '' ?>">
                <a href="tin-tuc" title="Kiến thức">Kiến thức</a>
            </li>

            <li class="<?= ($com == 'video') ? 'active' : '' ?>">
                <a href="video" title="Video">Video</a>
            </li>

            <li class="<?= ($com == 'gioi-thieu') ? 'active' : '' ?>">
                <a href="gioi-thieu" title="Về chúng tôi">Về chúng tôi</a>
            </li>

            <li class="<?= ($com == 'lien-he') ? 'active' : '' ?>">
                <a href="lien-he" title="<?= lienhe ?>"><?= lienhe ?></a>
            </li>
        </ul>
    </div>
</nav>