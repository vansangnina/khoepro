<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 text-sm">
    <!-- Logo -->
    <!-- <a class="brand-link" href="index.php">
        <img class="brand-image" src="assets/images/nina.png" alt="Nina">
    </a> -->

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent text-sm" data-widget="treeview" role="menu" data-accordion="false">
                <!-- dashboard -->
                <?php
                $active = "";
                if ($com == 'index' || $com == '') $active = 'active';
                ?>
                <li class="nav-item <?= $active ?>">
                    <a class="nav-link <?= $active ?>" href="index.php" title="<?=dashboard?>">
                        <i class="nav-icon text-sm fas fa-tachometer-alt"></i>
                        <p><?=dashboard?></p>
                    </a>
                </li>

                <!-- Product Research & AI Agent (Phase 03 + Phase 04) -->
                <?php
                $isResearchActive = ($com == 'product_research');
                ?>
                <li class="nav-item has-treeview <?= $isResearchActive ? 'menu-open' : '' ?>">
                    <a class="nav-link <?= $isResearchActive ? 'active' : '' ?>" href="#" title="Nghiên cứu & AI Discovery">
                        <i class="nav-icon text-sm fas fa-search-dollar text-warning"></i>
                        <p>
                            Nghiên cứu sản phẩm
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a class="nav-link <?= ($com == 'product_research' && in_array($act, array('man', 'add', 'edit', 'create_product'))) ? 'active' : '' ?>" href="index.php?com=product_research&act=man" title="Ứng viên nghiên cứu">
                                <i class="nav-icon text-sm far fa-circle text-info"></i>
                                <p>Ứng viên nghiên cứu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($com == 'product_research' && in_array($act, array('seeds', 'add_seed', 'edit_seed'))) ? 'active' : '' ?>" href="index.php?com=product_research&act=seeds" title="Từ khóa / Seeds">
                                <i class="nav-icon text-sm far fa-circle text-success"></i>
                                <p>Từ khóa & Seeds</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($com == 'product_research' && $act == 'jobs') ? 'active' : '' ?>" href="index.php?com=product_research&act=jobs" title="Hàng đợi Jobs">
                                <i class="nav-icon text-sm far fa-circle text-warning"></i>
                                <p>Hàng đợi Jobs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($com == 'product_research' && $act == 'provider_config') ? 'active' : '' ?>" href="index.php?com=product_research&act=provider_config" title="Cấu hình AI & API">
                                <i class="nav-icon text-sm far fa-circle text-purple"></i>
                                <p>Cấu hình AI & API</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($com == 'product_research' && $act == 'weights') ? 'active' : '' ?>" href="index.php?com=product_research&act=weights" title="Trọng số chấm điểm">
                                <i class="nav-icon text-sm far fa-circle text-secondary"></i>
                                <p>Trọng số chấm điểm</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Affiliate Clicks -->
                <li class="nav-item <?= ($com == 'affiliate') ? 'active' : '' ?>">
                    <a class="nav-link <?= ($com == 'affiliate') ? 'active' : '' ?>" href="index.php?com=affiliate&act=man" title="Thống kê Affiliate Clicks">
                        <i class="nav-icon text-sm fas fa-mouse-pointer text-success"></i>
                        <p>Affiliate Clicks</p>
                    </a>
                </li>

                <!-- Group -->
                <?php $disabled = array();
                if (isset($config['group'])) {
                    foreach ($config['group'] as $key => $value) { ?>
                        <li class="nav-item has-treeview menu-group">
                            <a class="nav-link" href="#" title="<?= $key ?>">
                                <i class="nav-icon text-sm fas fa-layer-group"></i>
                                <p>
                                    <?= $key ?>
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php if (isset($value['product'])) {
                                    foreach ($value['product'] as $k) { ?>
                                        <?php
                                        $disabled['product'][$k] = 1;
                                        $v = $config['product'][$k];
                                        $none = "";
                                        $active = "";
                                        $menuopen = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_list', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_cat', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_item', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_sub', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_brand', $k, null, 'phrase-1') && $func->checkPermission('product', 'man', $k, null, 'phrase-1') && $func->checkPermission('import', 'man', $k, null, 'phrase-1') && $func->checkPermission('export', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if ((($com == 'product') || ($com == 'import') || ($com == 'export')) && ($k == $_GET['type'])) {
                                            $active = 'active';
                                            $menuopen = 'menu-open';
                                        }
                                        ?>
                                        <?php if (!empty($v['dropdown'])) { ?>
                                            <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                                                <a class="nav-link <?= $active ?>" href="#" title="<?= $v['title_main'] ?>">
                                                    <i class="nav-icon text-sm fas fa-boxes"></i>
                                                    <p>
                                                        <?= $v['title_main'] ?>
                                                        <i class="right fas fa-angle-left"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <?php if (isset($v['list']) && $v['list'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_list', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_list' || $act == 'add_list' || $act == 'edit_list' || $kind == 'man_list') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_list&type=<?= $k ?>" title="<?=danhmuccap1?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap1?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['cat']) && $v['cat'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_cat', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_cat' || $act == 'add_cat' || $act == 'edit_cat' || $kind == 'man_cat') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_cat&type=<?= $k ?>" title="<?=danhmuccap2?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap2?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['item']) && $v['item'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_item', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_item' || $act == 'add_item' || $act == 'edit_item' || $kind == 'man_item') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_item&type=<?= $k ?>" title="<?=danhmuccap3?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap3?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['sub']) && $v['sub'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_sub', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_sub' || $act == 'add_sub' || $act == 'edit_sub' || $kind == 'man_sub') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_sub&type=<?= $k ?>" title="<?=danhmuccap4?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap4?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['brand']) && $v['brand'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_brand', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_brand' || $act == 'add_brand' || $act == 'edit_brand') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_brand&type=<?= $k ?>" title="<?=danhmuchang?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuchang?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['color']) && $v['color'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_color', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_color' || $act == 'add_color' || $act == 'edit_color') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_color&type=<?= $k ?>" title="<?=danhmucmausac?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmucmausac?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['size']) && $v['size'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_size', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'product' && ($act == 'man_size' || $act == 'add_size' || $act == 'edit_size') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_size&type=<?= $k ?>" title="<?=danhmuckichthuoc?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuckichthuoc?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php
                                                    $none = "";
                                                    $active = "";
                                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                                    if ($com == 'product' && ($act == 'man' || $act == 'add' || $act == 'edit' || $act == 'copy' || $kind == 'man') && $k == $_GET['type']) $active = "active";
                                                    ?>
                                                    <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                            <p><?= $v['title_main'] ?></p>
                                                        </a></li>
                                                    <?php if (isset($v['import']) && $v['import'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('import', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                                        if (($com == 'import') && ($k == $_GET['type'])) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>">
                                                            <a class="nav-link <?= $active ?>" href="index.php?com=import&act=man&type=<?= $k ?>" title="Import"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p>Import</p>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if (isset($v['export']) && $v['export'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('export', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                                        if (($com == 'export') && ($act == 'man') && ($k == $_GET['type'])) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>">
                                                            <a class="nav-link <?= $active ?>" href="index.php?com=export&act=man&type=<?= $k ?>" title="Export"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p>Export</p>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                <?php }
                                    }
                                } ?>

                                <?php if (isset($value['product'])) {
                                    foreach ($value['product'] as $k) { ?>
                                        <?php
                                        $disabled['product'][$k] = 1;
                                        $v = $config['product'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'product' && $k == $_GET['type']) $active = "active";
                                        ?>
                                        <?php if (empty($v['dropdown'])) { ?>
                                            <li class="nav-item <?= $none ?>">
                                                <a class="nav-link <?= $active ?>" href="index.php?com=product&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm fas fa-boxes"></i>
                                                    <p><?= $v['title_main'] ?></p>
                                                </a>
                                            </li>
                                <?php }
                                    }
                                } ?>

                                <?php if (isset($value['news'])) {
                                    foreach ($value['news'] as $k) { ?>
                                        <?php
                                        $disabled['news'][$k] = 1;
                                        $v = $config['news'][$k];
                                        $none = "";
                                        $active = "";
                                        $menuopen = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_list', $k, null, 'phrase-1') && $func->checkPermission('news', 'man_cat', $k, null, 'phrase-1') && $func->checkPermission('news', 'man_item', $k, null, 'phrase-1') && $func->checkPermission('news', 'man_sub', $k, null, 'phrase-1') && $func->checkPermission('news', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if (($com == 'news') && ($k == $_GET['type'])) {
                                            $active = 'active';
                                            $menuopen = 'menu-open';
                                        }

                                        ?>
                                        <?php if (!empty($v['dropdown'])) { ?>
                                            <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                                                <a class="nav-link <?= $active ?>" href="#" title="<?= $v['title_main'] ?>">
                                                    <i class="nav-icon text-sm fas fa-book"></i>
                                                    <p>
                                                        <?= $v['title_main'] ?>
                                                        <i class="right fas fa-angle-left"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <?php if (isset($v['list']) && $v['list'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_list', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'news' && ($act == 'man_list' || $act == 'add_list' || $act == 'edit_list' || $kind == 'man_list' || $kind == 'man_list') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_list&type=<?= $k ?>" title="<?=danhmuccap1?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap1?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['cat']) && $v['cat'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_cat', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'news' && ($act == 'man_cat' || $act == 'add_cat' || $act == 'edit_cat' || $kind == 'man_cat' || $kind == 'man_cat') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_cat&type=<?= $k ?>" title="<?=danhmuccap2?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap2?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['item']) && $v['item'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_item', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'news' && ($act == 'man_item' || $act == 'add_item' || $act == 'edit_item' || $kind == 'man_item' || $kind == 'man_item') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_item&type=<?= $k ?>" title="<?=danhmuccap3?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap3?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php if (isset($v['sub']) && $v['sub'] == true) {
                                                        $none = "";
                                                        $active = "";
                                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_sub', $k, null, 'phrase-1')) $none = "d-none";
                                                        if ($com == 'news' && ($act == 'man_sub' || $act == 'add_sub' || $act == 'edit_sub' || $kind == 'man_sub' || $kind == 'man_sub') && $k == $_GET['type']) $active = "active"; ?>
                                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_sub&type=<?= $k ?>" title="<?=danhmuccap4?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                                <p><?=danhmuccap4?></p>
                                                            </a></li>
                                                    <?php } ?>
                                                    <?php
                                                    $none = "";
                                                    $active = "";
                                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                                    if ($com == 'news' && ($act == 'man' || $act == 'add' || $act == 'edit' || $act == 'copy' || $kind == 'man') && $k == $_GET['type']) $active = "active";
                                                    ?>
                                                    <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                            <p><?= $v['title_main'] ?></p>
                                                        </a></li>
                                                </ul>
                                            </li>
                                <?php }
                                    }
                                } ?>

                                <?php if (isset($value['news'])) {
                                    foreach ($value['news'] as $k) { ?>
                                        <?php
                                        $disabled['news'][$k] = 1;
                                        $v = $config['news'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'news' && $k == $_GET['type']) $active = "active";
                                        ?>
                                        <?php if (empty($v['dropdown'])) { ?>
                                            <li class="nav-item <?= $none ?>">
                                                <a class="nav-link <?= $active ?>" href="index.php?com=news&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm fas fa-book"></i>
                                                    <p><?= $v['title_main'] ?></p>
                                                </a>
                                            </li>
                                <?php }
                                    }
                                } ?>

                                <?php if (isset($value['tags'])) {
                                    foreach ($value['tags'] as $k) { ?>
                                        <?php
                                        $disabled['tags'][$k] = 1;
                                        $v = $config['tags'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('tags', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'tags' && $k == $_GET['type']) $active = "active";
                                        ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=tags&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>

                                <?php if (isset($value['static'])) {
                                    foreach ($value['static'] as $k) { ?>
                                        <?php
                                        $disabled['static'][$k] = 1;
                                        $v = $config['static'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('static', 'update', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'static' && $k == $_GET['type']) $active = "active";
                                        ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=static&act=update&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>

                                <?php if (isset($value['newsletter'])) {
                                    foreach ($value['newsletter'] as $k) { ?>
                                        <?php
                                        $disabled['newsletter'][$k] = 1;
                                        $v = $config['newsletter'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('newsletter', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'newsletter' && $k == $_GET['type']) $active = "active";
                                        ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=newsletter&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>

                                <?php if (isset($value['photo'])) {
                                    foreach ($value['photo'] as $k) {
                                        $disabled['photo'][$k] = 1;
                                        $v = $config['photo']['man_photo'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('photo', 'man_photo', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'photo' && $_GET['type'] == $k && ($act == 'man_photo' || $act == 'add_photo' || $act == 'edit_photo')) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=photo&act=man_photo&type=<?= $k ?>" title="<?= $v['title_main_photo'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main_photo'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>

                                <?php if (isset($value['photo_static'])) {
                                    foreach ($value['photo_static'] as $k) {
                                        $disabled['photo_static'][$k] = 1;
                                        $v = $config['photo']['photo_static'][$k];
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('photo', 'photo_static', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'photo' && $_GET['type'] == $k && $act == 'photo_static') $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=photo&act=photo_static&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>
                            </ul>
                        </li>
                <?php }
                } ?>

                <!-- Sản phẩm -->
                <?php if (isset($config['product'])) { ?>
                    <?php foreach ($config['product'] as $k => $v) {
                        if (!isset($disabled['product'][$k])) { ?>
                            <?php
                            $none = "";
                            $active = "";
                            $menuopen = "";
                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_list', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_cat', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_item', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_sub', $k, null, 'phrase-1') && $func->checkPermission('product', 'man_brand', $k, null, 'phrase-1') && $func->checkPermission('product', 'man', $k, null, 'phrase-1') && $func->checkPermission('import', 'man', $k, null, 'phrase-1') && $func->checkPermission('export', 'man', $k, null, 'phrase-1')) $none = "d-none";
                            if ((($com == 'product') || ($com == 'import') || ($com == 'export')) && ($k == $_GET['type'])) {
                                $active = 'active';
                                $menuopen = 'menu-open';
                            }
                            ?>
                            <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                                <a class="nav-link <?= $active ?>" href="#" title="<?= $v['title_main'] ?>">
                                    <i class="nav-icon text-sm fas fa-boxes"></i>
                                    <p>
                                        <?= $v['title_main'] ?>
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (!empty($v['dropdown'])) {
                                        if (isset($v['list']) && $v['list'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_list', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'product' && ($act == 'man_list' || $act == 'add_list' || $act == 'edit_list' || $kind == 'man_list') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_list&type=<?= $k ?>" title="<?=danhmuccap1?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap1?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php if (isset($v['cat']) && $v['cat'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_cat', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'product' && ($act == 'man_cat' || $act == 'add_cat' || $act == 'edit_cat' || $kind == 'man_cat') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_cat&type=<?= $k ?>" title="<?=danhmuccap2?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap2?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php if (isset($v['item']) && $v['item'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_item', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'product' && ($act == 'man_item' || $act == 'add_item' || $act == 'edit_item' || $kind == 'man_item') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_item&type=<?= $k ?>" title="<?=danhmuccap3?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap3?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php if (isset($v['sub']) && $v['sub'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_sub', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'product' && ($act == 'man_sub' || $act == 'add_sub' || $act == 'edit_sub' || $kind == 'man_sub') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_sub&type=<?= $k ?>" title="<?=danhmuccap4?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap4?></p>
                                                </a></li>
                                    <?php }
                                    } ?>
                                    <?php if (isset($v['brand']) && $v['brand'] == true) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_brand', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'product' && ($act == 'man_brand' || $act == 'add_brand' || $act == 'edit_brand') && $k == $_GET['type']) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_brand&type=<?= $k ?>" title="<?=danhmuchang?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?=danhmuchang?></p>
                                            </a></li>
                                    <?php } ?>
                                    <?php if (isset($v['color']) && $v['color'] == true) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_color', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'product' && ($act == 'man_color' || $act == 'add_color' || $act == 'edit_color') && $k == $_GET['type']) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_color&type=<?= $k ?>" title="<?=danhmucmausac?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?=danhmucmausac?></p>
                                            </a></li>
                                    <?php } ?>
                                    <?php if (isset($v['size']) && $v['size'] == true) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man_size', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'product' && ($act == 'man_size' || $act == 'add_size' || $act == 'edit_size') && $k == $_GET['type']) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man_size&type=<?= $k ?>" title="<?=danhmuckichthuoc?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?=danhmuckichthuoc?></p>
                                            </a></li>
                                    <?php } ?>
                                    <?php
                                    $none = "";
                                    $active = "";
                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('product', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                    if ($com == 'product' && ($act == 'man' || $act == 'add' || $act == 'edit' || $act == 'copy' || $kind == 'man') && $k == $_GET['type']) $active = "active";
                                    ?>
                                    <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=product&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                            <p><?= $v['title_main'] ?></p>
                                        </a></li>
                                    <?php if (isset($v['import']) && $v['import'] == true) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('import', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if (($com == 'import') && ($k == $_GET['type'])) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=import&act=man&type=<?= $k ?>" title="Import"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p>Import</p>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (isset($v['export']) && $v['export'] == true) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('export', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if (($com == 'export') && ($act == 'man') && ($k == $_GET['type'])) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=export&act=man&type=<?= $k ?>" title="Export"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p>Export</p>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                    <?php }
                    } ?>
                <?php } ?>

                <!-- Bài viết (Có cấp) -->
                <?php if (isset($config['news'])) { ?>
                    <?php foreach ($config['news'] as $k => $v) {
                        if (!isset($disabled['news'][$k])) {
                            if (!empty($v['dropdown'])) { ?>
                                <?php
                                $none = "";
                                $active = "";
                                $menuopen = "";
                                if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_list', $k, null, 'phrase-1') && $func->checkPermission('news', 'man_cat', $k, null, 'phrase-1') && $func->checkPermission('news', 'man_item', $k, null, 'phrase-1') && $func->checkPermission('news', 'man_sub', $k, null, 'phrase-1') && $func->checkPermission('news', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                if (($com == 'news') && ($k == $_GET['type'])) {
                                    $active = 'active';
                                    $menuopen = 'menu-open';
                                }
                                ?>
                                <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                                    <a class="nav-link <?= $active ?>" href="#" title="<?= $v['title_main'] ?>">
                                        <i class="nav-icon text-sm fas fa-book"></i>
                                        <p>
                                            <?= $v['title_main'] ?>
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php if (isset($v['list']) && $v['list'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_list', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'news' && ($act == 'man_list' || $act == 'add_list' || $act == 'edit_list' || $kind == 'man_list' || $kind == 'man_list') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_list&type=<?= $k ?>" title="<?=danhmuccap1?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap1?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php if (isset($v['cat']) && $v['cat'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_cat', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'news' && ($act == 'man_cat' || $act == 'add_cat' || $act == 'edit_cat' || $kind == 'man_cat' || $kind == 'man_cat') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_cat&type=<?= $k ?>" title="<?=danhmuccap2?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap2?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php if (isset($v['item']) && $v['item'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_item', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'news' && ($act == 'man_item' || $act == 'add_item' || $act == 'edit_item' || $kind == 'man_item' || $kind == 'man_item') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_item&type=<?= $k ?>" title="<?=danhmuccap3?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap3?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php if (isset($v['sub']) && $v['sub'] == true) {
                                            $none = "";
                                            $active = "";
                                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man_sub', $k, null, 'phrase-1')) $none = "d-none";
                                            if ($com == 'news' && ($act == 'man_sub' || $act == 'add_sub' || $act == 'edit_sub' || $kind == 'man_sub' || $kind == 'man_sub') && $k == $_GET['type']) $active = "active"; ?>
                                            <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man_sub&type=<?= $k ?>" title="<?=danhmuccap4?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                    <p><?=danhmuccap4?></p>
                                                </a></li>
                                        <?php } ?>
                                        <?php
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'news' && ($act == 'man' || $act == 'add' || $act == 'edit' || $act == 'copy' || $kind == 'man') && $k == $_GET['type']) $active = "active";
                                        ?>
                                        <li class="nav-item <?= $none ?>"><a class="nav-link <?= $active ?>" href="index.php?com=news&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main'] ?></p>
                                            </a></li>
                                    </ul>
                                </li>
                    <?php }
                        }
                    } ?>
                <?php } ?>

                <!-- Bài viết (Không cấp) -->
                <?php if (isset($config['shownews']) && $config['shownews'] == true) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man', '', $config['news'], 'phrase-2')) $none = "d-none";
                    if (($com == 'news') && !isset($disabled['news'][$_GET['type']]) && empty($config['news'][$_GET['type']]['dropdown'])) {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=danhsachbaiviet?>">
                            <i class="nav-icon text-sm far fa-newspaper"></i>
                            <p>
                                <?=danhsachbaiviet?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($config['news'] as $k => $v) {
                                if (!isset($disabled['news'][$k]) && empty($v['dropdown'])) { ?>
                                    <?php
                                    $none = "";
                                    $active = "";
                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('news', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                    if ($com == 'news' && ($act == 'man' || $act == 'add' || $act == 'edit' || $act == 'copy' || $kind == 'man') && $k == $_GET['type']) $active = "active";
                                    ?>
                                    <li class="nav-item <?= $none ?>">
                                        <a class="nav-link <?= $active ?>" href="index.php?com=news&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                            <p><?= $v['title_main'] ?></p>
                                        </a>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Cart -->
                <?php if (isset($config['order']['active']) && $config['order']['active'] == true) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('order', 'man', '', null, 'phrase-1')) $none = "d-none";
                    if ($com == 'order') $active = 'active';
                    ?>
                    <li class="nav-item <?= $active ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="index.php?com=order&act=man" title="<?=quanlydonhang?>">
                            <i class="nav-icon text-sm fas fa-shopping-bag"></i>
                            <p><?=quanlydonhang?></p>
                        </a>
                    </li>
                <?php } ?>

                <!-- Tags -->
                <?php if (isset($config['tags'])) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('tags', 'man', '', $config['tags'], 'phrase-2')) $none = "d-none";
                    if ($com == 'tags' && !isset($disabled['tags'][$_GET['type']])) {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=quanlytags?>">
                            <i class="nav-icon text-sm fas fa-tags"></i>
                            <p>
                                <?=quanlytags?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($config['tags'] as $k => $v) {
                                if (!isset($disabled['tags'][$k])) { ?>
                                    <?php
                                    $none = "";
                                    $active = "";
                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('tags', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                    if ($com == 'tags' && $k == $_GET['type']) $active = "active";
                                    ?>
                                    <li class="nav-item <?= $none ?>">
                                        <a class="nav-link <?= $active ?>" href="index.php?com=tags&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                            <p><?= $v['title_main'] ?></p>
                                        </a>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Newsletter -->
                <?php if (isset($config['newsletter'])) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('newsletter', 'man', '', $config['newsletter'], 'phrase-2')) $none = "d-none";
                    if ($com == 'newsletter' && !isset($disabled['newsletter'][$_GET['type']])) {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="nhận tin">
                            <i class="nav-icon text-sm fas fa-envelope"></i>
                            <p>
                                <?=quanlynhantin?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($config['newsletter'] as $k => $v) {
                                if (!isset($disabled['newsletter'][$k])) { ?>
                                    <?php
                                    $none = "";
                                    $active = "";
                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('newsletter', 'man', $k, null, 'phrase-1')) $none = "d-none";
                                    if ($com == 'newsletter' && $k == $_GET['type']) $active = "active";
                                    ?>
                                    <li class="nav-item <?= $none ?>">
                                        <a class="nav-link <?= $active ?>" href="index.php?com=newsletter&act=man&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                            <p><?= $v['title_main'] ?></p>
                                        </a>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Static -->
                <?php if (isset($config['static'])) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('static', 'update', '', $config['static'], 'phrase-2')) $none = "d-none";
                    if ($com == 'static' && !isset($disabled['static'][$_GET['type']])) {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=quanlytrangtinh?>">
                            <i class="nav-icon text-sm fas fa-bookmark"></i>
                            <p>
                                <?=quanlytrangtinh?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($config['static'] as $k => $v) {
                                if (!isset($disabled['static'][$k])) { ?>
                                    <?php
                                    $none = "";
                                    $active = "";
                                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('static', 'update', $k, null, 'phrase-1')) $none = "d-none";
                                    if ($com == 'static' && $k == $_GET['type']) $active = "active";
                                    ?>
                                    <li class="nav-item <?= $none ?>">
                                        <a class="nav-link <?= $active ?>" href="index.php?com=static&act=update&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                            <p><?= $v['title_main'] ?></p>
                                        </a>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Gallery -->
                <?php if (isset($config['photo'])) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('photo', 'photo_static', '', $config['photo']['photo_static'], 'phrase-2') && $func->checkPermission('photo', 'man_photo', '', $config['photo']['man_photo'], 'phrase-2')) $none = "d-none";
                    if ($com == 'photo' && !isset($disabled['photo'][$_GET['type']]) && !isset($disabled['photo_static'][$_GET['type']])) {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=quanlyhinhanhvideo?>">
                            <i class="nav-icon text-sm fas fa-photo-video"></i>
                            <p>
                                <?=quanlyhinhanhvideo?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if (isset($config['photo']['photo_static'])) { ?>
                                <?php foreach ($config['photo']['photo_static'] as $k => $v) {
                                    if (!isset($disabled['photo_static'][$k])) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('photo', 'photo_static', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'photo' && $_GET['type'] == $k && $act == 'photo_static') $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=photo&act=photo_static&type=<?= $k ?>" title="<?= $v['title_main'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>
                            <?php } ?>
                            <?php if (isset($config['photo']['man_photo'])) { ?>
                                <?php foreach ($config['photo']['man_photo'] as $k => $v) {
                                    if (!isset($disabled['photo'][$k])) {
                                        $none = "";
                                        $active = "";
                                        if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('photo', 'man_photo', $k, null, 'phrase-1')) $none = "d-none";
                                        if ($com == 'photo' && $_GET['type'] == $k && ($act == 'man_photo' || $act == 'add_photo' || $act == 'edit_photo')) $active = "active"; ?>
                                        <li class="nav-item <?= $none ?>">
                                            <a class="nav-link <?= $active ?>" href="index.php?com=photo&act=man_photo&type=<?= $k ?>" title="<?= $v['title_main_photo'] ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                                <p><?= $v['title_main_photo'] ?></p>
                                            </a>
                                        </li>
                                <?php }
                                } ?>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Địa điểm -->
                <?php if (isset($config['places']['active']) && $config['places']['active'] == true) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('places', 'man_city', '', null, 'phrase-1') && $func->checkPermission('places', 'man_district', '', null, 'phrase-1') && $func->checkPermission('places', 'man_ward', '', null, 'phrase-1')) $none = "d-none";
                    if ($com == 'places') {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=quanlydiadiem?>">
                            <i class="nav-icon text-sm fas fa-building"></i>
                            <p>
                                <?=quanlydiadiem?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $none = "";
                            $active = "";
                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('places', 'man_city', '', null, 'phrase-1')) $none = "d-none";
                            if ($com == 'places' && ($act == 'man_city' || $act == 'add_city' || $act == 'edit_city')) $active = "active";
                            ?>
                            <li class="nav-item <?= $none ?>">
                                <a class="nav-link <?= $active ?>" href="index.php?com=places&act=man_city" title="<?=tinhthanh?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                    <p><?=tinhthanh?></p>
                                </a>
                            </li>
                            <?php
                            $none = "";
                            $active = "";
                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('places', 'man_district', '', null, 'phrase-1')) $none = "d-none";
                            if ($com == 'places' && ($act == 'man_district' || $act == 'add_district' || $act == 'edit_district')) $active = "active";
                            ?>
                            <li class="nav-item <?= $none ?>">
                                <a class="nav-link <?= $active ?>" href="index.php?com=places&act=man_district" title="<?=quanhuyen?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                    <p><?=quanhuyen?></p>
                                </a>
                            </li>
                            <?php
                            $none = "";
                            $active = "";
                            if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('places', 'man_ward', '', null, 'phrase-1')) $none = "d-none";
                            if ($com == 'places' && ($act == 'man_ward' || $act == 'add_ward' || $act == 'edit_ward')) $active = "active";
                            ?>
                            <li class="nav-item <?= $none ?>">
                                <a class="nav-link <?= $active ?>" href="index.php?com=places&act=man_ward" title="<?=phuongxa?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                    <p><?=phuongxa?></p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <!-- User -->
                <?php if (isset($config['user']['active']) && $config['user']['active'] == true && !$func->checkRole()) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if ($com == 'user' && $act != 'login' && $act != 'logout') {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=quanlyuser?>">
                            <i class="nav-icon text-sm fas fa-users"></i>
                            <p>
                                <?=quanlyuser?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if (isset($config['permission']['active']) && $config['permission']['active'] == true) {
                                $active = "";
                                if ($act == 'permission_group' || $act == 'add_permission_group' || $act == 'edit_permission_group') $active = "active"; ?>
                                <li class="nav-item"><a class="nav-link <?= $active ?>" href="index.php?com=user&act=permission_group" title="<?=nhomquyen?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                    <p><?=nhomquyen?></p>
                                    </a></li>
                            <?php } ?>
                            <?php
                            $active = "";
                            if ($act == 'info_admin') $active = "active";
                            ?>
                            <li class="nav-item"><a class="nav-link <?= $active ?>" href="index.php?com=user&act=info_admin" title="<?=thongtinadmin?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                    <p><?=thongtinadmin?></p>
                                </a></li>
                            <?php if (isset($config['user']['admin']) && $config['user']['admin'] == true) {
                                $active = "";
                                if ($act == 'man_admin' || $act == 'add_admin' || $act == 'edit_admin') $active = "active"; ?>
                                <li class="nav-item"><a class="nav-link <?= $active ?>" href="index.php?com=user&act=man_admin" title="<?=taikhoanadmin?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                        <p><?=taikhoanadmin?></p>
                                    </a></li>
                            <?php } ?>
                            <?php if (isset($config['user']['member']) && $config['user']['member'] == true) {
                                $active = "";
                                if ($com == 'user' && ($act == 'man_member' || $act == 'add_member' || $act == 'edit_member')) $active = "active"; ?>
                                <li class="nav-item"><a class="nav-link <?= $active ?>" href="index.php?com=user&act=man_member" title="<?=taikhoankhach?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                        <p><?=taikhoankhach?></p>
                                    </a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Onesignal -->
                <?php if (isset($config['onesignal']) && $config['onesignal'] == true) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('pushOnesignal', 'man', '', null, 'phrase-1')) $none = "d-none";
                    if ($com == 'pushOnesignal') $active = 'active';
                    ?>
                    <li class="nav-item <?= $active ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="index.php?com=pushOnesignal&act=man" title="<?=quanlythongbaoday?>">
                            <i class="nav-icon text-sm fas fa-bell"></i>
                            <p><?=quanlythongbaoday?></p>
                        </a>
                    </li>
                <?php } ?>

                <!-- SEO page -->
                <?php if (isset($config['seopage']) && count($config['seopage']['page']) > 0) { ?>
                    <?php
                    $none = "";
                    $active = "";
                    $menuopen = "";
                    if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('seopage', 'update', '', $config['seopage']['page'], 'phrase-2')) $none = "d-none";
                    if ($com == 'seopage') {
                        $active = 'active';
                        $menuopen = 'menu-open';
                    }
                    ?>
                    <li class="nav-item has-treeview <?= $menuopen ?> <?= $none ?>">
                        <a class="nav-link <?= $active ?>" href="#" title="<?=quanlyseopage?>">
                            <i class="nav-icon text-sm fas fa-share-alt"></i>
                            <p>
                                <?=quanlyseopage?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($config['seopage']['page'] as $k => $v) { ?>
                                <?php
                                $none = "";
                                $active = "";
                                if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('seopage', 'update', $k, null, 'phrase-1')) $none = "d-none";
                                if ($com == 'seopage' && $k == $_GET['type']) $active = "active";
                                ?>
                                <li class="nav-item <?= $none ?>">
                                    <a class="nav-link <?= $active ?>" href="index.php?com=seopage&act=update&type=<?= $k ?>" title="<?= $v ?>"><i class="nav-icon text-sm far fa-caret-square-right"></i>
                                        <p><?= $v ?></p>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Thiết lập thông tin -->
                <?php
                $none = "";
                $active = "";
                if (isset($is_permission) && $is_permission == true) if ($func->checkPermission('setting', 'update', '', null, 'phrase-1')) $none = "d-none";
                if ($com == 'setting') $active = 'active';
                ?>
                <li class="nav-item <?= $active ?> <?= $none ?>">
                    <a class="nav-link <?= $active ?>" href="index.php?com=setting&act=update" title="<?=thietlapthongtin?>">
                        <i class="nav-icon text-sm fas fa-cogs"></i>
                        <p><?=thietlapthongtin?></p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>