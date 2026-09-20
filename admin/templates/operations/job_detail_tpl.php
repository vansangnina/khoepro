<?php
$linkJobs = "index.php?com=operations&act=jobs";
$job = $jobDetail['job'] ?? array();
$related = $jobDetail['related'] ?? array();
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-file-alt mr-2 text-info"></i>Chi tiết Tác vụ: <span class="text-primary text-uppercase"><?=$module?></span> #<?=$job['id'] ?? 0?>
                </h1>
                <small class="text-muted">Kiểm tra thông số kỹ thuật, payload đầu vào, nhật ký lỗi và thực hiện Safe Retry</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkJobs?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại Danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <div class="row">
            <!-- Left: Metadata & Status -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>Thông tin Tác vụ (Job Metadata)
                    </div>
                    <div class="card-body p-3">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 160px;">Mã Tác vụ:</td>
                                <td class="font-weight-bold">#<?=$job['id'] ?? 'N/A'?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phân hệ:</td>
                                <td><span class="badge badge-light border text-uppercase font-weight-bold"><?=$module?></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trạng thái:</td>
                                <td>
                                    <?php
                                    $st = $job['status'] ?? 'UNKNOWN';
                                    $badge = 'secondary';
                                    if ($st === 'SUCCESS' || $st === 'PUBLISHED') $badge = 'success';
                                    elseif ($st === 'RUNNING' || $st === 'PUBLISHING') $badge = 'primary';
                                    elseif ($st === 'PENDING' || $st === 'SCHEDULED' || $st === 'READY') $badge = 'warning text-dark font-weight-bold';
                                    elseif ($st === 'FAILED') $badge = 'danger font-weight-bold';
                                    ?>
                                    <span class="badge badge-<?=$badge?> px-2 py-1"><?=$st?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Provider:</td>
                                <td><code><?=$job['provider'] ?? ($job['ai_model'] ?? 'N/A')?></code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Số lần thử (Attempts):</td>
                                <td><?=$job['attempts'] ?? 0?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Thời lượng xử lý:</td>
                                <td><?=(!empty($job['duration'])) ? $job['duration'] . ' giây' : 'N/A'?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Thời điểm tạo:</td>
                                <td><?=(!empty($job['date_created'])) ? date('H:i:s d/m/Y', $job['date_created']) : 'N/A'?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Bắt đầu / Hoàn tất:</td>
                                <td>
                                    <?=(!empty($job['started_at'])) ? date('H:i:s d/m/Y', $job['started_at']) : '-'?> /
                                    <?=(!empty($job['completed_at'])) ? date('H:i:s d/m/Y', $job['completed_at']) : '-'?>
                                </td>
                            </tr>
                        </table>

                        <?php if (!empty($job['error_message'])): ?>
                            <hr class="my-2">
                            <div class="alert alert-danger mb-0 mt-2">
                                <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Thông báo Lỗi:</h6>
                                <code><?=htmlspecialchars($job['error_message'])?></code>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between">
                        <?php if ($st === 'FAILED' || $st === 'CANCELLED'): ?>
                            <form action="index.php?com=operations&act=retry_job" method="POST" class="d-inline">
                                <input type="hidden" name="module" value="<?=$module?>">
                                <input type="hidden" name="job_id" value="<?=$job['id']?>">
                                <button type="submit" class="btn btn-sm btn-warning font-weight-bold shadow-sm" onclick="return confirm('Bạn có chắc muốn Safe Retry tác vụ này?');">
                                    <i class="fas fa-redo mr-1"></i> Safe Retry Job
                                </button>
                            </form>
                        <?php elseif ($st === 'PENDING' || $st === 'SCHEDULED'): ?>
                            <form action="index.php?com=operations&act=cancel_job" method="POST" class="d-inline">
                                <input type="hidden" name="module" value="<?=$module?>">
                                <input type="hidden" name="job_id" value="<?=$job['id']?>">
                                <button type="submit" class="btn btn-sm btn-danger font-weight-bold shadow-sm" onclick="return confirm('Hủy tác vụ này?');">
                                    <i class="fas fa-times mr-1"></i> Hủy Tác vụ
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">Tác vụ đã hoàn tất hoặc đang được xử lý bình thường.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Payload & Related Entities -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-code mr-2 text-success"></i>Dữ liệu Thô / Payload (Sanitized)
                    </div>
                    <div class="card-body p-2">
                        <pre class="bg-light p-3 border rounded text-dark mb-0" style="max-height: 400px; overflow-y: auto; font-size: 0.8rem;"><code><?=json_encode($job, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)?></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
