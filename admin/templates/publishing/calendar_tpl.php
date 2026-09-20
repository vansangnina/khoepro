<?php
$linkMan = "index.php?com=publishing&act=man";
$linkCalendar = "index.php?com=publishing&act=calendar";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-calendar-alt mr-2 text-info"></i>Lịch Xuất bản Đa Kênh (Publishing Calendar)
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
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header p-2 d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark"><i class="fas fa-stream mr-1"></i> Dòng thời gian Xuất bản (Chronological Publishing Timeline)</span>
                <span class="badge badge-info"><?=count($calendarItems ?? array())?> bài đăng</span>
            </div>
            <div class="card-body p-3">
                <?php if (!empty($calendarItems)): ?>
                    <div class="timeline timeline-inverse">
                        <?php 
                        $currentDate = '';
                        foreach ($calendarItems as $item): 
                            $timeVal = $item['scheduled_at'] ?: ($item['published_at'] ?: $item['date_created']);
                            $dateStr = date('d/m/Y (l)', $timeVal);
                            if ($dateStr !== $currentDate):
                                $currentDate = $dateStr;
                        ?>
                            <div class="time-label">
                                <span class="bg-primary px-3 py-1 font-weight-bold"><?=$dateStr?></span>
                            </div>
                        <?php endif; ?>
                            <div>
                                <i class="fas fa-video bg-<?= ($item['status'] === 'PUBLISHED') ? 'success' : (($item['status'] === 'SCHEDULED') ? 'primary' : 'secondary') ?>"></i>
                                <div class="timeline-item shadow-sm">
                                    <span class="time"><i class="fas fa-clock"></i> <?=date('H:i', $timeVal)?></span>
                                    <h3 class="timeline-header font-weight-bold" style="font-size: 0.95rem;">
                                        <a href="index.php?com=publishing&act=view&id=<?=$item['id']?>" class="text-primary font-weight-bold"><?=htmlspecialchars($item['title'])?></a>
                                        <span class="badge badge-dark ml-2"><?=strtoupper($item['platform'])?></span>
                                        <span class="badge <?= ($item['status'] === 'PUBLISHED') ? 'badge-success' : (($item['status'] === 'SCHEDULED') ? 'badge-primary' : 'badge-secondary') ?> ml-1"><?=$item['status']?></span>
                                    </h3>
                                    <div class="timeline-body p-2 small">
                                        <div><i class="fas fa-box text-secondary mr-1"></i> Sản phẩm: <strong><?=htmlspecialchars($item['product_name'] ?? 'N/A')?></strong></div>
                                        <div><i class="fas fa-user-circle text-secondary mr-1"></i> Kênh: <strong><?=htmlspecialchars($item['account_handle'] ?: '@fitnado.vn')?></strong></div>
                                    </div>
                                    <div class="timeline-footer p-2 bg-light">
                                        <a href="index.php?com=publishing&act=view&id=<?=$item['id']?>" class="btn btn-sm btn-primary py-0 px-2 shadow-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div>
                            <i class="fas fa-flag-checkered bg-gray"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fa-3x mb-2 text-secondary" style="opacity: 0.5;"></i><br>
                        Chưa có lịch trình xuất bản nào được tạo.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
