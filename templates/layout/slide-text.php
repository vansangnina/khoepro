<?php if(count($slider)) { ?>
    <div class="slide-text">
        <?php foreach($slider as $v) { ?>
            <div class="box-slide">
                <a class="slideshow-image" href="<?=$v['link']?>" target="_blank" title="<?=$v['name'.$lang]?>">
                    <picture>
                        <source media="(max-width: 500px)" srcset="<?=THUMBS?>/500x200x1/<?=UPLOAD_PHOTO_L.$v['photo']?>" >
                        <img class="lazy w-100" onerror="this.src='<?=THUMBS?>/1366x600x1/assets/images/noimage.png';" data-src="<?=THUMBS?>/1366x600x1/<?=UPLOAD_PHOTO_L.$v['photo']?>" alt="<?=$v['name'.$lang]?>" title="<?=$v['name'.$lang]?>"/>
                    </picture>
                    <div class="info-slide">
                        <div class="name-slide opacity-0 "><?=$v['name'.$lang]?></div>
                        <div class="desc-slide text-split opacity-0"><?=$v['desc'.$lang]?></div>
                        <div class="views-more-slide d-flex align-items-center justify-content-center hover_xemthem  opacity-0"><?=xemthem?></div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
<?php } ?>