<div class="menu-res">
    <div class="menu-bar-res d-flex align-items-center justify-content-between ">
        <a id="hamburger" href="#menu" title="Menu"><span></span></a>
        <div class="search-res">
            <p class="icon-search transition"><i class="bi bi-search"></i></p>
            <div class="search-grid w-clear">
                <input type="text" name="keyword-res" id="keyword-res" placeholder="<?= nhaptukhoatimkiem ?>" onkeypress="doEnter(event,'keyword-res');" value="<?= (!empty($_GET['keyword'])) ? $_GET['keyword'] : "" ?>" />
                <p onclick="onSearch('keyword-res');"><i class="bi bi-search"></i></p>
            </div>
        </div>
    </div>
    <nav id="menu">
        <ul>
            <li><a class="<?php if ($com == '' || $com == 'index') echo 'active'; ?> transition" href="" title="<?= trangchu ?>"><?= trangchu ?></a></li>
            <li class="menu-line"></li>
            <li><a class="<?php if ($com == 'gioi-thieu') echo 'active'; ?> transition" href="gioi-thieu" title="<?= gioithieu ?>"><?= gioithieu ?></a></li>
            <li class="menu-line"></li>
            <li>
                <a class="has-child <?php if ($com == 'san-pham') echo 'active'; ?> transition" href="san-pham" title="<?= sanpham ?>"><?= sanpham ?></a>
                <?php if (count($productListMenu)) { ?>
                    <ul>
                        <?php foreach ($productListMenu as $klist => $vlist) {
                            $productCatMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>
                            <li>
                                <a class="has-child transition" title="<?= $vlist['name' . $lang] ?>" href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
                                <?php if (!empty($productCatMenu)) { ?>
                                    <ul>
                                        <?php foreach ($productCatMenu as $kcat => $vcat) {
                                            $productItemMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_item where id_cat = ? and find_in_set('hienthi',status) order by numb,id desc", array($vcat['id'])); ?>
                                            <li>
                                                <a class="has-child transition" title="<?= $vcat['name' . $lang] ?>" href="<?= $vcat[$sluglang] ?>"><?= $vcat['name' . $lang] ?></a>
                                                <?php if (!empty($productItemMenu)) { ?>
                                                    <ul>
                                                        <?php foreach ($productItemMenu as $kitem => $vitem) {
                                                            $productSubMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>
                                                            <li>
                                                                <a class="has-child transition" title="<?= $vitem['name' . $lang] ?>" href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>
                                                                <?php if (!empty($productSubMenu)) { ?>
                                                                    <ul>
                                                                        <?php foreach ($productSubMenu as $ksub => $vsub) { ?>
                                                                            <li>
                                                                                <a class="transition" title="<?= $vsub['name' . $lang] ?>" href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                <?php } ?>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <li class="menu-line"></li>
            <li>
                <a class="has-child <?php if ($com == 'tin-tuc') echo 'active'; ?> transition" href="tin-tuc" title="<?= tintuc ?>"><?= tintuc ?></a>
                <?php if (count($newsListMenu)) { ?>
                    <ul>
                        <?php foreach ($newsListMenu as $klist => $vlist) {
                            $newsCatMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($vlist['id'])); ?>
                            <li>
                                <a class="has-child transition" title="<?= $vlist['name' . $lang] ?>" href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
                                <?php if (!empty($newsCatMenu)) { ?>
                                    <ul>
                                        <?php foreach ($newsCatMenu as $kcat => $vcat) {
                                            $newsItemMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_item where id_cat = ? and find_in_set('hienthi',status) order by numb,id desc", array($vcat['id'])); ?>
                                            <li>
                                                <a class="has-child transition" title="<?= $vcat['name' . $lang] ?>" href="<?= $vcat[$sluglang] ?>"><?= $vcat['name' . $lang] ?></a>
                                                <?php if (!empty($newsItemMenu)) { ?>
                                                    <ul>
                                                        <?php foreach ($newsItemMenu as $kitem => $vitem) {
                                                            $newsSubMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_sub where id_item = ? and find_in_set('hienthi',status) order by numb,id desc", array($vitem['id'])); ?>
                                                            <li>
                                                                <a class="has-child transition" title="<?= $vitem['name' . $lang] ?>" href="<?= $vitem[$sluglang] ?>"><?= $vitem['name' . $lang] ?></a>
                                                                <?php if (!empty($newsSubMenu)) { ?>
                                                                    <ul>
                                                                        <?php foreach ($newsSubMenu as $ksub => $vsub) { ?>
                                                                            <li>
                                                                                <a class="transition" title="<?= $vsub['name' . $lang] ?>" href="<?= $vsub[$sluglang] ?>"><?= $vsub['name' . $lang] ?></a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                <?php } ?>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <li class="menu-line"></li>
            <li><a class="<?php if ($com == 'tuyen-dung') echo 'active'; ?> transition" href="tuyen-dung" title="<?= tuyendung ?>"><?= tuyendung ?></a></li>
            <li class="menu-line"></li>
            <li><a class="<?php if ($com == 'thu-vien-anh') echo 'active'; ?> transition" href="thu-vien-anh" title="<?= thuvienanh ?>"><?= thuvienanh ?></a></li>
            <li class="menu-line"></li>
            <li><a class="<?php if ($com == 'video') echo 'active'; ?> transition" href="video" title="Video">Video</a></li>
            <li class="menu-line"></li>
            <li><a class="<?php if ($com == 'lien-he') echo 'active'; ?> transition" href="lien-he" title="<?= lienhe ?>"><?= lienhe ?></a></li>
        </ul>
    </nav>
</div>