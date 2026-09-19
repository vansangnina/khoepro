<div class="wrap-user">
    <div class="title-user">
        <span><?= dangky ?></span>
    </div>
    <form class="form-user validation-user" novalidate method="post" action="account/dang-ky" enctype="multipart/form-data">
        <?= $flash->getMessages("frontend") ?>
        <label class="mb-2"><?= hoten ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="fullname"><i class="fa fa-user"></i></span>
            <input type="text" class="form-control text-sm" id="fullname" name="fullname" placeholder="<?= nhaphoten ?>" value="<?= $flash->get('fullname') ?>" required>
            <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>
        </div>
        <label class="mb-2"><?= taikhoan ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="username"><i class="fa fa-user"></i></span>
            <input type="text" class="form-control text-sm" id="username" name="username" placeholder="<?= nhaptaikhoan ?>" value="<?= $flash->get('username') ?>" required>
            <div class="invalid-feedback"><?= vuilongnhaptaikhoan ?></div>
        </div>
        <label class="mb-2"><?= matkhau ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="password"><i class="fa fa-lock"></i></span>
            <input type="password" class="form-control text-sm" id="password" name="password" placeholder="<?= nhapmatkhau ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapmatkhau ?></div>
        </div>
        <label class="mb-2"><?= nhaplaimatkhau ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="repassword"><i class="fa fa-lock"></i></span>
            <input type="password" class="form-control text-sm" id="repassword" name="repassword" placeholder="<?= nhaplaimatkhau ?>" required>
            <div class="invalid-feedback"><?= vuilongnhaplaimatkhau ?></div>
        </div>
        <label class="mb-2"><?= gioitinh ?></label>
        <div class="input-group input-user">
            <?php $flashGender = $flash->get('gender'); ?>
            <div class="radio-user custom-control custom-radio">
                <input type="radio" id="nam" name="gender" class="form-check-input" value="1" <?= ($flashGender == 1) ? 'checked' : '' ?> required>
                <label class="custom-control-label" for="nam"><?= nam ?></label>
            </div>
            <div class="radio-user custom-control custom-radio">
                <input type="radio" id="nu" name="gender" class="form-check-input" value="2" <?= ($flashGender == 2) ? 'checked' : '' ?> required>
                <label class="custom-control-label" for="nu"><?= nu ?></label>
            </div>
        </div>
        <label class="mb-2"><?= ngaysinh ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" ><i class="fa-solid fa-calendar-days"></i></span>
            <input type="text" class="form-control text-sm" id="birthday" name="birthday" placeholder="<?= nhapngaysinh ?>" value="<?= $flash->get('birthday') ?>" required autocomplete="off">
            <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
        </div>
        <label class="mb-2">Email</label>
        <div class="input-group input-user">
            <span class="input-group-text" id="email"><i class="fa fa-envelope"></i></span>
            <input type="email" class="form-control text-sm" id="email" name="email" placeholder="<?= nhapemail ?>" value="<?= $flash->get('email') ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>
        </div>
        <label class="mb-2"><?= dienthoai ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="phone"><i class="fa-solid fa-phone"></i></span>
            <input type="number" class="form-control text-sm" id="phone" name="phone" placeholder="<?= nhapdienthoai ?>" value="<?= $flash->get('phone') ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
        </div>
        <label class="mb-2"><?= diachi ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="address"><i class="fa-solid fa-location-dot"></i></span>
            <input type="text" class="form-control text-sm" id="address" name="address" placeholder="<?= nhapdiachi ?>" value="<?= $flash->get('address') ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>
        </div>
        <div class="button-user">
            <input type="submit" class="btn btn-primary btn-block" name="registration-user" value="<?= dangky ?>" disabled>
        </div>
    </form>
</div>