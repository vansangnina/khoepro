<header class="fitnado-header">
    <div class="fitnado-wrap fitnado-top">
        <a class="fitnado-logo" href="" title="<?= (!empty($setting['name' . $lang])) ? $setting['name' . $lang] : 'Khỏe Pro' ?>">
            <?php if (!empty($logo['photo'])) { ?>
                <img onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" src="<?= THUMBS ?>/150x60x2/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>" alt="<?= (!empty($setting['name' . $lang])) ? $setting['name' . $lang] : 'Khỏe Pro' ?>" />
                <div class="logo-fallback" style="display:none;">
                    <span class="logo-text">Khỏe Pro</span>
                    <small><?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.' ?></small>
                </div>
            <?php } else { ?>
                <span class="logo-text">Khỏe Pro</span>
                <small><?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.' ?></small>
            <?php } ?>
        </a>

        <div class="fitnado-search">
            <input type="text" id="keyword" placeholder="<?= nhaptukhoatimkiem ?> (sản phẩm, bài viết, video...)" onkeypress="doEnter(event,'keyword');" value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword']) : '' ?>" />
            <button type="button" onclick="onSearch('keyword');" aria-label="Search">⌕</button>
        </div>

        <div class="fitnado-top-action">
            <a href="gio-hang" title="Giỏ hàng / Yêu thích">
                <span>♡</span>
                <span class="top-action-text"><?= (!empty($_SESSION['cart'])) ? 'Giỏ hàng (' . count($_SESSION['cart']) . ')' : 'Yêu thích' ?></span>
            </a>

            <?php if (array_key_exists($loginMember, $_SESSION) && !empty($_SESSION[$loginMember]['active'])) { ?>
                <a href="account/thong-tin" title="Tài khoản">
                    <span>♙</span>
                    <span class="top-action-text">Hi, <?= htmlspecialchars($_SESSION[$loginMember]['username']) ?></span>
                </a>
                <a href="account/dang-xuat" title="<?= dangxuat ?>">
                    <span class="top-action-text">(<?= dangxuat ?>)</span>
                </a>
            <?php } else { ?>
                <a href="account/dang-nhap" title="<?= dangnhap ?>">
                    <span>♙</span>
                    <span class="top-action-text"><?= dangnhap ?></span>
                </a>
            <?php } ?>

            <a href="san-pham" class="fitnado-btn top-action-text">Bắt đầu ngay</a>
        </div>
    </div>
</header>