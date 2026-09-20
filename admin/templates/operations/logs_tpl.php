<?php
$linkOverview = "index.php?com=operations&act=overview";
$linkLogs     = "index.php?com=operations&act=logs";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-terminal mr-2 text-dark"></i>Nhật ký Hệ thống & Kiểm toán Bảo mật (Log Center)
                </h1>
                <small class="text-muted">Nhật ký sự kiện được làm sạch tự động (Sanitized) bảo vệ hoàn toàn bí mật & token</small>
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
                    <input type="hidden" name="act" value="logs">

                    <label class="mr-2 font-weight-normal">Mức độ:</label>
                    <select name="level" class="form-control form-control-sm mr-3">
                        <option value="all" <?=($filterLevel === 'all') ? 'selected' : ''?>>-- Tất cả Levels --</option>
                        <option value="CRITICAL" <?=($filterLevel === 'CRITICAL') ? 'selected' : ''?>>CRITICAL</option>
                        <option value="ERROR" <?=($filterLevel === 'ERROR') ? 'selected' : ''?>>ERROR</option>
                        <option value="WARNING" <?=($filterLevel === 'WARNING') ? 'selected' : ''?>>WARNING</option>
                        <option value="INFO" <?=($filterLevel === 'INFO') ? 'selected' : ''?>>INFO</option>
                    </select>

                    <label class="mr-2 font-weight-normal">Phân hệ:</label>
                    <select name="module" class="form-control form-control-sm mr-3">
                        <option value="all" <?=($filterModule === 'all') ? 'selected' : ''?>>-- Tất cả Modules --</option>
                        <option value="research" <?=($filterModule === 'research') ? 'selected' : ''?>>Research</option>
                        <option value="content" <?=($filterModule === 'content') ? 'selected' : ''?>>Content</option>
                        <option value="video" <?=($filterModule === 'video') ? 'selected' : ''?>>Video</option>
                        <option value="publishing" <?=($filterModule === 'publishing') ? 'selected' : ''?>>Publishing</option>
                        <option value="system" <?=($filterModule === 'system') ? 'selected' : ''?>>System</option>
                    </select>

                    <label class="mr-2 font-weight-normal">Từ khóa:</label>
                    <input type="text" name="keyword" class="form-control form-control-sm mr-3" value="<?=htmlspecialchars($keyword)?>" placeholder="Tìm thông điệp...">

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search mr-1"></i> Tìm kiếm
                    </button>
                    <?php if ($filterLevel !== 'all' || $filterModule !== 'all' || !empty($keyword)): ?>
                        <a href="<?=$linkLogs?>" class="btn btn-sm btn-link text-danger ml-2">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0 font-monospace" style="font-size: 0.85rem;">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th style="width: 150px;">Thời gian</th>
                            <th style="width: 100px;">Mức độ</th>
                            <th style="width: 120px;">Module</th>
                            <th>Tiêu đề / Thông điệp (Sanitized)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logsList)): ?>
                            <?php foreach ($logsList as $l): ?>
                                <?php
                                $badge = 'secondary';
                                if ($l['severity'] === 'CRITICAL' || $l['severity'] === 'ERROR') $badge = 'danger';
                                elseif ($l['severity'] === 'WARNING') $badge = 'warning text-dark';
                                elseif ($l['severity'] === 'INFO') $badge = 'info';
                                ?>
                                <tr>
                                    <td class="text-muted">
                                        <?=date('H:i:s d/m/Y', $l['last_seen_at'])?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?=$badge?>"><?=$l['severity']?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-uppercase"><?=$l['module']?></span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?=$l['title']?></div>
                                        <div class="text-muted small"><?=htmlspecialchars($l['message'])?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Không có bản ghi nhật ký nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
