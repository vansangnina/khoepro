<?php
$linkOverview  = "index.php?com=operations&act=overview";
$linkPipeline  = "index.php?com=operations&act=pipeline";
$linkJobs      = "index.php?com=operations&act=jobs";
$linkProviders = "index.php?com=operations&act=providers";
$linkCosts     = "index.php?com=operations&act=costs";
$linkAlerts    = "index.php?com=operations&act=alerts";
$linkLogs      = "index.php?com=operations&act=logs";
$linkSettings  = "index.php?com=operations&act=settings";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-tachometer-alt mr-2 text-primary"></i>Operations & Automation Control Center
                </h1>
                <small class="text-muted">Trung tâm Vận hành, Giám sát Tiến trình, Điều khiển Tự động hóa & Kiểm soát Chi phí</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkPipeline?>" class="btn btn-sm btn-info shadow-sm mr-1">
                    <i class="fas fa-stream mr-1"></i> Pipeline & Hàng đợi
                </a>
                <a href="<?=$linkCosts?>" class="btn btn-sm btn-success shadow-sm mr-1">
                    <i class="fas fa-dollar-sign mr-1"></i> Quản lý Chi phí
                </a>
                <a href="<?=$linkSettings?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-sliders-h mr-1"></i> Cấu hình
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <!-- Emergency Controls Alert Banner -->
        <?php if (!empty($costToday['budget']['is_exceeded'])): ?>
        <div class="alert alert-danger shadow-sm border-0 mb-3">
            <h5><i class="icon fas fa-ban"></i> ĐÃ ĐẠT GIỚI HẠN NGÂN SÁCH API (BUDGET LIMIT REACHED)!</h5>
            Chi phí API hôm nay đã đạt hoặc vượt ngưỡng ngân sách (<?=number_format($costToday['budget']['spent_today'])?> / <?=number_format($costToday['budget']['daily_limit'])?> VND). Mọi API trả phí mới đang bị tạm khóa.
            <a href="<?=$linkCosts?>" class="btn btn-xs btn-outline-light ml-3 font-weight-bold">Giải trình Override Ngân sách</a>
        </div>
        <?php endif; ?>

        <!-- KPI Summary Cards -->
        <div class="row">
            <!-- 1. System Health -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-heartbeat"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Sức khỏe Hệ thống</span>
                        <span class="info-box-number font-weight-bold">
                            <?php
                            $allHealthy = true;
                            foreach ($workerStatuses as $ws) {
                                if ($ws['health'] === 'FAILED' || $ws['health'] === 'DEGRADED') { $allHealthy = false; break; }
                            }
                            ?>
                            <?php if ($allHealthy): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>HEALTHY</span>
                            <?php else: ?>
                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i>ATTENTION</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Human Action Queue -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-user-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Cần Bạn Xử Lý</span>
                        <span class="info-box-number text-danger font-weight-bold" style="font-size: 1.3rem;">
                            <?=$humanActions['total'] ?? 0?> <small class="text-muted font-weight-normal">hành động</small>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. API Cost Today -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Chi phí API Hôm nay</span>
                        <span class="info-box-number font-weight-bold" style="font-size: 1.2rem;">
                            <?=number_format($costToday['actual_cost_vnd'] ?? 0)?> <small>VND</small>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4. Active Alerts -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon bg-gradient-danger elevation-1"><i class="fas fa-bell"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Sự cố & Cảnh báo</span>
                        <span class="info-box-number font-weight-bold" style="font-size: 1.3rem;">
                            <?=count($activeAlerts)?> <small class="text-muted font-weight-normal">sự cố đang mở</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Background Workers & Queues -->
            <div class="col-lg-8">
                <!-- Background Workers Status Table -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-cogs mr-2 text-primary"></i>Background Workers & Heartbeats (Process Registry)</span>
                        <a href="<?=$linkJobs?>" class="btn btn-xs btn-outline-primary">Xem Hàng đợi Chi tiết</a>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th>Worker</th>
                                    <th>Module</th>
                                    <th class="text-center">Sức khỏe</th>
                                    <th class="text-center">Heartbeat gần nhất</th>
                                    <th class="text-center">Pending / Failed</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($workerStatuses)): ?>
                                    <?php foreach ($workerStatuses as $w): ?>
                                        <?php
                                        $badge = 'secondary';
                                        if ($w['health'] === 'HEALTHY') $badge = 'success';
                                        elseif ($w['health'] === 'WARNING') $badge = 'warning text-dark';
                                        elseif ($w['health'] === 'DEGRADED') $badge = 'info';
                                        elseif ($w['health'] === 'FAILED') $badge = 'danger';
                                        elseif ($w['health'] === 'DISABLED') $badge = 'secondary';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold text-dark"><?=$w['name']?></div>
                                                <small class="text-muted font-italic">PID: <?=($w['pid'] ?: 'N/A')?> | Host: <?=($w['hostname'] ?: 'localhost')?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border text-uppercase"><?=$w['module']?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-<?=$badge?> px-2 py-1"><?=$w['health']?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($w['last_heartbeat_at'])): ?>
                                                    <?=date('H:i:s d/m', $w['last_heartbeat_at'])?><br>
                                                    <small class="text-muted">(<?=time() - $w['last_heartbeat_at']?>s trước)</small>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic">Chưa chạy</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning mr-1"><?=$w['pending_jobs']?> Chờ</span>
                                                <span class="badge badge-danger"><?=$w['failed_jobs']?> Lỗi</span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?=$w['health_reason']?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Human Action Queue Top Items -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-tasks mr-2 text-warning"></i>Hàng đợi Cần Con Người Xử Lý ("CẦN BẠN XỬ LÝ")</span>
                        <a href="<?=$linkPipeline?>" class="btn btn-xs btn-outline-warning">Xem Tất Cả (<?=$humanActions['total']?>)</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (!empty($humanActions['items'])): ?>
                                <?php $topItems = array_slice($humanActions['items'], 0, 5); ?>
                                <?php foreach ($topItems as $item): ?>
                                    <?php
                                    $pBadge = 'info';
                                    if ($item['priority'] === 'CRITICAL') $pBadge = 'danger font-weight-bold';
                                    elseif ($item['priority'] === 'COST_BLOCKED') $pBadge = 'danger';
                                    elseif ($item['priority'] === 'WAITING_APPROVAL') $pBadge = 'warning text-dark';
                                    ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <span class="badge badge-<?=$pBadge?> mr-2"><?=$item['priority']?></span>
                                            <span class="badge badge-light border mr-2"><?=$item['category']?></span>
                                            <span class="font-weight-bold text-dark"><?=$item['title']?></span>
                                            <div class="text-muted small mt-1">Tham chiếu: <?=$item['reference']?> | <?=date('H:i:s d/m/Y', $item['date'])?></div>
                                        </div>
                                        <div>
                                            <a href="<?=$item['url']?>" class="btn btn-sm btn-primary shadow-sm">
                                                <?=$item['action_label']?> <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <p class="mb-0">Tuyệt vời! Không có tác vụ hay nội dung nào đang chờ bạn xử lý.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Diagnostics, Freshness & Controls -->
            <div class="col-lg-4">
                <!-- Environment Health Diagnostic -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-server mr-2 text-info"></i>Môi trường Vận hành & Runtime
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">PHP Runtime:</span>
                                <span class="font-weight-bold"><?=$envHealth['php']['actual_runtime']?></span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Target Compatibility:</span>
                                <span class="badge badge-light border"><?=$envHealth['php']['target_compat']?></span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Cơ sở Dữ liệu:</span>
                                <span class="badge badge-success"><?=$envHealth['database']['type']?> (<?=$envHealth['database']['version']?>)</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">FFmpeg Binary:</span>
                                <?php if ($envHealth['ffmpeg']['available']): ?>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Sẵn sàng</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Thiếu File</span>
                                <?php endif; ?>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">FFprobe Binary:</span>
                                <?php if ($envHealth['ffprobe']['available']): ?>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Sẵn sàng</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Thiếu File</span>
                                <?php endif; ?>
                            </li>
                            <li class="mb-0 d-flex justify-content-between">
                                <span class="text-muted">Dung lượng Trống:</span>
                                <span class="font-weight-bold text-dark"><?=$envHealth['disk']['free_gb']?> GB</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Data Freshness Status -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-sync-alt mr-2 text-primary"></i>Độ Tươi mới Dữ liệu (Freshness)
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($freshness as $k => $f): ?>
                                <?php
                                $fBadge = 'secondary';
                                if ($f['status'] === 'HEALTHY') $fBadge = 'success';
                                elseif ($f['status'] === 'WARNING') $fBadge = 'warning text-dark';
                                elseif ($f['status'] === 'NOT_CONFIGURED') $fBadge = 'light border text-muted';
                                ?>
                                <li class="mb-2 pb-2 border-bottom">
                                    <div class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?=$f['label']?></div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted"><?=$f['status_label']?></small>
                                        <span class="badge badge-<?=$fBadge?> px-2 py-1"><?=$f['status']?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Emergency Stop Controls -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-gradient-danger text-white font-weight-bold">
                        <i class="fas fa-shield-alt mr-2"></i>Emergency Controls (Dừng Khẩn Cấp)
                    </div>
                    <div class="card-body p-3 text-center">
                        <p class="text-muted small mb-3">Tạm ngắt toàn bộ các cuộc gọi API bên ngoài phát sinh chi phí (TTS, Video AI) mà không làm mất hàng đợi tác vụ.</p>
                        <form action="index.php?com=operations&act=emergency_pause_paid" method="POST">
                            <input type="hidden" name="pause" value="<?=(!empty($costToday['budget']['is_exceeded']) || $ops->isPaidAutomationPaused()) ? '0' : '1'?>">
                            <?php if ($ops->isPaidAutomationPaused()): ?>
                                <button type="submit" class="btn btn-block btn-success font-weight-bold shadow-sm" onclick="return confirm('Bạn có chắc chắn muốn khôi phục lại các cuộc gọi API trả phí?');">
                                    <i class="fas fa-play mr-1"></i> KHÔI PHỤC API TRẢ PHÍ
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-block btn-danger font-weight-bold shadow-sm" onclick="return confirm('CẢNH BÁO: Bạn đang kích hoạt Dừng Khẩn Cấp toàn bộ API trả phí. Tiếp tục?');">
                                    <i class="fas fa-pause-circle mr-1"></i> DỪNG KHẨN CẤP API TRẢ PHÍ
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
