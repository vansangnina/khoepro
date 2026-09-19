

<div class="title-main"><span><?= (!empty($titleCate)) ? $titleCate : @$titleMain ?></span></div>
<div class="row-news row">
    <?php if (isset($news) && count($news) > 0) { ?>
        <?php foreach ($news as $k => $v) { ?>
            <div class="news d-flex flex-wrap col-md-6 pb-3" data-aos="fade-up" data-aos-duration="1000">
                <a class="pic-news scale-img text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                    <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/210x144x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/210x144x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                </a>
                <div class="info-news">
                    <h3>
                        <a class="name-news text-decoration-none text-split" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    </h3>
                    <p class="desc-news text-split mb-0"><?= $v['desc' . $lang] ?></p>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12">
            <div class="alert alert-warning w-100" role="alert">
                <strong><?= khongtimthayketqua ?></strong>
            </div>
        </div>
    <?php } ?>
    <div class="col-12">
        <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
    </div>
</div>