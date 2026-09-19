<div class="title-main"><span><?= @$titleMain ?></span></div>
<?php if (isset($video) && count($video) > 0) { ?>
    <div class="row row-video">
        <?php foreach ($video as $k => $v) { ?>
            <div class="col-6 col-md-3 col-sm-4 mb-3 mg-video">
                <div class="video">
                    <p class="pic-video">
                        <a class="scale-img text-decoration-none" data-fancybox="video" data-src="<?= $func->get_youtube_shorts($v['link_video']) ?>" title="<?= $v['name' . $lang] ?>">
                            <?= $func->getImage(['class' => 'lazy w-100', 'size-error' => '480x360x1', 'url' => 'https://img.youtube.com/vi/' . $func->getYoutube($v['link_video']) . '/0.jpg', 'alt' => $v['name' . $lang]]) ?>
                        </a>
                    </p>
                    <h3>
                        <a class="name-video text-split scale-img text-decoration-none" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    </h3>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
<?php } else { ?>
    <div class="alert alert-warning" role="alert">
        <strong><?= khongtimthayketqua ?></strong>
    </div>
<?php } ?>