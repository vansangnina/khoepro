<?php
if (!empty($_GET['changepass']) && ($_GET['changepass'] == 1)) {
    $changepass = "&changepass=1";
} else {
    $changepass = '';
}

$linkSave = "index.php?com=user&act=info_admin" . $changepass;
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active"><?=thongtintaikhoan?></li>
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

        <div class="card card-primary card-outline text-sm">
            <div class="card-header">
                <h3 class="card-title"><?=thongtinadmin?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (!empty($changepass)) { ?>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="old-password"><?=matkhaucu?>:</label>
                            <input type="password" class="form-control text-sm" name="old-password" id="old-password" placeholder="<?matkhaucu?>">
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="new-password">
                                <span class="d-inline-block align-middle"><?=matkhaumoi?>:</span>
                                <span class="text-danger ml-2" id="show-password"></span>
                            </label>
                            <div class="row align-items-center">
                                <div class="col-6"><input type="password" class="form-control text-sm" name="new-password" id="new-password" placeholder="<?=matkhaumoi?>"></div>
                                <div class="col-6"><a class="btn btn-sm bg-gradient-primary text-sm" href="#" onclick="randomPassword()"><i class="fas fa-random mr-2"></i><?=taomatkhau?></a></div>
                            </div>
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="renew-password"><?=nhaplaimatkhaumoi?>:</label>
                            <input type="password" class="form-control text-sm" name="renew-password" id="renew-password" placeholder="<?=nhaplaimatkhaumoi?>">
                        </div>
                    <?php } else { ?>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="username"><?=taikhoan?>: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-sm" name="data[username]" id="username" placeholder="<?=taikhoan?>" value="<?= (!empty($flash->has('username'))) ? $flash->get('username') : @$item['username'] ?>" required>
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="fullname"><?=hoten?>: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-sm" name="data[fullname]" id="fullname" placeholder="Họ tên" value="<?= (!empty($flash->has('fullname'))) ? $flash->get('fullname') : @$item['fullname'] ?>" required>
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control text-sm" name="data[email]" id="email" placeholder="Email" value="<?= (!empty($flash->has('email'))) ? $flash->get('email') : @$item['email'] ?>">
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="phone"><?=dienthoai?>:</label>
                            <input type="text" class="form-control text-sm" name="data[phone]" id="phone" placeholder="<?=dienthoai?>" value="<?= (!empty($flash->has('phone'))) ? $flash->get('phone') : @$item['phone'] ?>">
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="gender"><?=gioitinh?>:</label>
                            <?php $flashGender = $flash->get('gender'); ?>
                            <select class="custom-select text-sm" name="data[gender]" id="gender" required>
                                <option value="">Chọn giới tính</option>
                                <option <?= (!empty($flashGender) && $flashGender == 1) ? 'selected' : ((@$item['gender'] == 1) ? 'selected' : '') ?> value="1"><?=nam?></option>
                                <option <?= (!empty($flashGender) && $flashGender == 2) ? 'selected' : ((@$item['gender'] == 2) ? 'selected' : '') ?> value="2"><?=nu?></option>
                            </select>
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="birthday"><?=ngaysinh?>:</label>
                            <input type="text" class="form-control text-sm max-date" name="data[birthday]" id="birthday" placeholder="<?=ngaysinh?>" value="<?= (!empty($flash->has('birthday'))) ? date("d/m/Y", $flash->get('birthday')) : ((!empty($item['birthday'])) ? date("d/m/Y", $item['birthday']) : '') ?>" required autocomplete="off">
                        </div>
                        <div class="form-group col-xl-4 col-lg-6 col-md-6">
                            <label for="address"><?=diachi?>:</label>
                            <input type="text" class="form-control text-sm" name="data[address]" id="address" placeholder="<?=diachi?>" value="<?= (!empty($flash->has('address'))) ? $flash->get('address') : @$item['address'] ?>" required>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-sm">
            <button type="submit" class="btn btn-sm bg-gradient-primary submit-check" disabled><i class="far fa-save mr-2"></i><?=luu?></button>
            <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i><?=lamlai?></button>
            <input type="hidden" name="id" value="<?= (isset($item['id']) && $item['id'] > 0) ? $item['id'] : '' ?>">
        </div>
    </form>
</section>