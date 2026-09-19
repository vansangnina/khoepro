
<!-- <div class="row">
    <div class="col-3 position-sticky" style="background:#000;height:100vh;top:40px">Lèt</div>
    <div class="col-6" style="height:150vh;padding-top:60px">
    <?php foreach ($newsHot as $k => $v) { ?>
        <div>
            <div class="news-slick">
                <a class="img scale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                    <img onerror="this.src='<?= THUMBS ?>/150x120x1/assets/images/noimage.png';" src="<?= THUMBS ?>/150x120x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                </a>
                <div class="info">
                    <h3>
                        <a class="name-newshome text-split text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    </h3>
                    <p class="desc-newshome desc-home-cl text-split"><?= $v['desc' . $lang] ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
    <div class="col-3 position-sticky" style="background:#f00;height:100vh;top:40px">right</div>
</div> -->

<div class="wrap-gioithieu wrap-content">
    <div class="box_gioithieu">
        <div class="title_gt">
            <span>Đơn vị phân phối sản phẩm lọc hàng đầu việt nam</span>
            <b>- Cửa hàng lọc thành tâm</b>
        </div>
        <div class="mota_gt d-flex">
                <div class="mota_gt_l"><?= $func->decodeHtmlChars($gioithieu['desc' . $lang]) ?></div>
                <div class="mota_gt_r"><?= $func->decodeHtmlChars($gioithieu['desc' . $lang]) ?></div>
        </div>
        <div class="tieuchi_gt d-flex">
            <?php foreach ($productHot as $k => $v) { if($k<4){ ?>
                <div class="box_tieuchi d-flex">
                    <img class="lazy" data-src="<?= WATERMARK ?>/product/285x285x1/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                    <b><?= $v['name' . $lang] ?></b>
                </div>
            <?php } }?>
        </div>
    </div>
</div>

<?php if (count($brand)) { ?>
    <div class="wrap-brand padding-top-bottom">
        <div class="wrap-content">
            <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:3|margin:10,screen:575|items:4|margin:10,screen:767|items:4|margin:10,screen:991|items:5|margin:10,screen:1199|items:7|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="500" data-autoplayspeed="3500" data-dots="0" data-nav="1" data-navcontainer=".control-brand">
                <?php foreach ($brand as $v) { ?>
                    <div>
                        <a class="brand text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                            <img class="w-100 lazy" onerror="this.src='<?= THUMBS ?>/150x120x2/assets/images/noimage.png';" data-src="<?= THUMBS ?>/150x120x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="control-brand control-owl transition"></div>
        </div>
    </div>
<?php } ?>

<?php if (isset($proListHot) && count($proListHot) > 0) { ?>
    <?php foreach ($proListHot as $k => $v) {
        $proCatHot = $cache->get("select name$lang,photo , slugvi, slugen, id from #_product_cat where id_list = ?  and find_in_set('hienthi',status) order by numb,id desc", array($v['id']), 'result', 7200);
    ?>
        <div class="section-product pad-bottom">
            <div class="wrap-content">
                <div class=" d-flex align-center justify-content-between flex-wrap d-title-choose-list ">
                    <div class="title-product">
                        <h2><?= $v['name' . $lang] ?></h2>
                    </div>
                    <div class=" d-flex align-items-center flex-wrap d-title-choose-cat">
                        <div class="choose_list">
                            <span class="choosed" data-list="<?= $v['id'] ?>" data-cat="0">Tất cả</span>
                            <?php foreach ($proCatHot as $kc => $vc) { ?>
                                <span data-list="<?= $v['id'] ?>" data-cat="<?= $vc['id'] ?>"><?= $vc['name' . $lang] ?></span>
                            <?php } ?>
                        </div>
                        <div class="btn_sp">
                            <a class=" text-decoration-none " href="<?= $v[$sluglang] ?>"><?= xemthem ?> <i class="bi bi-caret-right-fill"></i></a>
                        </div>
                    </div>
                </div>
                <div class="wp_sp_index">
                    <div class="show_padding show_padding<?= $v['id'] ?>" data-list="<?= $v['id'] ?>" data-cat=""></div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php if (count($proListHot)) { ?>
    <div class="w-pronb">
        <div class="wrap-content padding-top-bottom">
            <div class="title-main"><span>Sản phẩm nổi bật</span></div>
            <div class=" d-flex align-items-center justify-content-center flex-wrap ">
                <div class="dm-noibat d-flex flex-wrap align-items-center justify-content-center">
                    <div class="cats-bar">
                        <div class="cats-bar-icon active">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="cats-owl list-hot">
                        <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:2|margin:10,screen:575|items:2|margin:10,screen:767|items:3|margin:10,screen:991|items:4|margin:10,screen:1199|items:4|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="0" data-nav="0" data-navcontainer="">
                            <a class="text-decoration-none" data-id="0" data-tenkhongdau="san-pham">Tất cả</a>
                            <?php foreach ($proListHot as $v) { ?>
                                <a class="text-decoration-none" data-id="<?= $v['id'] ?>" data-tenkhongdau="<?= $v[$sluglang] ?>"><?= $v['name' . $lang] ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="load_ajax_product"></div>
        </div>
    </div>
<?php } ?>

<?php if (count($productHot)) { ?>
    <div class="wrap-product wrap-content">
        <div class="title-main"><span>Sản phẩm nổi bật</span></div>
        <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:2|margin:10,screen:575|items:2|margin:10,screen:767|items:3|margin:10,screen:991|items:4|margin:20,screen:1199|items:4|margin:20" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="0" data-nav="0" data-navcontainer="">
            <?php foreach ($productHot as $k => $v) { ?>
                <div class="box-product">
                    <div class="pic-product">
                        <a class="text-decoration-none scale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                            <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" data-src="<?= WATERMARK ?>/product/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                        </a>
                        <div class="product-tool d-flex align-items-stretch justify-content-between transition mb-0">
                            <a class="product-detail-view text-decoration-none text-hover-main transition" href="<?= $v[$sluglang] ?>" title="Xem chi tiêt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="10" cy="10" r="7" />
                                    <line x1="21" y1="21" x2="15" y2="15" />
                                </svg>
                                <span>Chi tiêt</span>
                            </a>
                            <a class="product-quick-view text-decoration-none text-hover-main transition" data-slug="<?= $v[$sluglang] ?>" title="Xem nhanh">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="12" cy="12" r="2" />
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                </svg>
                                <span>Xem nhanh</span>
                            </a>
                        </div>
                    </div>
                    <h3 class="mb-0"><a class="text-decoration-none text-split name-product" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a></h3>
                    <p class="price-product">
                        <?php if ($v['discount']) { ?>
                            <span class="price-new"><?= $func->formatMoney($v['sale_price']) ?></span>
                            <span class="price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                            <span class="price-per"><?= '-' . $v['discount'] . '%' ?></span>
                        <?php } else { ?>
                            <span class="price-new"><?= ($v['regular_price']) ? $func->formatMoney($v['regular_price']) : lienhe ?></span>
                        <?php } ?>
                    </p>
                    <p class="cart-product d-flex flex-wrap justify-content-between">
                        <span class="cart-add addcart transition" data-id="<?= $v['id'] ?>" data-action="addnow"><?= themvaogiohang ?></span>
                        <span class="cart-buy addcart transition" data-id="<?= $v['id'] ?>" data-action="buynow"><?= muangay ?></span>
                    </p>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<?php if (count($productHot)) { ?>
    <div class="wrap-product wrap-content">
        <div class="title-main"><span>Sản phẩm nổi bật</span></div>
        <div class="paging-product"></div>
    </div>
<?php } ?>

<?php if (count($proListHot)) { ?>
    <?php foreach ($proListHot as $vlist) { ?>
        <div class="wrap-product wrap-content">
            <div class="title-main"><span><?= $vlist['name' . $lang] ?></span></div>
            <div class="paging-product-category paging-product-category-<?= $vlist['id'] ?>" data-list="<?= $vlist['id'] ?>">
            </div>
        </div>
    <?php } ?>
<?php } ?>

<div class="wrap-intro">
    <div class="wrap-content py-4">
        <div class="title-main"><span>Video clip - tin tức</span></div>
        <div class="row">
            <div class="col-md-12">
                <div class=" d-flex flex-wrap align-items-start justify-content-between ">
                    <div class="newshome-intro">
                        <a class="pic-newshome-best scale-img newshome-best" href="<?= $newsHot[0][$sluglang] ?>" title="<?= $newsHot[0]['name' . $lang] ?>">
                            <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/350x250x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/350x250x1/<?= UPLOAD_NEWS_L . $newsHot[0]['photo'] ?>" alt="<?= $newsHot[0]['name' . $lang] ?>" title="<?= $newsHot[0]['name' . $lang] ?>" />
                            <h3 class="name-newshome-news"><?= $newsHot[0]['name' . $lang] ?></h3>
                        </a>
                        
                        <p class="desc-newshome text-split"><?= $newsHot[0]['desc' . $lang] ?></p>
                    </div>
                    <div class="newshome-scroll">
                        <div class="slick-v-3">
                            <?php foreach ($newsHot as $k => $v) { ?>
                                <div>
                                    <div class="news-slick">
                                        <?php if(($k%2)==0){?>
                                            <a class="img scale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                                                <img onerror="this.src='<?= THUMBS ?>/150x120x1/assets/images/noimage.png';" src="<?= THUMBS ?>/150x120x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                                            </a>
                                        <?php }?>
                                        <div class="info <?=(($k%2)==1)?'info_right':''?>">
                                            <h3>
                                                <a class="name-newshome text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                                            </h3>
                                            <p class="desc-newshome desc-home-cl text-split"><?= $v['desc' . $lang] ?></p>
                                        </div>
                                        <?php if(($k%2)==1){?>
                                            <a class="img scale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                                                <img onerror="this.src='<?= THUMBS ?>/150x120x1/assets/images/noimage.png';" src="<?= THUMBS ?>/150x120x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                                            </a>
                                        <?php }?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php /*?>
            <div class="col-md-5">
                <div class="videohome-intro">
                    <?php foreach ($videoHot as $k => $v) { ?>
                        <a class="item-video1 pic-video scale-img text-decoration-none" data-fancybox="video-gallery" href="<?= $func->get_youtube_shorts($v['link_video']) ?>" title="<?= $v['name' . $lang] ?>">
                            <img onerror="this.src='<?= THUMBS ?>/480x360x2/assets/images/noimage.png';" src="https://img.youtube.com/vi/<?= $func->getYoutube($v['link_video']) ?>/0.jpg" alt="<?= $v['name' . $lang] ?>" />
                        </a>
                    <?php break;
                    } ?>
                    <div class="div_hiden">
                        <div class="owl-page owl-carousel owl-theme owl-video" data-items="screen:0|items:3|margin:10,screen:425|items:3|margin:10,screen:575|items:3|margin:10,screen:767|items:3|margin:10,screen:991|items:4|margin:10,screen:1199|items:3|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="0" data-nav="1" data-navcontainer=".control-video">
                            <?php foreach ($videoHot as $k => $v) { ?>
                                <div>
                                    <a class="item-video2 pic-video-2 scale-img text-decoration-none" data-fancybox="video-gallery" href="<?= $v['link_video'] ?>" title="<?= $v['name' . $lang] ?>">
                                        <img onerror="this.src='<?= THUMBS ?>/480x360x2/assets/images/noimage.png';" src="https://img.youtube.com/vi/<?= $func->getYoutube($v['link_video']) ?>/0.jpg" alt="<?= $v['name' . $lang] ?>" />
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php //echo $addons->set('video-fotorama', 'video-fotorama', 4); 
                    ?>
                    <?php //echo $addons->set('video-select', 'video-select', 4); 
                    ?>
                </div>
            </div>
            <?php */?>
        </div>
    </div>
</div>

<?php /*if (count($newsHot)) { ?>
    <div class="wrap-newsnb padding-top-bottom">
        <div class="wrap-content">
            <p class="title-main"><span><?= tintuc ?></span></p>
            <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:2|margin:10,screen:575|items:2|margin:10,screen:767|items:3|margin:10,screen:991|items:4|margin:20,screen:1199|items:4|margin:20" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="0" data-nav="0" data-navcontainer=".control-news">
                <?php foreach ($newsHot as $k => $v) { ?>
                    <div class="item-newsnb">
                        <p class="pic-newsnb">
                            <a class="scale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                                <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/420x288x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/420x288x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                            </a>
                        </p>
                        <div class="info-newsnb">
                            <h3 class="mb-0">
                                <a class="name-newsnb text-split text-decoration-none" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                            </h3>
                            <p class="time-newshome"><?= ngaydang ?>: <?= date("d/m/Y", $v['date_created']) ?></p>
                            <p class="desc-newsnb text-split"><?= $v['desc' . $lang] ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="control-news control-owl transition"></div>
        </div>
    </div>
<?php }*/ ?>

<?php if (count($partner)) { ?>
    <div class="wrap-partner padding-top-bottom">
        <div class="wrap-content">
            <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:3|margin:10,screen:575|items:4|margin:10,screen:767|items:4|margin:10,screen:991|items:5|margin:10,screen:1199|items:7|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="500" data-autoplayspeed="3500" data-dots="0" data-nav="1" data-navcontainer=".control-partner">
                <?php foreach ($partner as $v) { ?>
                    <div>
                        <a class="partner" href="<?= $v['link'] ?>" target="_blank" title="<?= $v['name' . $lang] ?>">
                            <img class="w-100 lazy" onerror="this.src='<?= THUMBS ?>/150x120x2/assets/images/noimage.png';" data-src="<?= THUMBS ?>/150x120x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="control-partner control-owl transition"></div>
        </div>
    </div>
<?php } ?>