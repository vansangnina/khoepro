<?php
$linkMan = "index.php?com=pushOnesignal&act=man";
$linkSave = "index.php?com=pushOnesignal&act=save";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active"><?=chitietthongbaoday?></li>
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
            <a class="btn btn-sm bg-gradient-danger" href="<?= $linkMan ?>" title="<?=thoat?>"><i class="fas fa-sign-out-alt mr-2"></i><?=thoat?></a>
        </div>

        <?= $flash->getMessages('admin') ?>

        <div class="card card-primary card-outline text-sm">
            <div class="card-header">
                <h3 class="card-title"><?= ($act == "edit") ? capnhat : themmoi; ?></h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="upload-file">
                        <p><?=uploadhinhanh?>:</p>
                        <label class="upload-file-label mb-2" for="file">
                            <div class="upload-file-image rounded mb-3">
                                <?= $func->getImage(['class' => 'rounded img-upload', 'size-error' => '250x250x1', 'upload' => UPLOAD_SYNC_L, 'image' => (!empty($item['photo'])) ? $item['photo'] : '', 'alt' => 'Alt Photo']) ?>
                            </div>
                            <div class="custom-file my-custom-file">
                                <input type="file" class="custom-file-input" name="file" id="file" lang="vi">
                                <label class="custom-file-label mb-0" data-browse="Chọn" for="file"><?=chonfile?></label>
                            </div>
                        </label>
                        <strong class="d-block text-sm">Width: 100px - Height: 100px (.jpg|.gif|.png|.jpeg|.gif)</strong>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name"><?=tieude?>:</label>
                    <input type="text" class="form-control text-sm" name="data[name]" id="name" placeholder="<?=tieude?>" value="<?= (!empty($flash->has('name'))) ? $flash->get('name') : @$item['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="link">Link:</label>
                    <input type="text" class="form-control text-sm" name="data[link]" id="link" placeholder="Link" value="<?= (!empty($flash->has('link'))) ? $flash->get('link') : @$item['link'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="description"><?=mota?>:</label>
                    <textarea class="form-control text-sm" name="data[description]" id="description" rows="5" placeholder="<?=mota?>" required><?= $func->decodeHtmlChars($flash->get('description')) ?: $func->decodeHtmlChars(@$item['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="numb" class="d-inline-block align-middle mb-0 mr-2"><?=sothutu?>:</label>
                    <input type="number" class="form-control form-control-mini d-inline-block align-middle text-sm" min="0" name="data[numb]" id="numb" placeholder="<?=sothutu?>" value="<?= isset($item['numb']) ? $item['numb'] : 1 ?>">
                </div>
            </div>
        </div>
        <div class="card-footer text-sm">
            <button type="submit" class="btn btn-sm bg-gradient-primary submit-check" disabled><i class="far fa-save mr-2"></i><?=luu?></button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i><?=lamlai?></button>
            <a class="btn btn-sm bg-gradient-danger" href="<?= $linkMan ?>" title="<?=thoat?>"><i class="fas fa-sign-out-alt mr-2"></i><?=thoat?></a>
            <input type="hidden" name="id" value="<?= (isset($item['id']) && $item['id'] > 0) ? $item['id'] : '' ?>">
        </div>
    </form>
</section>