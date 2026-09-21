

<main class="fitnado-wrap py-4">
    <div class="fitnado-sectionHead">
        <div>
            <h2><?= (!empty($titleCate)) ? '📚 ' . mb_strtoupper($titleCate, 'UTF-8') : '📚 KIẾN THỨC, HƯỚNG DẪN & ĐÁNH GIÁ CHUYÊN SÂU' ?></h2>
            <p>Tổng hợp kinh nghiệm tập luyện, tiêu chuẩn chọn đồ gym và các bài phân tích độc lập.</p>
        </div>
    </div>

    <?php if (isset($news) && count($news) > 0) { ?>
        <div class="fitnado-news-grid">
            <?php foreach ($news as $k => $v) { ?>
                <article class="fitnado-news-card" data-aos="fade-up">
                    <a class="news-card-thumb" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                        <img class="lazy" onerror="this.src='<?= THUMBS ?>/400x250x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/400x250x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                        <span class="news-card-badge"><i class="fa-solid fa-book-open"></i> Bài viết</span>
                    </a>
                    <div class="news-card-body">
                        <div class="news-card-meta">
                            <span class="meta-date"><i class="fa-regular fa-calendar-check"></i> <?= (!empty($v['date_created'])) ? date("d/m/Y", $v['date_created']) : date("d/m/Y") ?></span>
                            <span class="meta-dot">•</span>
                            <span class="meta-read"><i class="fa-regular fa-clock"></i> 4 phút đọc</span>
                        </div>
                        <h3 class="news-card-title">
                            <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                        </h3>
                        <p class="news-card-desc"><?= $v['desc' . $lang] ?></p>
                        <a href="<?= $v[$sluglang] ?>" class="news-card-more">Đọc tiếp <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100 my-4 text-center" role="alert">
            <strong><i class="fa-solid fa-triangle-exclamation"></i> <?= khongtimthayketqua ?></strong>
            <p class="mb-0 mt-2">Chưa có bài viết trong chuyên mục này.</p>
        </div>
    <?php } ?>

    <?php if (!empty($paging)) { ?>
        <div class="pagination-home w-100 my-4 d-flex justify-content-center"><?= $paging ?></div>
    <?php } ?>
</main>