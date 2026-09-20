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
                    <i class="fas fa-lightbulb mr-2 text-warning"></i>Khuyến nghị Tối ưu hóa (Optimization Recommendations)
                </h1>
                <small class="text-muted">Động cơ phân tích hiệu suất sinh đề xuất A/B Testing — AI Recommends, Human Decides</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <form action="index.php?com=optimization&act=generate_recommendations" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm mr-2 font-weight-bold">
                        <i class="fas fa-sync-alt mr-1"></i> Quét & Sinh Khuyến nghị
                    </button>
                </form>
                <a href="<?=$linkExperiments?>" class="btn btn-sm btn-info shadow-sm mr-1">
                    <i class="fas fa-flask mr-1"></i> Danh sách Thử nghiệm
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
                    <input type="hidden" name="act" value="recommendations">

                    <label class="mr-2 font-weight-normal">Trạng thái:</label>
                    <select name="status" class="form-control form-control-sm mr-3">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="PENDING" <?=($filterStatus === 'PENDING') ? 'selected' : ''?>>Chờ duyệt (PENDING)</option>
                        <option value="APPROVED" <?=($filterStatus === 'APPROVED') ? 'selected' : ''?>>Đã duyệt (APPROVED)</option>
                        <option value="EXECUTING" <?=($filterStatus === 'EXECUTING') ? 'selected' : ''?>>Đang thực thi (EXECUTING)</option>
                        <option value="COMPLETED" <?=($filterStatus === 'COMPLETED') ? 'selected' : ''?>>Hoàn tất (COMPLETED)</option>
                        <option value="REJECTED" <?=($filterStatus === 'REJECTED') ? 'selected' : ''?>>Từ chối (REJECTED)</option>
                        <option value="STALE" <?=($filterStatus === 'STALE') ? 'selected' : ''?>>Hết hạn (STALE)</option>
                    </select>

                    <label class="mr-2 font-weight-normal">Loại đề xuất:</label>
                    <select name="type" class="form-control form-control-sm mr-3">
                        <option value="">-- Tất cả loại --</option>
                        <option value="CREATE_NEW_HOOK" <?=($filterType === 'CREATE_NEW_HOOK') ? 'selected' : ''?>>Đổi Hook mới</option>
                        <option value="CREATE_CONTENT_VARIATION" <?=($filterType === 'CREATE_CONTENT_VARIATION') ? 'selected' : ''?>>Biến thể Kịch bản</option>
                        <option value="UPGRADE_TO_HYBRID" <?=($filterType === 'UPGRADE_TO_HYBRID') ? 'selected' : ''?>>Nâng cấp HYBRID</option>
                        <option value="REVIEW_PRODUCT_PAGE" <?=($filterType === 'REVIEW_PRODUCT_PAGE') ? 'selected' : ''?>>Tối ưu Trang & CTA</option>
                        <option value="KEEP_TESTING" <?=($filterType === 'KEEP_TESTING') ? 'selected' : ''?>>Tiếp tục theo dõi</option>
                        <option value="WAIT_FOR_MORE_DATA" <?=($filterType === 'WAIT_FOR_MORE_DATA') ? 'selected' : ''?>>Chờ thêm dữ liệu</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-filter mr-1"></i> Lọc
                    </button>
                    <?php if (!empty($filterStatus) || !empty($filterType)): ?>
                    <a href="<?=$linkRecommendations?>" class="btn btn-sm btn-link text-danger ml-2">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Recommendations Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="text-center" style="width: 60px;">#ID</th>
                            <th>Sản phẩm / Bài đăng gốc</th>
                            <th>Loại Khuyến nghị</th>
                            <th>Biến số Thử nghiệm</th>
                            <th>Lý do & Giả thuyết</th>
                            <th class="text-center">Chi phí ước tính</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Ngày tạo</th>
                            <th class="text-center" style="width: 140px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recommendations)): ?>
                            <?php foreach ($recommendations as $rec): ?>
                                <?php
                                $statusBadge = 'secondary';
                                if ($rec['status'] === 'PENDING') $statusBadge = 'warning text-dark font-weight-bold';
                                elseif ($rec['status'] === 'APPROVED') $statusBadge = 'info';
                                elseif ($rec['status'] === 'EXECUTING') $statusBadge = 'primary';
                                elseif ($rec['status'] === 'COMPLETED') $statusBadge = 'success';
                                elseif ($rec['status'] === 'REJECTED') $statusBadge = 'danger';
                                elseif ($rec['status'] === 'STALE') $statusBadge = 'dark';

                                $typeBadge = 'info';
                                if ($rec['recommendation_type'] === 'UPGRADE_TO_HYBRID') $typeBadge = 'success';
                                elseif ($rec['recommendation_type'] === 'CREATE_NEW_HOOK') $typeBadge = 'warning text-dark';
                                elseif ($rec['recommendation_type'] === 'REVIEW_PRODUCT_PAGE') $typeBadge = 'primary';
                                elseif ($rec['recommendation_type'] === 'WAIT_FOR_MORE_DATA') $typeBadge = 'secondary';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold">#<?=$rec['id']?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark">
                                            <?=htmlspecialchars($rec['product_name'] ?: ('Sản phẩm #' . $rec['id_product']))?>
                                        </div>
                                        <?php if (!empty($rec['post_title'])): ?>
                                        <small class="text-muted d-block text-truncate" style="max-width: 240px;">
                                            <i class="fas fa-share-alt mr-1"></i><?=htmlspecialchars($rec['post_title'])?>
                                        </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?=$typeBadge?> px-2 py-1">
                                            <?=htmlspecialchars($rec['recommendation_type'])?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-dark">
                                            <i class="fas fa-sliders-h mr-1 text-primary"></i><?=htmlspecialchars($rec['proposed_variable'])?>
                                        </span>
                                        <small class="text-muted d-block"><?=htmlspecialchars($rec['target_mode'])?></small>
                                    </td>
                                    <td style="max-width: 320px;">
                                        <div class="text-truncate" title="<?=htmlspecialchars($rec['reason_summary'])?>">
                                            <small class="text-dark font-weight-bold"><?=htmlspecialchars($rec['reason_summary'])?></small>
                                        </div>
                                        <div class="text-truncate text-muted" title="<?=htmlspecialchars($rec['hypothesis'])?>">
                                            <small><i class="fas fa-question-circle mr-1"></i><?=htmlspecialchars($rec['hypothesis'])?></small>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold text-<?=((float)$rec['estimated_cost_vnd'] > 0) ? 'danger' : 'success'?>">
                                        <?=number_format((float)$rec['estimated_cost_vnd'], 0, ',', '.')?> đ
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?=$statusBadge?> px-2 py-1">
                                            <?=$rec['status']?>
                                        </span>
                                    </td>
                                    <td class="text-center text-muted">
                                        <small><?=date('d/m/Y H:i', $rec['date_created'])?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?com=optimization&act=recommendation_detail&id=<?=$rec['id']?>" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i> Chi tiết / Duyệt
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
                                    Chưa có khuyến nghị tối ưu hóa nào. Hãy bấm <strong>"Quét & Sinh Khuyến nghị"</strong> để hệ thống phân tích dữ liệu hiệu suất hiện tại.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
