<?php
$linkMan = "index.php?com=contact&act=man";
$linkSave = "index.php?com=contact&act=save";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>" title="<?=quanlylienhe?>"><?=quanlylienhe?></a></li>
                <li class="breadcrumb-item active"><?=chitietlienhe?></li>
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
                <h3 class="card-title"><?=thongtinlienhe?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12">
                        <label class="d-inline-block align-middle mb-1 mr-2"><?=taptin?>:</label>
                        <?php if (isset($item['file_attach']) && ($item['file_attach'] != '')) { ?>
                            <a class="btn btn-sm bg-gradient-primary text-white d-inline-block align-middle p-2 rounded mb-1" href="<?= UPLOAD_FILE . @$item['file_attach'] ?>" title="<?=downloadtaptinhientai?>"><i class="fas fa-download mr-2"></i><?=downloadtaptinhientai?></a>
                        <?php } else { ?>
                            <a class="bg-gradient-secondary text-white d-inline-block p-2 rounded mb-1" href="#" title="Tập tin trống"><i class="fas fa-download mr-2"></i><?=taptintrong?></a>
                        <?php } ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fullname"><?=hoten?>:</label>
                        <input type="text" class="form-control text-sm" name="data[fullname]" id="fullname" placeholder="<?=hoten?>" value="<?= (!empty($flash->has('fullname'))) ? $flash->get('fullname') : @$item['fullname'] ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control text-sm" name="data[email]" id="email" placeholder="Email" value="<?= (!empty($flash->has('email'))) ? $flash->get('email') : @$item['email'] ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="phone"><?=dienthoai?>:</label>
                        <input type="text" class="form-control text-sm" name="data[phone]" id="phone" placeholder="<?=dienthoai?>" value="<?= (!empty($flash->has('phone'))) ? $flash->get('phone') : @$item['phone'] ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="address"><?=diachi?>:</label>
                        <input type="text" class="form-control text-sm" name="data[address]" id="address" placeholder="<?=diachi?>" value="<?= (!empty($flash->has('address'))) ? $flash->get('address') : @$item['address'] ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="subject"><?=chude?>:</label>
                        <input type="text" class="form-control text-sm" name="data[subject]" id="subject" placeholder="<?=chude?>" value="<?= (!empty($flash->has('subject'))) ? $flash->get('subject') : @$item['subject'] ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content"><?=noidung?>:</label>
                    <textarea class="form-control text-sm" name="data[content]" id="content" rows="5" placeholder="<?=noidung?>" required><?= (!empty($flash->has('content'))) ? $flash->get('content') : @$item['content'] ?></textarea>
                </div>
                <div class="form-group">
                    <label for="notes"><?=ghichu?>:</label>
                    <textarea class="form-control text-sm" name="data[notes]" id="notes" rows="5" placeholder="<?=ghichu?>"><?= (!empty($flash->has('notes'))) ? $flash->get('notes') : @$item['notes'] ?></textarea>
                </div>
                <div class="form-group">
                    <?php $status_array = (!empty($item['status'])) ? explode(',', $item['status']) : array(); ?>
                    <?php if (isset($config['contact']['check'])) {
                        foreach ($config['contact']['check'] as $key => $value) { ?>
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