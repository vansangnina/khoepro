<?php
$linkRecommendations = "index.php?com=optimization&act=recommendations";
$linkExperiments = "index.php?com=optimization&act=experiments";
$linkRules = "index.php?com=optimization&act=rules";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-flask mr-2 text-info"></i>Thử nghiệm A/B Tối ưu hóa (Optimization Experiments)
                </h1>
                <small class="text-muted">Theo dõi và đo lường hiệu quả so sánh giữa Nội dung Gốc (Baseline) và Biến thể mới (Variation)</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkRecommendations?>" class="btn btn-sm btn-outline-warning text-dark font-weight-bold shadow-sm mr-1">
                    <i class="fas fa-lightbulb mr-1"></i> Xem Khuyến nghị
                </a>
                <a href="<?=$linkRules?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-sliders-h mr-1"></i> Cấu hình Quy tắc
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <!-- Filter Bar -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <form action="index.php" method="GET" class="form-inline">
                    <input type="hidden" name="com" value="optimization">
                    <input type="hidden" name="act" value="experiments">

                    <label class="mr-2 font-weight-normal">Trạng thái:</label>
                    <select name="status" class="form-control form-control-sm mr-3">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="RUNNING" <?=($filterStatus === 'RUNNING') ? 'selected' : ''?>>Đang chạy (RUNNING)</option>
                        <option value="APPROVED" <?=($filterStatus === 'APPROVED') ? 'selected' : ''?>>Đã duyệt (APPROVED)</option>
                        <option value="COMPLETED" <?=($filterStatus === 'COMPLETED') ? 'selected' : ''?>>Hoàn tất (COMPLETED)</option>
                        <option value="CANCELLED" <?=($filterStatus === 'CANCELLED') ? 'selected' : ''?>>Đã hủy (CANCELLED)</option>
                    </select>

                    <label class="mr-2 font-weight-normal">Biến số:</label>
                    <select name="variable" class="form-control form-control-sm mr-3">
                        <option value="">-- Tất cả biến số --</option>
                        <option value="HOOK" <?=($filterVariable === 'HOOK') ? 'selected' : ''?>>Hook mở đầu</option>
                        <option value="CTA" <?=($filterVariable === 'CTA') ? 'selected' : ''?>>Kêu gọi hành động (CTA)</option>
                        <option value="SCRIPT" <?=($filterVariable === 'SCRIPT') ? 'selected' : ''?>>Kịch bản tổng thể</option>
                        <option value="VIDEO_STYLE" <?=($filterVariable === 'VIDEO_STYLE') ? 'selected' : ''?>>Chế độ Video (Hybrid/Economy)</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-filter mr-1"></i> Lọc
                    </button>
                    <?php if (!empty($filterStatus) || !empty($filterVariable)): ?>
                    <a href="<?=$linkExperiments?>" class="btn btn-sm btn-link text-danger ml-2">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Experiments Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="text-center" style="width: 60px;">#ID</th>
                            <th>Mã Thử nghiệm / Sản phẩm</th>
                            <th>Biến số Thử nghiệm</th>
                            <th>Bài đăng Gốc (Baseline)</th>
                            <th>Bài đăng Biến thể (Variation)</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Kết luận</th>
                            <th class="text-center">Bắt đầu</th>
                            <th class="text-center" style="width: 140px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($experiments)): ?>
                            <?php foreach ($experiments as $exp): ?>
                                <?php
                                $statusBadge = 'secondary';
                                if ($exp['status'] === 'RUNNING') $statusBadge = 'primary font-weight-bold';
                                elseif ($exp['status'] === 'COMPLETED') $statusBadge = 'success';
                                elseif ($exp['status'] === 'APPROVED') $statusBadge = 'info';

                                $conclusionBadge = 'secondary';
                                if ($exp['result_conclusion'] === 'VARIATION_BETTER') $conclusionBadge = 'success font-weight-bold';
                                elseif ($exp['result_conclusion'] === 'BASELINE_BETTER') $conclusionBadge = 'info';
                                elseif ($exp['result_conclusion'] === 'INSUFFICIENT_DATA') $conclusionBadge = 'warning text-dark';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold">#<?=$exp['id']?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark">
                                            <?=htmlspecialchars($exp['experiment_code'])?>
                                        </div>
                                        <small class="text-muted d-block"><?=htmlspecialchars($exp['product_name'] ?: ('Sản phẩm #' . $exp['id_product']))?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-dark font-weight-bold">
                                            <?=htmlspecialchars($exp['changed_variable'])?>
                                        </span>
                                        <small class="text-muted d-block"><?=htmlspecialchars($exp['target_mode'])?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($exp['base_post_title'])): ?>
                                        <div class="text-truncate font-weight-bold" style="max-width: 180px;">
                                            <?=htmlspecialchars($exp['base_post_title'])?>
                                        </div>
                                        <small class="text-muted"><code><?=$exp['base_tracking_code'] ?: 'No Tracking'?></code></small>
                                        <?php else: ?>
                                        <span class="text-muted font-italic">Không có bài gốc</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($exp['var_post_title'])): ?>
                                        <div class="text-truncate font-weight-bold text-primary" style="max-width: 180px;">
                                            <?=htmlspecialchars($exp['var_post_title'])?>
                                        </div>
                                        <small class="text-muted"><code><?=$exp['var_tracking_code'] ?: 'No Tracking'?></code></small>
                                        <?php else: ?>
                                        <span class="badge badge-secondary">Đang tạo biến thể</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?=$statusBadge?> px-2 py-1">
                                            <?=$exp['status']?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($exp['result_conclusion'])): ?>
                                        <span class="badge badge-<?=$conclusionBadge?> px-2 py-1">
                                            <?=$exp['result_conclusion']?>
                                        </span>
                                        <?php else: ?>
                                        <span class="text-muted font-italic">Đang đo lường</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">
                                        <small><?=!empty($exp['started_at']) ? date('d/m/Y', $exp['started_at']) : 'N/A'?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?com=optimization&act=experiment_detail&id=<?=$exp['id']?>" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-chart-bar mr-1"></i> So sánh & Đánh giá
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-flask fa-3x mb-3 text-secondary d-block"></i>
                                    Chưa có thử nghiệm nào được kích hoạt. Hãy xem mục <strong>"Khuyến nghị Tối ưu"</strong> và phê duyệt để khởi tạo thử nghiệm A/B đầu tiên.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
