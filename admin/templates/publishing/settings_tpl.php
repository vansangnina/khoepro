<?php
$linkMan = "index.php?com=publishing&act=man";
$linkSettings = "index.php?com=publishing&act=settings";
$linkSaveSettings = "index.php?com=publishing&act=save_settings";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-cog mr-2 text-secondary"></i>Cấu hình Xuất bản & TikTok API Status (Publishing Settings)
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Danh sách bài đăng
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <!-- Bảng Trạng thái TikTok Developer App & API -->
        <div class="card card-outline card-dark shadow-sm mb-3">
            <div class="card-header p-2 bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="font-weight-bold"><i class="fab fa-tiktok mr-1"></i> Trạng thái Tích hợp TikTok Content Posting API</span>
                <?php if (!empty($tiktokStatus['configured'])): ?>
                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> CONFIGURED</span>
                <?php else: ?>
                    <span class="badge badge-warning text-dark"><i class="fas fa-exclamation-circle mr-1"></i> NOT CONFIGURED</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12">
                        <h6 class="font-weight-bold text-dark mb-1">
                            Trạng thái hiện tại: 
                            <span class="<?= !empty($tiktokStatus['configured']) ? 'text-success' : 'text-warning' ?>">
                                <?= $tiktokStatus['status'] ?>
                            </span>
                        </h6>
                        <p class="text-muted small mb-0"><?=$tiktokStatus['message']?></p>
                    </div>
                    <div class="col-md-4 col-12 text-md-right mt-2 mt-md-0">
                        <span class="badge badge-success p-2"><i class="fas fa-shield-alt mr-1"></i> Manual Publishing = 100% Usable</span>
                    </div>
                </div>

                <hr class="my-3">

                <div class="alert alert-light border small mb-0">
                    <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-info-circle text-info mr-1"></i> Nguyên tắc Bảo mật & Minh bạch (Business Rule):</h6>
                    <ul class="mb-0 pl-3 text-muted">
                        <li>Hệ thống <strong>không lưu mật khẩu / Client Secret</strong> trên giao diện người dùng.</li>
                        <li>Mọi thông tin kết nối TikTok Developer App được cấu hình phía Server-side (`libraries/config.php`).</li>
                        <li>Khi chưa có API authorization thật, hệ thống tự động sử dụng <strong>Manual Provider</strong> để quy trình xuất bản không bao giờ bị gián đoạn.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Biểu mẫu Cấu hình Xuất bản Chung -->
        <form action="<?=$linkSaveSettings?>" method="POST">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header p-2">
                    <span class="font-weight-bold text-dark"><i class="fas fa-sliders-h mr-1"></i> Cấu hình Quy chuẩn Nội dung & Tiếp thị</span>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Phương thức xuất bản mặc định:</label>
                            <select name="default_provider" class="form-control">
                                <option value="manual" <?= (($publishConfig['default_provider'] ?? '') === 'manual') ? 'selected' : '' ?>>Thủ công (Manual Provider - Khuyên dùng)</option>
                                <option value="tiktok_api" <?= (($publishConfig['default_provider'] ?? '') === 'tiktok_api') ? 'selected' : '' ?>>TikTok API (Nếu có cấu hình)</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Giới hạn số lượng Hashtags tối đa:</label>
                            <input type="number" name="max_hashtags" class="form-control" min="1" max="15" value="<?=$publishConfig['max_hashtags'] ?? 5?>">
                            <small class="form-text text-muted">Tránh spam quá nhiều hashtag làm giảm chất lượng phân phối thuật toán TikTok.</small>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Câu Tuyên bố Tiếp thị Liên kết Mặc định (Affiliate Disclosure):</label>
                        <input type="text" name="default_disclosure" class="form-control" value="<?=htmlspecialchars($publishConfig['default_disclosure'] ?? 'FITNADO Affiliate Partner - Tham khảo kỹ thông số trước khi mua')?>">
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">Tự động gắn mã Tracking UTM:</label>
                            <div class="custom-control custom-switch mt-1">
                                <input type="checkbox" class="custom-control-input" id="enable_utm" name="enable_utm" value="1" <?= !empty($publishConfig['enable_utm']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="enable_utm">Kích hoạt UTM Tracking cho Landing URL</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">UTM Source:</label>
                            <input type="text" name="utm_source" class="form-control" value="<?=htmlspecialchars($publishConfig['utm_source'] ?? 'tiktok')?>">
                        </div>
                        <div class="col-md-4 col-12 form-group mb-3">
                            <label class="font-weight-bold text-dark">UTM Medium:</label>
                            <input type="text" name="utm_medium" class="form-control" value="<?=htmlspecialchars($publishConfig['utm_medium'] ?? 'organic_video')?>">
                        </div>
                    </div>
                </div>
                <div class="card-footer p-3 bg-light text-right">
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Lưu Cấu hình Xuất bản
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
