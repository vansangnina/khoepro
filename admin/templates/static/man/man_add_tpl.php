<?php
$linkSave = "index.php?com=static&act=save&type=" . $type;

if ((isset($config['static'][$type]['images']) && $config['static'][$type]['images'] == true) or (isset($config['static'][$type]['images1']) && $config['static'][$type]['images1'] == true) or (isset($config['static'][$type]['video']) && $config['static'][$type]['video'] == true)) {
    $colLeft = "col-xl-8";
    $colRight = "col-xl-4";
} else {
    $colLeft = "col-12";
    $colRight = "d-none";
}

if (isset($config['static'][$type]['gallery']) && count($config['static'][$type]['gallery']) > 0) {
    foreach ($config['static'][$type]['gallery'] as $key => $value) {
        if ($key == $type) {
            $keyGallery = $key;
            $flagGallery = true;
            break;
        }
    }
}
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active"><?=quanlytrangtinh?></li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <form class="validation-form" novalidate method="post" action="<?= $linkSave ?>" enctype="multipart/form-data">
        <div class="card-footer text-sm sticky-top">
            <button type="submit" class="btn btn-sm bg-gradient-primary submit-check" disabled><i class="far fa-save mr-2"></i><?=luu?></button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i><?=lamlai?></button>
        </div>

        <?= $flash->getMessages('admin') ?>

        <div class="row">
            <div class="<?= $colLeft ?>">
                <div class="card card-primary card-outline text-sm">
                    <div class="card-header">
                        <h3 class="card-title"><?=noidung?> <?= $config['static'][$type]['title_main'] ?></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($config['static'][$type]['file']) && $config['static'][$type]['file'] == true) { ?>
                            <div class="form-group">
                                <div class="upload-file mb-2">
                                    <label class="mb-0 d-block"><?=uploadtaptin?>:</label>
                                    <label class="upload-file-label mb-2" for="file_attach">
                                        <div class="custom-file my-custom-file">
                                            <input type="file" class="custom-file-input" name="file_attach" id="file_attach" lang="vi">
                                            <label class="custom-file-label mb-0" data-browse="Chọn" for="file_attach"><?=chonfile?></label>
                                        </div>
                                    </label>
                                    <?php if (isset($item['file_attach']) && $item['file_attach'] != '') { ?>
                                        <div class="file-uploaded mb-2">
                                            <a class="btn btn-sm bg-gradient-primary text-white d-inline-block align-middle rounded p-2" href="<?= UPLOAD_FILE . @$item['file_attach'] ?>" title="<?=downloadtaptinhientai?>"><i class="fas fa-download mr-2"></i><?=downloadtaptinhientai?></a>
                                        </div>
                                    <?php } ?>
                                    <strong class="d-block text-sm"><?= $config['static'][$type]['file_type'] ?></strong>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <?php $status_array = (!empty($item['status'])) ? explode(',', $item['status']) : array(); ?>
                            <?php if (isset($config['static'][$type]['check'])) {
                                foreach ($config['static'][$type]['check'] as $key => $value) { ?>
                                    <div class="form-group d-inline-block mb-2 mr-2">
                                        <label for="<?= $key ?>-checkbox" class="d-inline-block align-middle mb-0 mr-2"><?= $value ?>:</label>
                                        <div class="custom-control custom-checkbox d-inline-block align-middle">
                                            <input type="checkbox" class="custom-control-input <?= $key ?>-checkbox" name="status[<?= $key ?>]" id="<?= $key ?>-checkbox" <?= (empty($status_array) && empty($item['id']) ? 'checked' : in_array($key, $status_array)) ? 'checked' : '' ?> value="<?= $key ?>">
                                            <label for="<?= $key ?>-checkbox" class="custom-control-label"></label>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                        <?php if (
                            (isset($config['static'][$type]['name']) && $config['static'][$type]['name'] == true) ||
                            (isset($config['static'][$type]['desc']) && $config['static'][$type]['desc'] == true) ||
                            (isset($config['static'][$type]['content']) && $config['static'][$type]['content'] == true)
                        ) { ?>
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-three-tab-lang" role="tablist">
                                        <?php foreach ($config['website']['lang'] as $k => $v) { ?>
                                            <li class="nav-item">
                                                <a class="nav-link <?= ($k == 'vi') ? 'active' : '' ?>" id="tabs-lang" data-toggle="pill" href="#tabs-lang-<?= $k ?>" role="tab" aria-controls="tabs-lang-<?= $k ?>" aria-selected="true"><?= $v ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="card-body card-article">
                                    <div class="tab-content" id="custom-tabs-three-tabContent-lang">
                                        <?php foreach ($config['website']['lang'] as $k => $v) { ?>
                                            <div class="tab-pane fade show <?= ($k == 'vi') ? 'active' : '' ?>" id="tabs-lang-<?= $k ?>" role="tabpanel" aria-labelledby="tabs-lang">
                                                <?php if (isset($config['static'][$type]['name']) && $config['static'][$type]['name'] == true) { ?>
                                                    <div class="form-group">
                                                        <label for="name<?= $k ?>"><?=tieude?> (<?= $k ?>):</label>
                                                        <input type="text" class="form-control for-seo text-sm" name="data[name<?= $k ?>]" id="name<?= $k ?>" placeholder="<?=tieude?> (<?= $k ?>)" value="<?= (!empty($flash->has('name' . $k))) ? $flash->get('name' . $k) : @$item['name' . $k] ?>" required>
                                                    </div>
                                                <?php } ?>
                                                <?php if (isset($config['static'][$type]['desc']) && $config['static'][$type]['desc'] == true) { ?>
                                                    <div class="form-group">
                                                        <label for="desc<?= $k ?>"><?=mota?> (<?= $k ?>):</label>
                                                        <textarea class="form-control for-seo text-sm <?= (isset($config['static'][$type]['desc_cke']) && $config['static'][$type]['desc_cke'] == true) ? 'form-control-ckeditor' : '' ?>" name="data[desc<?= $k ?>]" id="desc<?= $k ?>" rows="5" placeholder="<?=mota?> (<?= $k ?>)"><?= $func->decodeHtmlChars($flash->get('desc' . $k)) ?: $func->decodeHtmlChars(@$item['desc' . $k]) ?></textarea>
                                                    </div>
                                                <?php } ?>
                                                <?php if (isset($config['static'][$type]['content']) && $config['static'][$type]['content'] == true) { ?>
                                                    <div class="form-group">
                                                        <label for="content<?= $k ?>"><?=noidung?> (<?= $k ?>):</label>
                                                        <textarea class="form-control for-seo text-sm <?= (isset($config['static'][$type]['content_cke']) && $config['static'][$type]['content_cke'] == true) ? 'form-control-ckeditor' : '' ?>" name="data[content<?= $k ?>]" id="content<?= $k ?>" rows="5" placeholder="<?=noidung?> (<?= $k ?>)"><?= $func->decodeHtmlChars($flash->get('content' . $k)) ?: $func->decodeHtmlChars(@$item['content' . $k]) ?></textarea>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="<?= $colRight ?>">
                <?php if (isset($config['static'][$type]['video']) && $config['static'][$type]['video'] == true) { ?>
                    <div class="card card-primary card-outline text-sm">
                        <div class="card-header">
                            <h3 class="card-title">Video <?= $config['static'][$type]['title_main'] ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="show-video">
                                <video controls>
                                  <source src="<?=UPLOAD_NEWS.$item['video']?>" id="video_here">
                                    Your browser does not support HTML5 video.
                                </video>
                            </div>
                            <div class="form-group mb-0">
                                <div class="upload-file">
                                    <label class="d-block">Upload video: <strong class="text-danger text-sm">(<?= $config['static'][$type]['video_type'] ?>)</strong></label>
                                    <label class="upload-file-label mb-0 max-width-full" for="video">
                                        <div class="custom-file my-custom-file ">
                                            <input type="file" class="custom-file-input file_upload_video" name="video" accept="video/*" id="video" lang="vi">
                                            <label class="custom-file-label mb-0" data-browse="Chọn" for="video"><?=chonfile?></label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if (isset($config['static'][$type]['images']) && $config['static'][$type]['images'] == true) { ?>
                    <div class="card card-primary card-outline text-sm">
                        <div class="card-header">
                            <h3 class="card-title"><?=hinhanh?> <?= $config['static'][$type]['title_main'] ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            /* Photo detail */
                            $photoDetail = array();
                            $photoDetail['upload'] = UPLOAD_NEWS_L;
                            $photoAction = 'photo';
                            $photoDetail['image'] = (!empty($item)) ? $item['photo'] : '';
                            $photoDetail['dimension'] = "Width: " . $config['static'][$type]['width'] . " px - Height: " . $config['static'][$type]['height'] . " px (" . $config['static'][$type]['img_type'] . ")";

                            /* Image */
                            include TEMPLATE . LAYOUT . "image.php";
                            ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (isset($config['static'][$type]['images1']) && $config['static'][$type]['images1'] == true) { ?>
                    <div class="card card-primary card-outline text-sm">
                        <div class="card-header">
                            <h3 class="card-title"><?=hinhanh?> <?= $config['static'][$type]['title_main'] ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            /* Photo detail */
                            $photoDetail1 = array();
                            $photoDetail1['upload'] = UPLOAD_NEWS_L;
                            $photoAction = 'photo1';
                            $photoDetail1['image'] = (!empty($item)) ? $item['photo1'] : '';
                            $photoDetail1['dimension'] = "Width: " . $config['static'][$type]['width1'] . " px - Height: " . $config['static'][$type]['height1'] . " px (" . $config['static'][$type]['img_type'] . ")";

                            /* Image */
                            include TEMPLATE . LAYOUT . "image1.php";
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if (isset($flagGallery) && $flagGallery == true) { ?>
            <div class="card card-primary card-outline text-sm">
                <div class="card-header">
                    <h3 class="card-title"><?=bosuutap?> <?= $config['static'][$type]['title_main'] ?></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="filer-gallery" class="label-filer-gallery mb-3">Album: (<?= $config['static'][$type]['gallery'][$keyGallery]['img_type_photo'] ?>)</label>
                        <input type="file" name="files[]" id="filer-gallery" multiple="multiple">
                        <input type="hidden" class="col-filer" value="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-6">
                        <input type="hidden" class="act-filer" value="man">
                        <input type="hidden" class="folder-filer" value="news">
                    </div>
                    <?php if (isset($gallery) && count($gallery) > 0) { ?>
                        <div class="form-group form-group-gallery">
                            <label class="label-filer"><?=albumhientai?>:</label>
                            <div class="action-filer mb-3">
                                <a class="btn btn-sm bg-gradient-primary text-white check-all-filer mr-1"><i class="far fa-square mr-2"></i><?=chontatca?></a>
                                <button type="button" class="btn btn-sm bg-gradient-success text-white sort-filer mr-1"><i class="fas fa-random mr-2"></i><?=sapxep?></button>
                                <a class="btn btn-sm bg-gradient-danger text-white delete-all-filer"><i class="far fa-trash-alt mr-2"></i><?=xoatatca?></a>
                            </div>
                            <div class="alert my-alert alert-sort-filer alert-info text-sm text-white bg-gradient-info"><i class="fas fa-info-circle mr-2"></i><?=cothechonnhieuhinhdedichuyen?></div>
                            <div class="jFiler-items my-jFiler-items jFiler-row">
                                <ul class="jFiler-items-list jFiler-items-grid row scroll-bar" id="jFilerSortable">
                                    <?php foreach ($gallery as $v) echo $func->galleryFiler($v['numb'], $v['id'], $v['photo'], $v['namevi'], 'news', 'col-xl-2 col-lg-3 col-md-3 col-sm-4 col-6'); ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if (isset($config['static'][$type]['seo']) && $config['static'][$type]['seo'] == true) { ?>
            <div class="card card-primary card-outline text-sm">
                <div class="card-header">
                    <h3 class="card-title"><?=noidungseo?></h3>
                    <a class="btn btn-sm bg-gradient-success d-inline-block text-white float-right create-seo" title="<?=taoseo?>"><?=taoseo?></a>
                </div>
                <div class="card-body">
                    <?php
                    $seoDB = $seo->getOnDB(0, $com, 'update', $type);
                    include TEMPLATE . LAYOUT . "seo.php";
                    ?>
                </div>
            </div>
        <?php } ?>
        <div class="card-footer text-sm">
            <button type="submit" class="btn btn-sm bg-gradient-primary submit-check" disabled><i class="far fa-save mr-2"></i><?=luu?></button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i><?=lamlai?></button>
        </div>
    </form>
</section>