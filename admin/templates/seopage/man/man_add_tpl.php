<?php
$linkSave = "index.php?com=seopage&act=save&type=" . $_GET['type'];
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active"><?=quanlyseopage?></li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <form method="post" action="<?= $linkSave ?>" enctype="multipart/form-data">
        <div class="card-footer text-sm sticky-top">
            <button type="submit" class="btn btn-sm bg-gradient-primary"><i class="far fa-save mr-2"></i><?=luu?></button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i><?=lamlai?></button>
        </div>
        <div class="card card-primary card-outline text-sm">
            <div class="card-header">
                <h3 class="card-title"><?=thongtinseopage?> - <?= $config['seopage']['page'][$_GET['type']] ?></h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="upload-file">
                        <p><?=uploadhinhanh?>:</p>
                        <label class="upload-file-label mb-2" for="file">
                            <div class="upload-file-image rounded mb-3">
                                <?= $func->getImage(['class' => 'rounded img-upload', 'sizes' => $config['seopage']['thumb'], 'upload' => UPLOAD_SEOPAGE_L, 'image' => (!empty($item['photo'])) ? $item['photo'] : '', 'alt' => 'Alt Photo']) ?>
                            </div>
                            <div class="custom-file my-custom-file">
                                <input type="file" class="custom-file-input" name="file" id="file" lang="vi">
                                <label class="custom-file-label mb-0" data-browse="Chọn" for="file"><?=chonfile?></label>
                            </div>
                        </label>
                        <strong class="d-block text-sm"><?php echo "Width: " . $config['seopage']['width'] . " px - Height: " . $config['seopage']['height'] . " px (" . $config['seopage']['img_type'] . ")" ?></strong>
                    </div>
                </div>
                <?php
                $seoDB = $item;
                include TEMPLATE . LAYOUT . "seo.php";
                ?>
            </div>
        </div>
        <div class="card-footer text-sm">
            <button type="submit" class="btn btn-sm bg-gradient-primary"><i class="far fa-save mr-2"></i><?=luu?></button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i><?=lamlai?></button>
        </div>
    </form>
</section>