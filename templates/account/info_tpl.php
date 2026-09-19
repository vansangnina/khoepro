<div class="wrap-user">
    <div class="title-user">
        <span><?= thongtincanhan ?></span>
    </div>
    <form class="form-user validation-user" novalidate method="post" action="account/thong-tin" enctype="multipart/form-data">
        <?= $flash->getMessages("frontend") ?>
        <label class="mb-2"><?= hoten ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="fullname"><i class="fa fa-user"></i></span>
            <input type="text" class="form-control text-sm" id="fullname" name="fullname" placeholder="<?= nhaphoten ?>" value="<?= (!empty($flash->has('fullname'))) ? $flash->get('fullname') : $rowDetail['fullname'] ?>" required>
            <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>
        </div>
        <label class="mb-2"><?= taikhoan ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="username"><i class="fa fa-user"></i></span>
            <input type="text" class="form-control text-sm" id="username" name="username" placeholder="<?= nhaptaikhoan ?>" value="<?= $rowDetail['username'] ?>" readonly required>
        </div>
        <label class="mb-2"><?= matkhaucu ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="old-password"><i class="fa fa-lock"></i></span>
            <input type="password" class="form-control text-sm" id="old-password" name="old-password" placeholder="<?= nhapmatkhaucu ?>">
        </div>
        <label class="mb-2"><?= matkhaumoi ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="new-password"><i class="fa fa-lock"></i></span>
            <input type="password" class="form-control text-sm" id="new-password" name="new-password" placeholder="<?= nhapmatkhaumoi ?>">
        </div>
        <label class="mb-2"><?= nhaplaimatkhaumoi ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="new-password-confirm"><i class="fa fa-lock"></i></span>
            <input type="password" class="form-control text-sm" id="new-password-confirm" name="new-password-confirm" placeholder="<?= nhaplaimatkhaumoi ?>">
        </div>
        <label class="mb-2"><?= gioitinh ?></label>
        <div class="input-group input-user">
            <?php $flashGender = $flash->get('gender'); ?>
            <div class="radio-user form-check">
                <input type="radio" id="nam" name="gender" class="form-check-input" <?= (!empty($flashGender) && $flashGender == 1) ? 'checked' : (($rowDetail['gender'] == 1) ? 'checked' : '') ?> value="1" required>
                <label for="nam"><?= nam ?></label>
            </div>
            <div class="radio-user form-check">
                <input type="radio" id="nu" name="gender" class="form-check-input" <?= (!empty($flashGender) && $flashGender == 2) ? 'checked' : (($rowDetail['gender'] == 2) ? 'checked' : '') ?> value="2" required>
                <label for="nu"><?= nu ?></label>
            </div>
        </div>
        <label class="mb-2"><?= ngaysinh ?></label>
        <div class="input-group input-user">
            <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
            <input type="text" class="form-control text-sm" id="birthday" name="birthday" placeholder="<?= nhapngaysinh ?>" value="<?= (!empty($flash->has('birthday'))) ? $flash->get('birthday') : date("d/m/Y", $rowDetail['birthday']) ?>" required autocomplete="off">
            <div class="invalid-feedback"><?= vuilongnhapngaysinh ?></div>
        </div>
        <label class="mb-2">Email</label>
        <div class="input-group input-user">
            <span class="input-group-text" id="email"><i class="fa fa-envelope"></i></span>
            <input type="email" class="form-control text-sm" id="email" name="email" placeholder="<?= nhapemail ?>" value="<?= (!empty($flash->has('email'))) ? $flash->get('email') : $rowDetail['email'] ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>
        </div>
        <label class="mb-2"><?= dienthoai ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="phone"><i class="fa-solid fa-phone"></i></span>
            <input type="number" class="form-control text-sm" id="phone" name="phone" placeholder="<?= nhapdienthoai ?>" value="<?= (!empty($flash->has('phone'))) ? $flash->get('phone') : $rowDetail['phone'] ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
        </div>
        <label class="mb-2"><?= diachi ?></label>
        <div class="input-group input-user">
            <span class="input-group-text" id="address"><i class="fa-solid fa-location-dot"></i></span>
            <input type="text" class="form-control text-sm" id="address" name="address" placeholder="<?= nhapdiachi ?>" value="<?= (!empty($flash->has('address'))) ? $flash->get('address') : $rowDetail['address'] ?>" required>
            <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>
        </div>
        <div class="button-user">
            <input type="submit" class="btn btn-primary btn-block" name="info-user" value="<?= capnhat ?>" disabled>
        </div>
    </form>
</div>