<?php
    $productHot = $d->rawQuery("select id, name$lang, slugvi, slugen, photo, regular_price, sale_price, discount from #_product where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status)", array('san-pham'));
    $product_box_html = $func->renderProductBox($productHot);
    $content = $rowDetail['content' . $lang];
    $final_content = str_replace('{{product_box}}', $product_box_html, $content);
?>
<div class="row">
    <div class="col-lg-9 mb-3">
        <h1 class="title-detail-main"><?= htmlspecialchars($rowDetail['name' . $lang]) ?></h1>
        <div class="time-main"><i class="bi bi-calendar-check-fill"></i><span class="mr-2"><?= date("d/m/Y h:i A", $rowDetail['date_created']) ?></span> <i class="bi bi-eye-fill"></i> <?= $rowDetail['view'] ?> <?= luotxem ?></div>
        <?php if (!empty($rowDetail['content' . $lang])) { ?>
            <div class="meta-toc">
                <a class="mucluc-dropdown-list_button"></a>
                <div class="box-readmore">
                    <ul class="toc-list" data-toc="article" data-toc-headings="h1, h2, h3"></ul>
                </div>
            </div>
            <div class="content-main content-text w-clear content-text" id="toc-content">
                <?= htmlspecialchars_decode($final_content) ?>
            </div>
            <div class="share">
                <b><?= chiase ?>:</b>
                <div class="social-plugin w-clear">
                    <?php
                    $params = array();
                    $params['oaid'] = $optsetting['oaidzalo'];
                    echo $func->markdown('social/share', $params);
                    ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning w-100" role="alert">
                <strong><?= noidungdangcapnhat ?></strong>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-3">
        <?php if (!empty($news)) { ?>
            <div class="share othernews mb-3">
                <b><?= tinlienquan ?>:</b>
                <div class="form-row">
                    <?php foreach ($news as $k => $v) { ?>
                        <div class="news-other d-flex flex-wrap col-12 col-lg-12 col-md-6">
                            <a class="scale-img text-decoration-none pic-news-other" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                                <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/210x144x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/210x144x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                            </a>
                            <div class="info-news-other">
                                <h3>
                                    <a class="name-news-other text-decoration-none text-split" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                                </h3>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Article Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= $seo->get('url') ?>"
    },
    "headline": "<?= addslashes($rowDetail['name' . $lang]) ?>",
    "image": [
        "<?= $configBase . UPLOAD_NEWS_L . $rowDetail['photo'] ?>"
    ],
    "datePublished": "<?= !empty($rowDetail['date_created']) ? date('c', $rowDetail['date_created']) : date('c') ?>",
    "dateModified": "<?= !empty($rowDetail['date_updated']) ? date('c', $rowDetail['date_updated']) : (!empty($rowDetail['date_created']) ? date('c', $rowDetail['date_created']) : date('c')) ?>",
    "author": {
        "@type": "Organization",
        "name": "Khỏe Pro",
        "url": "<?= $configBase ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Khỏe Pro",
        "logo": {
            "@type": "ImageObject",
            "url": "<?= $configBase . UPLOAD_PHOTO_L . @$logo['photo'] ?>"
        }
    },
    "description": "<?= addslashes($seo->get('description')) ?>"
}
</script>