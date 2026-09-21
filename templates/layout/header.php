<header class="fitnado-header">
    <!-- Top Utility & Trust Bar -->
    <div class="fitnado-topbar">
        <div class="fitnado-wrap fitnado-topbar-inner">
            <div class="fitnado-topbar-left">
                <span class="topbar-badge"><i class="fa-solid fa-shield-check"></i> Độc Lập & Khách Quan</span>
                <span class="topbar-desc d-none d-md-inline">Nền tảng đánh giá, so sánh dụng cụ tập gym & thiết bị thể thao chuẩn xác</span>
            </div>
            <div class="fitnado-topbar-right">
                <a href="phuong-phap-danh-gia" class="topbar-link"><i class="fa-solid fa-circle-check"></i> Tiêu chuẩn biên tập</a>
                <span class="topbar-sep">|</span>
                <a href="minh-bach-lien-ket-affiliate" class="topbar-link"><i class="fa-solid fa-handshake"></i> Minh bạch đối tác</a>
                <?php if (!empty($optsetting['hotline'])) { ?>
                    <span class="topbar-sep">|</span>
                    <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline']) ?>" class="topbar-link topbar-hotline"><i class="fa-solid fa-phone"></i> <?= $optsetting['hotline'] ?></a>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="fitnado-wrap fitnado-top">
        <a class="fitnado-logo" href="" title="<?= (!empty($setting['name' . $lang])) ? $setting['name' . $lang] : 'Khỏe Pro' ?>">
            <?php if (!empty($logo['photo'])) { ?>
                <img onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" src="<?= THUMBS ?>/236x211x2/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>" alt="<?= (!empty($setting['name' . $lang])) ? $setting['name' . $lang] : 'Khỏe Pro' ?>" />
                <div class="logo-fallback" style="display:none;">
                    <span class="logo-text">Khỏe Pro</span>
                    <small><?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.' ?></small>
                </div>
            <?php } else { ?>
                <span class="logo-text">Khỏe Pro</span>
                <small><?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.' ?></small>
            <?php } ?>
        </a>

        <!-- Pro Search Component -->
        <div class="fitnado-search-wrapper">
            <div class="fitnado-search">
                <div class="fitnado-search-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" id="keyword" placeholder="Tìm kiếm đai lưng, dây kháng lực, súng massage, thảm tập..." onkeypress="doEnter(event,'keyword');" value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword']) : '' ?>" autocomplete="off" />
                <button type="button" onclick="onSearch('keyword');" aria-label="Search" class="btn-search-submit">
                    <span>Tìm kiếm</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
            <div class="fitnado-search-tags d-none d-lg-flex">
                <span class="tags-label"><i class="fa-solid fa-fire text-danger"></i> Gợi ý:</span>
                <a href="san-pham?keyword=dai-lung">Đai lưng da</a>
                <a href="san-pham?keyword=day-khang-luc">Dây kháng lực</a>
                <a href="san-pham?keyword=con-lan">Con lăn GRID</a>
                <a href="san-pham?keyword=sung-massage">Súng massage</a>
                <a href="san-pham?keyword=tham-yoga">Thảm Liforme</a>
            </div>
        </div>

        <!-- Pro Header Actions -->
        <div class="fitnado-top-action">
            <a href="so-sanh-san-pham" class="fitnado-action-item" title="So sánh đối đầu">
                <div class="action-icon-wrap">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div class="action-text-wrap">
                    <span class="action-label">Đối đầu</span>
                    <strong class="action-title">So sánh</strong>
                </div>
            </a>

            <a href="gio-hang" class="fitnado-action-item" title="Sản phẩm quan tâm">
                <div class="action-icon-wrap position-relative">
                    <i class="fa-regular fa-heart"></i>
                    <?php if (!empty($_SESSION['cart'])) { ?>
                        <span class="action-badge"><?= count($_SESSION['cart']) ?></span>
                    <?php } ?>
                </div>
                <div class="action-text-wrap">
                    <span class="action-label">Quan tâm</span>
                    <strong class="action-title"><?= (!empty($_SESSION['cart'])) ? 'Giỏ (' . count($_SESSION['cart']) . ')' : 'Yêu thích' ?></strong>
                </div>
            </a>

            <?php if (array_key_exists($loginMember, $_SESSION) && !empty($_SESSION[$loginMember]['active'])) { ?>
                <div class="fitnado-action-item user-logged">
                    <div class="action-icon-wrap">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div class="action-text-wrap">
                        <span class="action-label">Tài khoản</span>
                        <a href="account/thong-tin" class="action-title text-truncate" style="max-width: 90px;"><?= htmlspecialchars($_SESSION[$loginMember]['username']) ?></a>
                    </div>
                    <a href="account/dang-xuat" class="btn-logout" title="<?= dangxuat ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                </div>
            <?php } else { ?>
                <a href="account/dang-nhap" class="fitnado-action-item" title="<?= dangnhap ?>">
                    <div class="action-icon-wrap">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div class="action-text-wrap">
                        <span class="action-label">Thành viên</span>
                        <strong class="action-title"><?= dangnhap ?></strong>
                    </div>
                </a>
            <?php } ?>

            <a href="huong-dan-chon-mua" class="fitnado-btn-cta">
                <i class="fa-solid fa-compass"></i>
                <span>Tư vấn chọn mua</span>
            </a>
        </div>
    </div>
</header>