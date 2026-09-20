<?php
$linkOverview  = "index.php?com=operations&act=overview";
$linkPipeline  = "index.php?com=operations&act=pipeline";
$linkJobs      = "index.php?com=operations&act=jobs";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-stream mr-2 text-primary"></i>Pipeline Overview & Hàng đợi Hành động
                </h1>
                <small class="text-muted">Trực quan hóa luồng dữ liệu 6 giai đoạn và toàn bộ hàng đợi duyệt tập trung</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkOverview?>" class="btn btn-sm btn-secondary shadow-sm mr-1">
                    <i class="fas fa-arrow-left mr-1"></i> Tổng quan
                </a>
                <a href="<?=$linkJobs?>" class="btn btn-sm btn-info shadow-sm">
                    <i class="fas fa-tasks mr-1"></i> Quản lý Tác vụ
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <!-- Visual Pipeline Stage Cards -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white font-weight-bold">
                <i class="fas fa-project-diagram mr-2 text-primary"></i>Luồng Vận hành Pipeline FITNADO
            </div>
            <div class="card-body p-4">
                <div class="row text-center">
                    <!-- Stage 1: Research -->
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light h-100">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">1. Research</div>
                            <div class="h5 font-weight-bold text-primary mb-1">
                                <?=$queueSummaries['research']['pending'] ?? 0?> <small class="text-muted" style="font-size: 0.75rem;">chờ</small>
                            </div>
                            <small class="d-block text-muted mb-2"><?=$queueSummaries['research']['failed'] ?? 0?> lỗi</small>
                            <a href="index.php?com=product_research&act=man" class="btn btn-xs btn-outline-primary btn-block">Mở Module</a>
                        </div>
                    </div>

                    <!-- Stage 2: Content -->
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light h-100">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">2. Content</div>
                            <div class="h5 font-weight-bold text-info mb-1">
                                <?=$queueSummaries['content']['pending'] ?? 0?> <small class="text-muted" style="font-size: 0.75rem;">chờ</small>
                            </div>
                            <small class="d-block text-muted mb-2"><?=$queueSummaries['content']['failed'] ?? 0?> lỗi</small>
                            <a href="index.php?com=ai_content&act=man" class="btn btn-xs btn-outline-info btn-block">Mở Module</a>
                        </div>
                    </div>

                    <!-- Stage 3: Video -->
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light h-100">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">3. Video</div>
                            <div class="h5 font-weight-bold text-warning mb-1">
                                <?=$queueSummaries['video']['pending'] ?? 0?> <small class="text-muted" style="font-size: 0.75rem;">chờ</small>
                            </div>
                            <small class="d-block text-muted mb-2"><?=$queueSummaries['video']['failed'] ?? 0?> lỗi</small>
                            <a href="index.php?com=ai_video&act=man" class="btn btn-xs btn-outline-warning btn-block">Mở Module</a>
                        </div>
                    </div>

                    <!-- Stage 4: Publishing -->
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light h-100">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">4. Publishing</div>
                            <div class="h5 font-weight-bold text-success mb-1">
                                <?=$queueSummaries['publishing']['pending'] ?? 0?> <small class="text-muted" style="font-size: 0.75rem;">lên lịch</small>
                            </div>
                            <small class="d-block text-muted mb-2"><?=$queueSummaries['publishing']['failed'] ?? 0?> lỗi</small>
                            <a href="index.php?com=publishing&act=posts" class="btn btn-xs btn-outline-success btn-block">Mở Module</a>
                        </div>
                    </div>

                    <!-- Stage 5: Analytics -->
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light h-100">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">5. Analytics</div>
                            <div class="h5 font-weight-bold text-purple mb-1" style="color: #6f42c1;">
                                Active <small class="text-muted" style="font-size: 0.75rem;">tracking</small>
                            </div>
                            <small class="d-block text-muted mb-2">Attribution Engine</small>
                            <a href="index.php?com=analytics&act=overview" class="btn btn-xs btn-outline-secondary btn-block">Mở Module</a>
                        </div>
                    </div>

                    <!-- Stage 6: Optimization -->
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light h-100">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">6. Optimization</div>
                            <div class="h5 font-weight-bold text-dark mb-1">
                                A/B Testing
                            </div>
                            <small class="d-block text-muted mb-2">Feedback Loop</small>
                            <a href="index.php?com=optimization&act=recommendations" class="btn btn-xs btn-outline-dark btn-block">Mở Module</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comprehensive Human Action Queue Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-clipboard-check mr-2 text-warning"></i>Toàn bộ Hàng đợi Hành động Con người ("CẦN BẠN XỬ LÝ")
                </span>
                <span class="badge badge-warning px-2 py-1 font-weight-bold">
                    Tổng cộng: <?=$humanActions['total']?> mục
                </span>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th style="width: 130px;">Mức ưu tiên</th>
                            <th style="width: 110px;">Phân loại</th>
                            <th>Nội dung / Mô tả mục cần xử lý</th>
                            <th>Mã tham chiếu</th>
                            <th class="text-center">Thời điểm tạo</th>
                            <th class="text-center" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($humanActions['items'])): ?>
                            <?php foreach ($humanActions['items'] as $item): ?>
                                <?php
                                $pBadge = 'info';
                                if ($item['priority'] === 'CRITICAL') $pBadge = 'danger font-weight-bold';
                                elseif ($item['priority'] === 'COST_BLOCKED') $pBadge = 'danger';
                                elseif ($item['priority'] === 'WAITING_APPROVAL') $pBadge = 'warning text-dark';
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-<?=$pBadge?> px-2 py-1"><?=$item['priority']?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-uppercase font-weight-bold"><?=$item['category']?></span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?=$item['title']?></div>
                                    </td>
                                    <td>
                                        <code class="text-muted"><?=$item['reference']?></code>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?=date('H:i:s d/m/Y', $item['date'])?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?=$item['url']?>" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
                                            <?=$item['action_label']?> <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <p class="mb-0">Hệ thống hoàn toàn thông suốt! Không có mục nào cần can thiệp xử lý.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
