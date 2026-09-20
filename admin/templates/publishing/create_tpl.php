<?php
$linkMan = "index.php?com=publishing&act=man";
$linkSaveCreate = "index.php?com=publishing&act=save_create";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-plus-circle mr-2 text-success"></i>Tạo Post Package Mới từ Video Đã Duyệt
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <form action="<?=$linkSaveCreate?>" method="POST">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header p-2 bg-success text-white">
                    <span class="font-weight-bold"><i class="fas fa-box-open mr-1"></i> Chọn Video Đã Duyệt & Cấu hình Gói Xuất bản</span>
                </div>
                <div class="card-body p-3">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Chọn Video Đã Được Phê Duyệt (Approved Video): <span class="text-danger">*</span></label>
                        <select name="id_video" class="form-control" required id="video_selector" onchange="updateVideoPreview()">
                            <option value="">-- Chọn Video Thành phẩm (Đã Duyệt) --</option>
                            <?php foreach ($approvedVideos as $v): ?>
                                <option value="<?=$v['id']?>" data-title="<?=htmlspecialchars($v['title'])?>" data-product="<?=htmlspecialchars($v['product_name'])?>" data-duration="<?=round($v['duration_actual'] ?? 0, 1)?>" data-mode="<?=$v['mode'] ?? 'ECONOMY'?>">
                                    [#<?=$v['id']?>] <?=$v['title']?> (SP: <?=$v['product_name']?> | <?=$v['mode']?> | <?=round($v['duration_actual'] ?? 0, 1)?>s)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Chỉ các video đã qua khâu duyệt của Admin con người (APPROVED) mới xuất hiện trong danh sách.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Tiêu đề Gói Xuất bản:</label>
                        <input type="text" name="title" id="post_title" class="form-control" placeholder="Ví dụ: Video Review Đai Cứng FITNADO Pro Lever - TikTok Post" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Nền tảng xuất bản:</label>
                            <select name="platform" class="form-control">
                                <?php foreach (PublishingCenter::PLATFORMS as $pKey => $pInfo): ?>
                                    <option value="<?=$pKey?>" <?= ($pKey === 'tiktok') ? 'selected' : '' ?>><?=$pInfo['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Tài khoản / Kênh đích:</label>
                            <select name="account_id" class="form-control">
                                <?php foreach ($accounts as $acc): ?>
                                    <option value="<?=$acc['id']?>" <?= !empty($acc['is_default']) ? 'selected' : '' ?>>
                                        [<?=$acc['platform']?>] <?=$acc['account_name']?> (<?=$acc['account_handle']?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Phương thức xuất bản:</label>
                            <select name="provider" class="form-control">
                                <option value="manual" selected>Thủ công (Manual Provider - Khuyên dùng)</option>
                                <option value="tiktok_api">TikTok Content Posting API (Nếu có cấu hình)</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Affiliate Disclosure:</label>
                            <input type="text" name="disclosure_text" class="form-control" value="FITNADO Affiliate Partner - Tham khảo kỹ thông số trước khi mua">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nội dung Caption Video:</label>
                        <textarea name="caption" class="form-control" rows="4" placeholder="Nhập mô tả video hoặc để hệ thống tự động điền từ AI Content đã duyệt..."></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Hashtags chiến lược:</label>
                        <input type="text" name="hashtags" class="form-control" placeholder="#fitnado #reviewgym #tapgym #daicung">
                    </div>
                </div>
                <div class="card-footer p-3 bg-light text-right">
                    <button type="submit" class="btn btn-success shadow-sm font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Khởi tạo Post Package (Bản nháp)
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function updateVideoPreview() {
    var sel = document.getElementById('video_selector');
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        var titleInput = document.getElementById('post_title');
        if (!titleInput.value || titleInput.value.indexOf('Post:') === 0) {
            titleInput.value = 'Post: ' + (opt.getAttribute('data-title') || opt.getAttribute('data-product'));
        }
    }
}
</script>
