<section class="fitnado-hero">
    <div class="fitnado-wrap fitnado-heroGrid">
        <div class="fitnado-hero-left">
            <small>— <?= (!empty($slogan['name' . $lang])) ? $slogan['name' . $lang] : 'GEAR BETTER. A STRONGER YOU.' ?></small>
            <h1>TÌM ĐÚNG ĐỒ TẬP<br><span>KHỎI MUA NHẦM.</span></h1>
            <p>
                <?= (!empty($gioithieu['desc' . $lang])) ? strip_tags(html_entity_decode($gioithieu['desc' . $lang], ENT_QUOTES, 'UTF-8')) : 'Review, so sánh và test những sản phẩm gym thực tế. Tiết kiệm thời gian, tiền bạc và tập luyện hiệu quả hơn mỗi ngày.' ?>
            </p>

            <div class="fitnado-actions">
                <a href="san-pham" class="fitnado-btn">Khám phá sản phẩm →</a>
                <a href="video" class="fitnado-btn ghost">▶ Xem video</a>
            </div>

            <div class="fitnado-stats">
                <div class="fitnado-stats-item">
                    <b><?= (!empty($countProduct['total']) && $countProduct['total'] > 0) ? number_format($countProduct['total']) . '+' : '2.450+' ?></b>
                    <span>Sản phẩm phân tích</span>
                </div>
                <div class="fitnado-stats-item">
                    <b><?= (!empty($countNews['total']) && $countNews['total'] > 0) ? number_format($countNews['total']) . '+' : '150+' ?></b>
                    <span>Bài review chi tiết</span>
                </div>
                <div class="fitnado-stats-item">
                    <b><?= (!empty($counter['total']) && $counter['total'] > 0) ? number_format($counter['total']) : '500K+' ?></b>
                    <span>Người tập tin tưởng</span>
                </div>
            </div>
        </div>

        <div class="fitnado-visual">
            <?php if (!empty($slider[0]['photo'])) { ?>
                <a href="<?= (!empty($slider[0]['link'])) ? $slider[0]['link'] : 'san-pham' ?>" title="<?= (!empty($slider[0]['name' . $lang])) ? $slider[0]['name' . $lang] : 'FITNADO' ?>">
                    <img onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" src="<?= UPLOAD_PHOTO_L . $slider[0]['photo'] ?>" alt="<?= (!empty($slider[0]['name' . $lang])) ? $slider[0]['name' . $lang] : 'Hero Banner' ?>" />
                    <div style="display:none;">
                        <div class="figure">🏋️</div>
                        <b>BETTER GEAR<br>STRONGER YOU</b>
                    </div>
                </a>
            <?php } else { ?>
                <div>
                    <div class="figure">🏋️</div>
                    <b>BETTER GEAR<br>STRONGER YOU</b>
                </div>
            <?php } ?>
        </div>
    </div>
</section>