<?php
$linkOverview = "index.php?com=operations&act=overview";
$linkAlerts   = "index.php?com=operations&act=alerts";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-bell mr-2 text-danger"></i>Cảnh báo & Sự cố Vận hành (Alerts Center)
                </h1>
                <small class="text-muted">Trung tâm sự cố với cơ chế tự động gom trùng lặp (Deduplication) và vòng đời Xử lý</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkOverview?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Tổng quan
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
                    <input type="hidden" name="com" value="operations">
                    <input type="hidden" name="act" value="alerts">

                    <label class="mr-2 font-weight-normal">Trạng thái Sự cố:</label>
                    <select name="status" class="form-control form-control-sm mr-3">
                        <option value="ACTIVE" <?=($filterStatus === 'ACTIVE') ? 'selected' : ''?>>Đang mở (ACTIVE)</option>
                        <option value="ACKNOWLEDGED" <?=($filterStatus === 'ACKNOWLEDGED') ? 'selected' : ''?>>Đã xác nhận (ACKNOWLEDGED)</option>
                        <option value="RESOLVED" <?=($filterStatus === 'RESOLVED') ? 'selected' : ''?>>Đã giải quyết (RESOLVED)</option>
                        <option value="ALL" <?=($filterStatus === 'ALL') ? 'selected' : ''?>>-- Tất cả --</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-filter mr-1"></i> Lọc
                    </button>
                    <?php if ($filterStatus !== 'ACTIVE'): ?>
                        <a href="<?=$linkAlerts?>" class="btn btn-sm btn-link text-danger ml-2">Mặc định</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Alerts Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th style="width: 100px;">Mức độ</th>
                            <th style="width: 110px;">Phân hệ</th>
                            <th>Tiêu đề & Nội dung Sự cố</th>
                            <th class="text-center" style="width: 100px;">Lặp lại</th>
                            <th class="text-center" style="width: 140px;">Gần nhất</th>
                            <th class="text-center" style="width: 110px;">Trạng thái</th>
                            <th class="text-center" style="width: 160px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($alertsList)): ?>
                            <?php foreach ($alertsList as $al): ?>
                                <?php
                                $sBadge = 'secondary';
                                if ($al['severity'] === 'CRITICAL') $sBadge = 'danger font-weight-bold';
                                elseif ($al['severity'] === 'ERROR') $sBadge = 'danger';
                                elseif ($al['severity'] === 'WARNING') $sBadge = 'warning text-dark';
                                elseif ($al['severity'] === 'INFO') $sBadge = 'info';

                                $stBadge = 'danger';
                                if ($al['status'] === 'ACKNOWLEDGED') $stBadge = 'warning text-dark';
                                elseif ($al['status'] === 'RESOLVED') $stBadge = 'success';
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-<?=$sBadge?> px-2 py-1"><?=$al['severity']?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-uppercase font-weight-bold"><?=$al['module']?></span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?=$al['title']?></div>
                                        <small class="text-muted"><?=htmlspecialchars($al['message'])?></small>
                                        <?php if (!empty($al['reference'])): ?>
                                            <div class="text-muted small mt-1">Tham chiếu: <code><?=$al['reference']?></code></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border font-weight-bold"><?=$al['occurrences']?> lần</span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?=date('H:i:s', $al['last_seen_at'])?><br>
                                        <?=date('d/m/Y', $al['last_seen_at'])?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?=$stBadge?> px-2 py-1"><?=$al['status']?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($al['status'] === 'ACTIVE'): ?>
                                            <form action="index.php?com=operations&act=acknowledge_alert" method="POST" class="d-inline">
                                                <input type="hidden" name="alert_id" value="<?=$al['id']?>">
                                                <button type="submit" class="btn btn-xs btn-outline-warning mr-1" title="Xác nhận đã thấy sự cố">
                                                    <i class="fas fa-check"></i> Xác nhận
                                                </button>
                                            </form>
                                            <form action="index.php?com=operations&act=resolve_alert" method="POST" class="d-inline">
                                                <input type="hidden" name="alert_id" value="<?=$al['id']?>">
                                                <button type="submit" class="btn btn-xs btn-outline-success" title="Đánh dấu đã giải quyết">
                                                    <i class="fas fa-check-double"></i> Đóng
                                                </button>
                                            </form>
                                        <?php elseif ($al['status'] === 'ACKNOWLEDGED'): ?>
                                            <form action="index.php?com=operations&act=resolve_alert" method="POST" class="d-inline">
                                                <input type="hidden" name="alert_id" value="<?=$al['id']?>">
                                                <button type="submit" class="btn btn-xs btn-outline-success">
                                                    <i class="fas fa-check-double mr-1"></i> Đóng Sự cố
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Đã giải quyết</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                    <p class="mb-0">Không có cảnh báo nào trong trạng thái này.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
