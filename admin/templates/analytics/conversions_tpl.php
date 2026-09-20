<?php
$linkConversions = "index.php?com=analytics&act=conversions";
$linkImport = "index.php?com=analytics&act=conversion_import";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-shopping-bag mr-2 text-success"></i>Đơn Hàng & Chuyển Đổi Affiliate (Conversions)
                </h1>
                <small class="text-muted">Quản lý dữ liệu đơn hàng đối soát từ các sàn TMĐT, phân bổ doanh thu và ghép nối nguồn chiến dịch</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkImport?>" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-import mr-1"></i> Nhập File CSV Đối Soát
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-outline card-primary shadow-sm mb-3">
            <div class="card-body p-3">
                <form method="GET" action="index.php" class="row align-items-end">
                    <input type="hidden" name="com" value="analytics">
                    <input type="hidden" name="act" value="conversions">
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <label class="form-label font-weight-bold mb-1">Trạng thái:</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="CONFIRMED" <?=($filterStatus === 'CONFIRMED') ? 'selected' : ''?>>CONFIRMED (Thành công)</option>
                            <option value="PENDING" <?=($filterStatus === 'PENDING') ? 'selected' : ''?>>PENDING (Chờ duyệt)</option>
                            <option value="REVERSED" <?=($filterStatus === 'REVERSED') ? 'selected' : ''?>>REVERSED (Hoàn tiền / Hủy)</option>
                            <option value="CANCELLED" <?=($filterStatus === 'CANCELLED') ? 'selected' : ''?>>CANCELLED (Đã hủy)</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <label class="form-label font-weight-bold mb-1">Sàn TMĐT:</label>
                        <select name="platform" class="form-control form-control-sm">
                            <option value="">-- Tất cả sàn --</option>
                            <option value="shopee" <?=($filterPlatform === 'shopee') ? 'selected' : ''?>>Shopee</option>
                            <option value="tiktok_shop" <?=($filterPlatform === 'tiktok_shop') ? 'selected' : ''?>>TikTok Shop</option>
                            <option value="lazada" <?=($filterPlatform === 'lazada') ? 'selected' : ''?>>Lazada</option>
                            <option value="tiki" <?=($filterPlatform === 'tiki') ? 'selected' : ''?>>Tiki</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <label class="form-label font-weight-bold mb-1">Nguồn phân bổ:</label>
                        <select name="attributed" class="form-control form-control-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="yes" <?=($filterAttributed === 'yes') ? 'selected' : ''?>>Đã gán Sản phẩm / Post</option>
                            <option value="no" <?=($filterAttributed === 'no') ? 'selected' : ''?>>Chưa rõ nguồn (UNATTRIBUTED)</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6 text-right">
                        <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="fas fa-filter mr-1"></i> Lọc</button>
                        <a href="<?=$linkConversions?>" class="btn btn-sm btn-secondary"><i class="fas fa-undo mr-1"></i> Đặt lại</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Conversions Table Card -->
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2 text-primary"></i>Danh Sách Đơn Hàng Đối Soát (<?=count($conversions)?> bản ghi)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th style="min-width: 140px;">Mã đơn / Sàn</th>
                            <th style="min-width: 160px;">Mã Tracking</th>
                            <th style="min-width: 220px;">Sản phẩm / Bài đăng liên kết</th>
                            <th class="text-right" style="width: 130px;">Giá trị đơn (GMV)</th>
                            <th class="text-right" style="width: 130px;">Hoa hồng (VND)</th>
                            <th class="text-center" style="width: 120px;">Trạng thái</th>
                            <th class="text-center" style="width: 130px;">Thời điểm đặt</th>
                            <th class="text-center" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($conversions)): ?>
                            <?php foreach ($conversions as $k => $item): ?>
                                <?php
                                $statusBadge = 'badge-secondary';
                                if ($item['status'] === 'CONFIRMED') $statusBadge = 'badge-success';
                                elseif ($item['status'] === 'PENDING') $statusBadge = 'badge-warning text-dark';
                                elseif ($item['status'] === 'REVERSED') $statusBadge = 'badge-danger';
                                elseif ($item['status'] === 'CANCELLED') $statusBadge = 'badge-dark';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold text-muted"><?=($k + 1)?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?=$item['external_conversion_id']?></div>
                                        <span class="badge badge-light border text-uppercase" style="font-size: 0.7rem;">
                                            <?=$item['platform']?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['tracking_code'])): ?>
                                            <span class="badge badge-light border text-monospace">
                                                <i class="fas fa-tag text-secondary mr-1"></i><?=$item['tracking_code']?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Không có mã</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['product_name'])): ?>
                                            <div class="font-weight-bold text-dark"><?=$item['product_name']?></div>
                                            <?php if (!empty($item['post_title'])): ?>
                                                <small class="text-muted"><i class="fas fa-paper-plane mr-1"></i><?=$item['post_title']?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($item['is_manual_matched'])): ?>
                                                <span class="badge badge-info" style="font-size: 0.65rem;" title="Ghép nối thủ công bởi <?=$item['matched_by']?>"><i class="fas fa-user-edit mr-1"></i>Manual Matched</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-dark font-weight-bold p-1">
                                                <i class="fas fa-question-circle mr-1"></i>UNATTRIBUTED
                                            </span>
                                            <div class="small text-muted">Chưa rõ sản phẩm</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        <?=number_format($item['order_value'])?> <?=$item['currency']?>
                                    </td>
                                    <td class="text-right font-weight-bold text-success">
                                        <?=number_format($item['commission_value'])?> <?=$item['currency']?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?=$statusBadge?> px-2 py-1 font-weight-bold">
                                            <?=$item['status']?>
                                        </span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        <?=date('d/m/Y H:i', $item['conversion_at'])?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-primary" data-toggle="modal" data-target="#matchModal<?=$item['id']?>" title="Ghép nối sản phẩm">
                                            <i class="fas fa-link"></i> Ghép nối
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Ghép Nối Thủ Công -->
                                <div class="modal fade" id="matchModal<?=$item['id']?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <form method="POST" action="index.php?com=analytics&act=manual_match">
                                                <input type="hidden" name="conversion_id" value="<?=$item['id']?>">
                                                <div class="modal-header bg-light py-2">
                                                    <h5 class="modal-title font-weight-bold" style="font-size: 1rem;">
                                                        <i class="fas fa-link mr-2 text-primary"></i>Ghép Nối Nguồn Đơn Hàng #<?=$item['external_conversion_id']?>
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-sm">
                                                    <p class="mb-2 text-muted">Gán đơn hàng từ sàn <strong><?=$item['platform']?></strong> (Hoa hồng: <strong><?=number_format($item['commission_value'])?> <?=$item['currency']?></strong>) vào Sản phẩm và Bài đăng tương ứng:</p>
                                                    <div class="form-group mb-3">
                                                        <label class="font-weight-bold mb-1">Sản phẩm liên kết <span class="text-danger">*</span>:</label>
                                                        <select name="product_id" class="form-control form-control-sm select2" required style="width: 100%;">
                                                            <option value="">-- Chọn sản phẩm --</option>
                                                            <?php foreach ($allProducts as $p): ?>
                                                                <option value="<?=$p['id']?>" <?=($item['id_product'] == $p['id']) ? 'selected' : ''?>>#<?=$p['id']?> - <?=$p['namevi']?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="font-weight-bold mb-1">Bài đăng TikTok liên kết (Tùy chọn):</label>
                                                        <select name="post_id" class="form-control form-control-sm" style="width: 100%;">
                                                            <option value="">-- Không chọn bài đăng --</option>
                                                            <?php foreach ($allPosts as $po): ?>
                                                                <option value="<?=$po['id']?>" <?=($item['id_post'] == $po['id']) ? 'selected' : ''?>>#<?=$po['id']?> - [<?=$po['platform']?>] <?=$po['title']?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold mb-1">Ghi chú đối soát:</label>
                                                        <textarea name="match_notes" rows="2" class="form-control form-control-sm" placeholder="Nhập lý do hoặc bằng chứng đối soát..."><?=$item['match_notes'] ?? ''?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold">
                                                        <i class="fas fa-save mr-1"></i> Lưu Ghép Nối
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-2 d-block text-secondary"></i>
                                    <strong>Chưa có đơn hàng chuyển đổi nào</strong>
                                    <p class="small text-muted mb-0">Vui lòng tải lên file CSV báo cáo hoa hồng từ Shopee, TikTok Shop hoặc Lazada.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
