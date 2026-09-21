<nav class="fitnado-bottom-bar">
    <a href="" class="<?= ($com == '' || $com == 'index') ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i>
        <span>Trang chủ</span>
    </a>
    <a href="san-pham" class="<?= ($com == 'san-pham') ? 'active' : '' ?>">
        <i class="fa-solid fa-dumbbell"></i>
        <span>Đồ tập</span>
    </a>
    <a href="danh-gia-review" class="<?= ($com == 'danh-gia-review' || ($com == 'tin-tuc' && !empty($act) && $act == 'review')) ? 'active' : '' ?>">
        <i class="fa-solid fa-star-half-stroke"></i>
        <span>Review</span>
    </a>
    <a href="so-sanh-san-pham" class="<?= ($com == 'so-sanh-san-pham') ? 'active' : '' ?>">
        <i class="fa-solid fa-scale-balanced"></i>
        <span>So sánh</span>
    </a>
    <?php if (array_key_exists($loginMember, $_SESSION) && !empty($_SESSION[$loginMember]['active'])) { ?>
        <a href="account/thong-tin" class="<?= ($com == 'account') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-check"></i>
            <span>Tài khoản</span>
        </a>
    <?php } else { ?>
        <a href="account/dang-nhap" class="<?= ($com == 'account') ? 'active' : '' ?>">
            <i class="fa-regular fa-user"></i>
            <span>Tài khoản</span>
        </a>
    <?php } ?>
</nav>