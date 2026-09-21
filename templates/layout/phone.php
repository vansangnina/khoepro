<nav class="fitnado-bottom-bar" id="fitnado-bottom-bar" aria-label="Điều hướng nhanh di động">
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
    <a href="javascript:void(0)" class="btn-bottom-drawer-trigger" id="btn-bottom-drawer" title="Danh mục & Menu">
        <i class="fa-solid fa-bars-staggered"></i>
        <span>Danh mục</span>
    </a>
</nav>