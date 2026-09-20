<?php
$linkOverview = "index.php?com=operations&act=overview";
$linkJobs     = "index.php?com=operations&act=jobs";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-tasks mr-2 text-info"></i>Quản lý Tác vụ & Hàng đợi (Jobs & Queues)
                </h1>
                <small class="text-muted">Theo dõi, kiểm tra chi tiết, Safe Retry và Hủy bỏ tác vụ tập trung toàn hệ thống</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkOverview?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <!-- Stuck Jobs Alert if any -->
        <?php if (!empty($stuckJobs)): ?>
            <div class="alert alert-warning shadow-sm border-0 mb-3">
                <h5><i class="icon fas fa-exclamation-triangle"></i> PHÁT HIỆN TÁC VỤ KẸT (STUCK JOBS DETECTED)!</h5>
                Có <?=count($stuckJobs)?> tác vụ đang ở trạng thái RUNNING/PROCESSING vượt quá thời gian cho phép.
            </div>
        <?php endif; ?>

        <!-- Queue Summary Mini Cards -->
        <div class="row mb-3">
            <?php foreach ($queueSummaries as $mod => $qs): ?>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card shadow-sm border-0 mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted text-uppercase small font-weight-bold"><?=$mod?> Queue</div>
                                    <div class="h5 font-weight-bold text-dark mb-0">
                                        <?=$qs['pending']?> <small class="text-muted">chờ</small> / <?=$qs['processing']?> <small class="text-info">chạy</small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-danger"><?=$qs['failed']?> lỗi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Filter Bar -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <form action="index.php" method="GET" class="form-inline">
                    <input type="hidden" name="com" value="operations">
                    <input type="hidden" name="act" value="jobs">

                    <label class="mr-2 font-weight-normal">Phân hệ (Module):</label>
                    <select name="module" class="form-control form-control-sm mr-3">
                        <option value="all" <?=($filterModule === 'all') ? 'selected' : ''?>>-- Tất cả Modules --</option>
                        <option value="research" <?=($filterModule === 'research') ? 'selected' : ''?>>Research (Nghiên cứu)</option>
                        <option value="content" <?=($filterModule === 'content') ? 'selected' : ''?>>Content (Nội dung AI)</option>
                        <option value="video" <?=($filterModule === 'video') ? 'selected' : ''?>>Video (Sản xuất Video)</option>
                        <option value="publishing" <?=($filterModule === 'publishing') ? 'selected' : ''?>>Publishing (Xuất bản)</option>
                    </select>

                    <label class="mr-2 font-weight-normal">Trạng thái:</label>
                    <select name="status" class="form-control form-control-sm mr-3">
                        <option value="all" <?=($filterStatus === 'all') ? 'selected' : ''?>>-- Tất cả trạng thái --</option>
                        <option value="PENDING" <?=($filterStatus === 'PENDING') ? 'selected' : ''?>>PENDING (Đang chờ)</option>
                        <option value="RUNNING" <?=($filterStatus === 'RUNNING') ? 'selected' : ''?>>RUNNING (Đang xử lý)</option>
                        <option value="SUCCESS" <?=($filterStatus === 'SUCCESS') ? 'selected' : ''?>>SUCCESS (Thành công)</option>
                        <option value="FAILED" <?=($filterStatus === 'FAILED') ? 'selected' : ''?>>FAILED (Thất bại)</option>
                        <option value="CANCELLED" <?=($filterStatus === 'CANCELLED') ? 'selected' : ''?>>CANCELLED (Đã hủy)</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-filter mr-1"></i> Lọc
                    </button>
                    <?php if ($filterModule !== 'all' || $filterStatus !== 'all'): ?>
                        <a href="<?=$linkJobs?>" class="btn btn-sm btn-link text-danger ml-2">Xóa bộ lọc</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="text-center" style="width: 70px;">#ID</th>
                            <th>Phân hệ / Loại tác vụ</th>
                            <th>Provider / Model</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Số lần thử</th>
                            <th class="text-center">Thời lượng</th>
                            <th>Lỗi / Thông điệp</th>
                            <th class="text-center">Thời điểm tạo</th>
                            <th class="text-center" style="width: 140px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jobList['items'])): ?>
                            <?php foreach ($jobList['items'] as $job): ?>
                                <?php
                                $badge = 'secondary';
                                if ($job['status'] === 'SUCCESS' || $job['status'] === 'PUBLISHED') $badge = 'success';
                                elseif ($job['status'] === 'RUNNING' || $job['status'] === 'PUBLISHING') $badge = 'primary';
                                elseif ($job['status'] === 'PENDING' || $job['status'] === 'SCHEDULED' || $job['status'] === 'READY') $badge = 'warning text-dark font-weight-bold';
                                elseif ($job['status'] === 'FAILED') $badge = 'danger font-weight-bold';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold">
                                        #<?=$job['id']?>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-uppercase font-weight-bold"><?=$job['module']?></span>
                                        <div class="font-weight-bold text-dark"><?=$job['type']?></div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border"><?=$job['provider'] ?: 'default'?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?=$badge?> px-2 py-1"><?=$job['status']?></span>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?=$job['attempts']?>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?=$job['duration'] > 0 ? $job['duration'] . 's' : '-'?>
                                    </td>
                                    <td>
                                        <?php if (!empty($job['error_message'])): ?>
                                            <span class="text-danger small" title="<?=htmlspecialchars($job['error_message'])?>">
                                                <?=htmlspecialchars(mb_substr($job['error_message'], 0, 45))?>...
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?=date('H:i:s d/m', $job['date_created'])?>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?com=operations&act=job_detail&module=<?=$job['module']?>&id=<?=$job['id']?>" class="btn btn-xs btn-outline-info mr-1" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <?php if ($job['status'] === 'FAILED' || $job['status'] === 'CANCELLED'): ?>
                                            <form action="index.php?com=operations&act=retry_job" method="POST" class="d-inline">
                                                <input type="hidden" name="module" value="<?=$job['module']?>">
                                                <input type="hidden" name="job_id" value="<?=$job['id']?>">
                                                <button type="submit" class="btn btn-xs btn-outline-warning" title="Safe Retry" onclick="return confirm('Bạn có chắc muốn Retry tác vụ #<?=$job['id']?>?');">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($job['status'] === 'PENDING' || $job['status'] === 'SCHEDULED'): ?>
                                            <form action="index.php?com=operations&act=cancel_job" method="POST" class="d-inline">
                                                <input type="hidden" name="module" value="<?=$job['module']?>">
                                                <input type="hidden" name="job_id" value="<?=$job['id']?>">
                                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Hủy tác vụ" onclick="return confirm('Hủy tác vụ đang chờ #<?=$job['id']?>?');">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Không tìm thấy tác vụ nào phù hợp với bộ lọc.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($jobList['total_pages'] > 1): ?>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Trang <?=$jobList['page']?> / <?=$jobList['total_pages']?> (Tổng: <?=$jobList['total']?> tác vụ)</span>
                    <div>
                        <?php if ($jobList['page'] > 1): ?>
                            <a href="index.php?com=operations&act=jobs&module=<?=$filterModule?>&status=<?=$filterStatus?>&p=<?=$jobList['page']-1?>" class="btn btn-xs btn-outline-secondary mr-1">Trước</a>
                        <?php endif; ?>
                        <?php if ($jobList['page'] < $jobList['total_pages']): ?>
                            <a href="index.php?com=operations&act=jobs&module=<?=$filterModule?>&status=<?=$filterStatus?>&p=<?=$jobList['page']+1?>" class="btn btn-xs btn-outline-secondary">Sau</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
