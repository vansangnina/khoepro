<?php if (!empty($popup)) { ?>
    <!-- Modal popup -->
    <div id="popup" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="popupModalLabel"><?= $popup['name' . $lang] ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <a href="<?= $popup['link'] ?>">
                        <?= $func->getImage(['sizes' => '800x530x1', 'upload' => UPLOAD_PHOTO_L, 'image' => $popup['photo'], 'alt' => 'Popup']) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<!-- Modal quickview -->
<div class="modal fade" id="popup-quickview" tabindex="-1" aria-labelledby="popup-quickviewLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fs-5" id="popup-quickviewLabel"><?= sanpham ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- Modal cart -->
<div class="modal fade" id="popup-cart" tabindex="-1" aria-labelledby="popup-cartLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fs-5" id="popup-cartLabel"><?= giohangcuaban ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade booking" id="popup-booking" tabindex="-1" aria-labelledby="booking-cartLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fs-5" id="booking-cartLabel"><?= datlich ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="validation-newsletter form-booking" novalidate method="post" action="booking" enctype="multipart/form-data">

                    <div class="newsletter-booking">
                        <div class="form-floating form-floating-cus">
                            <input type="text" id="phone-booking" class="form-control text-sm" name="dataBooking[fullname]" placeholder="<?= hoten ?>" required />
                            <label for="fullname-booking"><?= hoten ?></label>
                        </div>
                    </div>
                    <div class="newsletter-booking">
                        <div class="form-floating form-floating-cus">
                            <input type="number" id="phone-booking" class="form-control text-sm" name="dataBooking[phone]" placeholder="<?= dienthoai ?>" required />
                            <label for="phone-booking"><?= dienthoai ?></label>
                        </div>
                    </div>
                    <div class="newsletter-booking">
                        <div class="form-floating form-floating-cus">
                            <input type="date" id="date-booking" class="form-control text-sm" name="dataBooking[ngay]" required />
                            <label for="date-booking">Ngày khám</label>
                        </div>
                    </div>
                    <div class="newsletter-booking">
                        <div class="form-floating form-floating-cus">
                            <input type="time" id="time-booking" class="form-control text-sm" name="dataBooking[gio]" required />
                            <label for="time-booking">Giờ khám</label>
                        </div>
                    </div>
                    <div class="newsletter-booking">
                        <div class="form-floating form-floating-cus">
                            <textarea name="dataBooking[content]" id="content-booking" class="form-control text-sm" placeholder="Vấn đề gặp phải" required></textarea>
                            <label for="content-booking">Vấn đề gặp phải</label>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-dongy">

                            <div class="form-check form-switch">
                                <input name="ok" class="form-check-input" type="checkbox" id="ok_booking">
                                <label class="form-check-label" for="ok_booking">ĐỒNG Ý ĐẶT LỊCH</label>
                            </div>
                            <p class="mb-0 desc-dongy">*Thông tin của bạn sẽ được bảo mật.</p>
                        </div>
                        <div class="booking-button">
                            <input type="submit" class="btn btn-sm bg-default btn-primary " name="submit-booking" value="<?= dangky ?>" disabled>
                        </div>
                    </div>
                    <input type="hidden" class="btn btn-sm" name="recaptcha_response_booking" id="recaptchaResponseBooking">
                    <input type="hidden" name="url-current" value="<?= $func->getCurrentPageURL() ?>">
                </form>
            </div>
        </div>
    </div>
</div>