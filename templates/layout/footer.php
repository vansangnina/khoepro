<footer class="fitnado-footer">
    <div class="fitnado-wrap">
        <div class="fitnado-foot">
            <div>
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
                <p style="color:var(--m); margin-top:14px; font-size:14px; line-height:1.6;">
                    <?= (!empty($footer['content' . $lang])) ? strip_tags(html_entity_decode($footer['content' . $lang], ENT_QUOTES, 'UTF-8')) : 'Review chân thực. Gợi ý thông minh. Tập luyện hiệu quả hơn.' ?>
                </p>
            </div>

            <div>
                <b>Về chúng tôi</b>
                <a href="gioi-thieu" title="Câu chuyện thương hiệu">Câu chuyện</a>
                <a href="gioi-thieu" title="Giới thiệu"><?= gioithieu ?></a>
                <a href="lien-he" title="<?= lienhe ?>"><?= lienhe ?></a>
            </div>

            <div>
                <b>Danh mục</b>
                <?php if (!empty($productListMenu)) {
                    foreach ($productListMenu as $k => $v) {
                        if ($k >= 4) break; ?>
                        <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    <?php }
                } else { ?>
                    <a href="san-pham">Tập tạ</a>
                    <a href="san-pham">Phục hồi</a>
                    <a href="san-pham">Thời trang</a>
                <?php } ?>
            </div>

            <div>
                <b>Hỗ trợ</b>
                <?php if (!empty($policy)) {
                    foreach ($policy as $k => $v) {
                        if ($k >= 3) break; ?>
                        <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    <?php }
                } else { ?>
                    <a href="tin-tuc">Câu hỏi thường gặp</a>
                    <a href="tin-tuc">Hướng dẫn mua hàng</a>
                <?php } ?>
            </div>

            <div>
                <b>Kết nối</b>
                <?php if (!empty($social)) { ?>
                    <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
                        <?php foreach ($social as $v) { ?>
                            <a href="<?= $v['link'] ?>" target="_blank" rel="nofollow" title="<?= $v['name' . $lang] ?>" style="display:inline-block; margin:0;">
                                <img onerror="this.style.display='none';" src="<?= THUMBS ?>/28x28x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" width="28" height="28" style="border-radius:4px;" />
                            </a>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <span>TikTok · YouTube</span>
                    <span>Instagram · Facebook</span>
                <?php } ?>
                <span style="font-size:12px; opacity:0.8;">Liên kết có thể là affiliate.</span>
            </div>
        </div>

        <div class="fitnado-footer-powered">
            <div>
                Copyright © <?= date("Y") ?> <?= (!empty($copyright['name' . $lang])) ? $copyright['name' . $lang] : 'FITNADO' ?>. All rights reserved.
            </div>
            <div>
                <span>Đang online: <?= (!empty($online)) ? $online : 1 ?></span> |
                <span>Tổng truy cập: <?= (!empty($counter['total'])) ? number_format($counter['total']) : '500,000+' ?></span>
            </div>
        </div>
    </div>
</footer>
