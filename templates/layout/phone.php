<nav class="fitnado-bottom-bar">
    <a href="" class="<?= ($com == '' || $com == 'index') ? 'active' : '' ?>">
        <span>⌂</span>
        Trang chủ
    </a>
    <a href="san-pham" class="<?= ($com == 'san-pham') ? 'active' : '' ?>">
        <span>☷</span>
        Danh mục
    </a>
    <a href="video" class="<?= ($com == 'video') ? 'active' : '' ?>">
        <span>▶</span>
        Video
    </a>
    <a href="gio-hang" class="<?= ($com == 'gio-hang') ? 'active' : '' ?>">
        <span>♡</span>
        Yêu thích
    </a>
    <?php if (array_key_exists($loginMember, $_SESSION) && !empty($_SESSION[$loginMember]['active'])) { ?>
        <a href="account/thong-tin" class="<?= ($com == 'account') ? 'active' : '' ?>">
            <span>♙</span>
            Tài khoản
        </a>
    <?php } else { ?>
        <a href="account/dang-nhap" class="<?= ($com == 'account') ? 'active' : '' ?>">
            <span>♙</span>
            Tài khoản
        </a>
    <?php } ?>
</nav>