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
                <a href="cam-ket-khach-quan" class="topbar-link"><i class="fa-solid fa-handshake"></i> Đánh giá khách quan</a>
                <?php if (!empty($optsetting['hotline'])) { ?>
                    <span class="topbar-sep">|</span>
                    <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline']) ?>" class="topbar-link topbar-hotline"><i class="fa-solid fa-phone"></i> <?= $optsetting['hotline'] ?></a>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="fitnado-wrap fitnado-top">
        <!-- Mobile Left: Hamburger Button -->
        <button type="button" class="fitnado-mobile-toggle d-lg-none" id="btn-toggle-drawer" aria-label="Mở Menu">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>

        <!-- Brand Logo -->
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

        <!-- Pro Search Component (Desktop & Tablet) -->
        <div class="fitnado-search-wrapper">
            <div class="fitnado-search">
                <div class="fitnado-search-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" id="keyword" placeholder="Tìm đai lưng, dây kháng lực, súng massage, thảm tập..." onkeypress="doEnter(event,'keyword');" value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword']) : '' ?>" autocomplete="off" />
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

        <!-- Pro Header Actions (Desktop) -->
        <div class="fitnado-top-action d-none d-lg-flex">
            <a href="so-sanh-san-pham" class="fitnado-action-item" title="So sánh đối đầu">
                <div class="action-icon-wrap">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div class="action-text-wrap">
                    <span class="action-label">Đối đầu</span>
                    <strong class="action-title">So sánh</strong>
                </div>
            </a>

            <a href="danh-gia-review" class="fitnado-action-item" title="Đánh giá & Review đồ tập">
                <div class="action-icon-wrap position-relative">
                    <i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <div class="action-text-wrap">
                    <span class="action-label">Chuyên sâu</span>
                    <strong class="action-title">Review</strong>
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

        <!-- Mobile Right: Quick Actions -->
        <div class="fitnado-mobile-actions d-flex d-lg-none align-items-center">
            <a href="so-sanh-san-pham" class="mobile-action-icon" title="So sánh">
                <i class="fa-solid fa-scale-balanced"></i>
            </a>
            <button type="button" class="mobile-action-icon" id="btn-toggle-mobile-search" aria-label="Tìm kiếm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Quick Search Dropdown / Bar -->
    <div class="fitnado-mobile-search-bar d-lg-none" id="fitnado-mobile-search-box">
        <div class="fitnado-wrap">
            <div class="fitnado-search">
                <div class="fitnado-search-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" id="keyword-mobile" placeholder="Tìm đai lưng, dây kháng lực, súng massage..." onkeypress="doEnter(event,'keyword-mobile');" value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword']) : '' ?>" autocomplete="off" />
                <button type="button" onclick="onSearch('keyword-mobile');" aria-label="Search" class="btn-search-submit">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Offcanvas Drawer Menu -->
<div class="fitnado-drawer-overlay" id="fitnado-drawer-overlay"></div>
<aside class="fitnado-drawer" id="fitnado-mobile-drawer" aria-label="Menu điều hướng">
    <div class="fitnado-drawer-header">
        <div class="drawer-brand">
            <span class="brand-text">Khỏe Pro</span>
            <small class="brand-slogan">Review & Đánh Giá Đồ Tập Chuẩn</small>
        </div>
        <button type="button" class="btn-close-drawer" id="btn-close-drawer" aria-label="Đóng menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Drawer Search Form -->
    <div class="fitnado-drawer-search">
        <div class="drawer-search-box">
            <i class="fa-solid fa-magnifying-glass search-ico"></i>
            <input type="text" id="keyword-drawer" placeholder="Tìm sản phẩm, bài review..." onkeypress="doEnter(event,'keyword-drawer');" value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword']) : '' ?>" autocomplete="off" />
            <button type="button" onclick="onSearch('keyword-drawer');" class="btn-drawer-search"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- Drawer Menu List -->
    <div class="fitnado-drawer-body">
        <ul class="drawer-nav-list">
            <li class="<?= ($com == '' || $com == 'index') ? 'active' : '' ?>">
                <a href="" class="drawer-link"><i class="fa-solid fa-house"></i> <span>Trang chủ</span></a>
            </li>

            <!-- Categories Accordion -->
            <li class="drawer-has-child <?= ($com == 'san-pham') ? 'active' : '' ?>">
                <div class="drawer-link-row">
                    <a href="san-pham" class="drawer-link"><i class="fa-solid fa-dumbbell"></i> <span>Đồ tập Gym & Thể thao</span></a>
                    <?php if (!empty($productListMenu)) { ?>
                        <button type="button" class="drawer-toggle-sub" aria-label="Mở danh mục"><i class="fa-solid fa-chevron-down"></i></button>
                    <?php } ?>
                </div>
                <?php if (!empty($productListMenu)) { ?>
                    <ul class="drawer-sub-menu">
                        <li><a href="san-pham" class="sub-all-link">🔥 Xem tất cả đồ tập</a></li>
                        <?php foreach ($productListMenu as $vlist) { ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <!-- Review Accordion -->
            <li class="drawer-has-child <?= ($com == 'danh-gia-review' || ($com == 'tin-tuc' && !empty($act) && $act == 'review')) ? 'active' : '' ?>">
                <div class="drawer-link-row">
                    <a href="danh-gia-review" class="drawer-link"><i class="fa-solid fa-star-half-stroke"></i> <span>Đánh giá & Review</span></a>
                    <?php if (!empty($newsListMenu)) { ?>
                        <button type="button" class="drawer-toggle-sub" aria-label="Mở danh mục review"><i class="fa-solid fa-chevron-down"></i></button>
                    <?php } ?>
                </div>
                <?php if (!empty($newsListMenu)) { ?>
                    <ul class="drawer-sub-menu">
                        <li><a href="danh-gia-review" class="sub-all-link">⭐ Tất cả bài Review</a></li>
                        <?php foreach ($newsListMenu as $vlist) { ?>
                            <li>
                                <a href="<?= $vlist[$sluglang] ?>"><?= $vlist['name' . $lang] ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>

            <li class="<?= ($com == 'so-sanh-san-pham') ? 'active' : '' ?>">
                <a href="so-sanh-san-pham" class="drawer-link"><i class="fa-solid fa-scale-balanced"></i> <span>So sánh đối đầu</span></a>
            </li>

            <li class="<?= ($com == 'huong-dan-chon-mua') ? 'active' : '' ?>">
                <a href="huong-dan-chon-mua" class="drawer-link"><i class="fa-solid fa-compass"></i> <span>Hướng dẫn chọn mua</span></a>
            </li>

            <li class="<?= ($com == 'kien-thuc-tap-luyen' || ($com == 'tin-tuc' && empty($act))) ? 'active' : '' ?>">
                <a href="kien-thuc-tap-luyen" class="drawer-link"><i class="fa-solid fa-book-open-reader"></i> <span>Kiến thức thể hình</span></a>
            </li>

            <li class="<?= ($com == 'video') ? 'active' : '' ?>">
                <a href="video" class="drawer-link"><i class="fa-solid fa-circle-play"></i> <span>Video 30s Review</span></a>
            </li>

            <li class="<?= ($com == 'gioi-thieu') ? 'active' : '' ?>">
                <a href="gioi-thieu" class="drawer-link"><i class="fa-solid fa-circle-info"></i> <span>Về chúng tôi</span></a>
            </li>

            <li class="<?= ($com == 'lien-he') ? 'active' : '' ?>">
                <a href="lien-he" class="drawer-link"><i class="fa-solid fa-paper-plane"></i> <span>Liên hệ & Hỗ trợ</span></a>
            </li>
        </ul>

        <!-- Trust Signals in Drawer -->
        <div class="drawer-trust-box">
            <div class="trust-pill"><i class="fa-solid fa-shield-check text-success"></i> Đánh giá 100% Khách quan</div>
            <div class="trust-pill"><i class="fa-solid fa-circle-check text-primary"></i> Tiêu chuẩn biên tập độc lập</div>
            <?php if (!empty($optsetting['hotline'])) { ?>
                <a href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline']) ?>" class="drawer-hotline-btn">
                    <i class="fa-solid fa-phone-volume"></i> Hotline: <?= $optsetting['hotline'] ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="fitnado-drawer-footer">
        <?php if (array_key_exists($loginMember, $_SESSION) && !empty($_SESSION[$loginMember]['active'])) { ?>
            <div class="drawer-user-box">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-check text-success"></i>
                    <span class="text-truncate"><?= htmlspecialchars($_SESSION[$loginMember]['username']) ?></span>
                </div>
                <a href="account/dang-xuat" class="text-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a>
            </div>
        <?php } else { ?>
            <a href="account/dang-nhap" class="drawer-login-btn">
                <i class="fa-regular fa-user"></i> <span>Đăng nhập thành viên</span>
            </a>
        <?php } ?>
    </div>
</aside>