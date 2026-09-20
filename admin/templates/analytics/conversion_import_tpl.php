<?php
$linkImport = "index.php?com=analytics&act=conversion_import";
$linkConversions = "index.php?com=analytics&act=conversions";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-file-import mr-2 text-success"></i>Nhập Báo Cáo Chuyển Đổi CSV (Conversion Import)
                </h1>
                <small class="text-muted">Tải lên file báo cáo hoa hồng từ Shopee, TikTok Shop, Lazada để đối soát và tự động phân bổ doanh thu</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkConversions?>" class="btn btn-sm btn-outline-primary shadow-sm">
                    <i class="fas fa-list mr-1"></i> Danh Sách Đơn Hàng
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <?php if (!empty($uploadError)): ?>
            <div class="alert alert-danger shadow-sm text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i><?=$uploadError?>
            </div>
        <?php endif; ?>

        <!-- Step 1: Upload CSV Form -->
        <div class="card card-outline card-success shadow-sm mb-3">
            <div class="card-header bg-white py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-upload mr-2 text-success"></i>Bước 1: Tải Lên File CSV Báo Cáo Hoa Hồng</h3>
            </div>
            <div class="card-body p-3">
                <form method="POST" action="<?=$linkImport?>" enctype="multipart/form-data" class="row align-items-end">
                    <div class="col-md-4 col-12 mb-3 mb-md-0">
                        <label class="form-label font-weight-bold mb-1">Chọn Sàn TMĐT / Nền Tảng <span class="text-danger">*</span>:</label>
                        <select name="platform" class="form-control form-control-sm" required>
                            <option value="shopee">Shopee Affiliate (Định dạng CSV Shopee)</option>
                            <option value="tiktok_shop">TikTok Shop Affiliate (Định dạng CSV TikTok)</option>
                            <option value="lazada">Lazada Affiliate</option>
                            <option value="tiki">Tiki Affiliate</option>
                            <option value="brand">Thương hiệu trực tiếp / Khác</option>
                        </select>
                    </div>
                    <div class="col-md-5 col-12 mb-3 mb-md-0">
                        <label class="form-label font-weight-bold mb-1">File CSV dữ liệu (.csv, .txt) <span class="text-danger">*</span>:</label>
                        <div class="custom-file">
                            <input type="file" name="csv_file" class="custom-file-input form-control-sm" id="csvFileInput" accept=".csv, .txt" required onchange="document.getElementById('fileLabel').innerText = this.files[0] ? this.files[0].name : 'Chọn file CSV...'">
                            <label class="custom-file-label text-truncate" id="fileLabel" for="csvFileInput">Chọn file CSV...</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <button type="submit" class="btn btn-sm btn-success btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-search mr-1"></i> Tải Lên & Xem Trước (Preview)
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Step 2: Preview Results (If uploaded) -->
        <?php if (!empty($previewResult)): ?>
            <div class="card card-outline card-primary shadow-sm mb-3">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-eye mr-2 text-primary"></i>Bước 2: Kết Quả Kiểm Tra Dữ Liệu (Preview)</h3>
                    <div>
                        <span class="badge badge-success mr-1">Hợp lệ: <?=$previewResult['valid_count']?></span>
                        <span class="badge badge-info mr-1">Khớp nguồn: <?=$previewResult['matched_count']?></span>
                        <span class="badge badge-warning text-dark mr-1">Chưa rõ nguồn: <?=$previewResult['unattributed_count']?></span>
                        <span class="badge badge-secondary mr-1">Trùng lặp: <?=$previewResult['duplicate_count']?></span>
                        <span class="badge badge-danger">Lỗi: <?=$previewResult['invalid_count']?></span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row mb-3">
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-2 bg-light rounded border">
                                <small class="text-muted d-block">Tổng số dòng</small>
                                <h4 class="font-weight-bold mb-0"><?=$previewResult['total_rows']?></h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-2 bg-light rounded border">
                                <small class="text-muted d-block">Đơn hàng hợp lệ</small>
                                <h4 class="font-weight-bold text-success mb-0"><?=$previewResult['valid_count']?></h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-2 bg-light rounded border">
                                <small class="text-muted d-block">Tổng giá trị đơn (GMV)</small>
                                <h4 class="font-weight-bold text-dark mb-0"><?=number_format($previewResult['total_order_value_vnd'])?> đ</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <div class="p-2 bg-light rounded border">
                                <small class="text-muted d-block">Tổng hoa hồng ước tính</small>
                                <h4 class="font-weight-bold text-warning mb-0"><?=number_format($previewResult['total_commission_vnd'])?> đ</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Table -->
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-striped text-sm mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Mã Tracking</th>
                                    <th>Giá trị đơn</th>
                                    <th>Hoa hồng</th>
                                    <th>Trạng thái</th>
                                    <th>Đối soát nguồn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($previewResult['valid_rows'], 0, 50) as $idx => $r): ?>
                                    <tr>
                                        <td><?=($idx + 1)?></td>
                                        <td class="font-weight-bold"><?=$r['external_conversion_id']?></td>
                                        <td><code><?=$r['tracking_code'] ?: 'N/A'?></code></td>
                                        <td><?=number_format($r['order_value'])?> <?=$r['currency']?></td>
                                        <td class="text-success font-weight-bold"><?=number_format($r['commission_value'])?> <?=$r['currency']?></td>
                                        <td><span class="badge badge-light border"><?=$r['status']?></span></td>
                                        <td>
                                            <?php if ($r['is_attributed']): ?>
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Khớp SP #<?=$r['matched_context']['id_product']?></span>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-dark"><i class="fas fa-question mr-1"></i>UNATTRIBUTED</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Confirm Button -->
                    <div class="mt-3 text-right">
                        <form method="POST" action="index.php?com=analytics&act=process_import" class="d-inline">
                            <input type="hidden" name="saved_file_path" value="<?=htmlspecialchars($previewResult['saved_file_path'])?>">
                            <input type="hidden" name="platform" value="<?=htmlspecialchars($previewResult['platform'])?>">
                            <a href="<?=$linkImport?>" class="btn btn-sm btn-secondary mr-2">Hủy bỏ</a>
                            <button type="submit" class="btn btn-sm btn-success font-weight-bold shadow-sm px-4">
                                <i class="fas fa-check-circle mr-1"></i> Xác Nhận & Nhập <?=$previewResult['valid_count']?> Đơn Hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Step 3: Recent Import History -->
        <div class="card card-outline card-secondary shadow-sm">
            <div class="card-header bg-white py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-2 text-secondary"></i>Lịch Sử Nhập Báo Cáo Hoa Hồng</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th>Tên File</th>
                            <th class="text-center" style="width: 120px;">Sàn TMĐT</th>
                            <th class="text-center" style="width: 100px;">Tổng dòng</th>
                            <th class="text-center" style="width: 110px;">Thành công</th>
                            <th class="text-center" style="width: 100px;">Trùng lặp</th>
                            <th class="text-center" style="width: 100px;">Thất bại</th>
                            <th class="text-center" style="width: 110px;">Trạng thái</th>
                            <th class="text-center" style="width: 140px;">Thời điểm</th>
                            <th class="text-center" style="width: 110px;">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($importLogs)): ?>
                            <?php foreach ($importLogs as $k => $log): ?>
                                <tr>
                                    <td class="text-center text-muted"><?=($k + 1)?></td>
                                    <td class="font-weight-bold text-dark"><?=$log['filename']?></td>
                                    <td class="text-center text-uppercase"><span class="badge badge-light border"><?=$log['platform']?></span></td>
                                    <td class="text-center font-weight-bold"><?=$log['total_rows']?></td>
                                    <td class="text-center text-success font-weight-bold"><?=$log['imported_count']?></td>
                                    <td class="text-center text-muted"><?=$log['duplicate_count']?></td>
                                    <td class="text-center text-danger"><?=$log['failed_count']?></td>
                                    <td class="text-center">
                                        <span class="badge <?=($log['status'] === 'SUCCESS') ? 'badge-success' : (($log['status'] === 'PARTIAL') ? 'badge-warning text-dark' : 'badge-danger')?>">
                                            <?=$log['status']?>
                                        </span>
                                    </td>
                                    <td class="text-center small text-muted"><?=date('d/m/Y H:i', $log['date_created'])?></td>
                                    <td class="text-center small"><?=$log['admin_user']?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    Chưa có lịch sử nhập file CSV
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
