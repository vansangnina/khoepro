<?php
$linkOverview = "index.php?com=analytics&act=overview";
$linkProducts = "index.php?com=analytics&act=products";
$linkPosts = "index.php?com=analytics&act=posts";
$linkConversions = "index.php?com=analytics&act=conversions";
$linkWinner = "index.php?com=analytics&act=winner_detection";
$linkRules = "index.php?com=analytics&act=winner_rules";
$linkImport = "index.php?com=analytics&act=conversion_import";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-chart-line mr-2 text-primary"></i>Tổng quan Đo lường & Hiệu suất FITNADO
                </h1>
                <small class="text-muted">Đo lường từ Video → Lượt xem → Click Affiliate → Chuyển đổi & Doanh thu thực tế (Observed Data Only)</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <!-- Time Range Filter Buttons -->
                <div class="btn-group shadow-sm mr-2" role="group">
                    <a href="<?=$linkOverview?>&time_range=today" class="btn btn-sm <?=($timeRange === 'today') ? 'btn-primary' : 'btn-outline-secondary'?>">Hôm nay</a>
                    <a href="<?=$linkOverview?>&time_range=7d" class="btn btn-sm <?=($timeRange === '7d') ? 'btn-primary' : 'btn-outline-secondary'?>">7 ngày</a>
                    <a href="<?=$linkOverview?>&time_range=30d" class="btn btn-sm <?=($timeRange === '30d') ? 'btn-primary' : 'btn-outline-secondary'?>">30 ngày</a>
                    <a href="<?=$linkOverview?>&time_range=all" class="btn btn-sm <?=($timeRange === 'all') ? 'btn-primary' : 'btn-outline-secondary'?>">Tất cả</a>
                </div>
                <a href="<?=$linkImport?>" class="btn btn-sm btn-success shadow-sm mr-1">
                    <i class="fas fa-file-import mr-1"></i> Import CSV Đơn Hàng
                </a>
                <a href="<?=$linkWinner?>" class="btn btn-sm btn-warning shadow-sm text-dark font-weight-bold">
                    <i class="fas fa-trophy mr-1"></i> Winner Detection
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Conversion Data Status Notice -->
<?php if (empty($overview['has_conversions_connected'])): ?>
<div class="container-fluid">
    <div class="alert alert-warning alert-dismissible fade show shadow-sm text-sm border-warning" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>DỮ LIỆU ĐỐI SOÁT ĐƠN HÀNG (CONVERSION DATA): CHƯA KẾT NỐI</strong>
        <span class="ml-2">Hệ thống đang ghi nhận đầy đủ Lượt xem và Click tiếp thị. Để xem doanh thu và hoa hồng thực tế, vui lòng <a href="<?=$linkImport?>" class="alert-link font-weight-bold">Nhập file CSV đối soát từ Sàn TMĐT</a>.</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- KPI Top Cards -->
<section class="content mb-2 text-sm">
    <div class="container-fluid">
        <div class="row">
            <!-- 1. Landing Sessions -->
            <div class="col-xl-3 col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #007bff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Lượt Khách Truy Cập (Sessions)</span>
                                <h3 class="font-weight-bold text-primary mb-0 mt-1"><?=number_format($overview['total_sessions'])?></h3>
                                <small class="text-muted">Tổng phiên có gắn Post / UTM</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-primary">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Affiliate Clicks & CTR -->
            <div class="col-xl-3 col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Click Sang Sàn (CTR)</span>
                                <h3 class="font-weight-bold text-info mb-0 mt-1"><?=number_format($overview['total_clicks'])?></h3>
                                <small class="text-muted">Tỷ lệ CTR: <strong class="text-dark"><?=$overview['ctr_pct']?>%</strong> (<?=$overview['total_clicks']?> / <?=$overview['total_sessions']?>)</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-mouse-pointer fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Conversions & CVR -->
            <div class="col-xl-3 col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Đơn Hàng Chuyển Đổi (CVR)</span>
                                <h3 class="font-weight-bold text-success mb-0 mt-1"><?=number_format($overview['conversions_count'])?></h3>
                                <small class="text-muted">CVR: <strong class="text-dark"><?=$overview['cvr_pct']?>%</strong> | <?=empty($overview['has_conversions_connected']) ? 'Chưa kết nối' : 'Đã đối soát'?></small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Confirmed Commission & Content ROI -->
            <div class="col-xl-3 col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Hoa Hồng Thực Nhận (VND)</span>
                                <h3 class="font-weight-bold text-warning mb-0 mt-1"><?=number_format($overview['confirmed_commission_vnd'])?> đ</h3>
                                <small class="text-muted">Content ROI: <strong class="<?=($overview['content_roi_vnd'] >= 0) ? 'text-success' : 'text-danger'?>"><?=number_format($overview['content_roi_vnd'])?> đ</strong></small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-warning">
                                <i class="fas fa-coins fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Phễu chuyển đổi & Tín hiệu Winner -->
<section class="content text-sm">
    <div class="container-fluid">
        <div class="row">
            <!-- Conversion Funnel Card -->
            <div class="col-lg-8 col-12 mb-3">
                <div class="card card-outline card-primary shadow-sm h-100">
                    <div class="card-header bg-white py-2">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-filter mr-2 text-primary"></i>Phễu Chuyển Đổi FITNADO (Attribution Funnel)</h3>
                        <div class="card-tools">
                            <span class="badge badge-light border">Khoảng thời gian: <?=$timeRange?></span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <!-- Funnel Step 1 -->
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 bg-light rounded border">
                                    <i class="fas fa-globe text-primary fa-2x mb-2"></i>
                                    <div class="text-muted small">1. Landing Sessions</div>
                                    <h4 class="font-weight-bold text-dark mb-0"><?=number_format($overview['total_sessions'])?></h4>
                                    <span class="badge badge-secondary mt-1">100% Khách</span>
                                </div>
                            </div>
                            <!-- Funnel Step 2 -->
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 bg-light rounded border">
                                    <i class="fas fa-eye text-info fa-2x mb-2"></i>
                                    <div class="text-muted small">2. Xem Chi Tiết SP</div>
                                    <h4 class="font-weight-bold text-dark mb-0"><?=number_format($overview['total_product_views'])?></h4>
                                    <span class="badge badge-info mt-1"><?=AnalyticsService::safePercentage($overview['total_product_views'], $overview['total_sessions'])?>% Session</span>
                                </div>
                            </div>
                            <!-- Funnel Step 3 -->
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 bg-light rounded border">
                                    <i class="fas fa-mouse-pointer text-warning fa-2x mb-2"></i>
                                    <div class="text-muted small">3. Click Affiliate</div>
                                    <h4 class="font-weight-bold text-dark mb-0"><?=number_format($overview['total_clicks'])?></h4>
                                    <span class="badge badge-warning mt-1 text-dark">CTR: <?=$overview['ctr_pct']?>%</span>
                                </div>
                            </div>
                            <!-- Funnel Step 4 -->
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 bg-light rounded border">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                    <div class="text-muted small">4. Đơn Hàng Thành Công</div>
                                    <h4 class="font-weight-bold text-dark mb-0"><?=number_format($overview['conversions_count'])?></h4>
                                    <span class="badge badge-success mt-1">CVR: <?=$overview['cvr_pct']?>%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Funnel Performance Notes -->
                        <div class="border-top pt-3 mt-2">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <span class="text-muted">Doanh thu / Session (EPCs):</span>
                                    <strong class="ml-1 text-dark"><?=number_format($overview['revenue_per_session_vnd'])?> đ</strong>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <span class="text-muted">Doanh thu / Click (EPC):</span>
                                    <strong class="ml-1 text-dark"><?=number_format($overview['epc_vnd'])?> đ</strong>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <span class="text-muted">Chi phí sản xuất Video (API):</span>
                                    <strong class="ml-1 text-danger"><?=number_format($overview['total_external_cost_vnd'])?> đ</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Winner Detection Overview Card -->
            <div class="col-lg-4 col-12 mb-3">
                <div class="card card-outline card-warning shadow-sm h-100">
                    <div class="card-header bg-white py-2">
                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-trophy mr-2 text-warning"></i>Phát hiện Winner (Detection)</h3>
                        <div class="card-tools">
                            <a href="<?=$linkWinner?>" class="btn btn-xs btn-outline-warning">Chi tiết <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-crown text-warning mr-2"></i>Sản phẩm Thắng (Winners)</span>
                                <span class="badge badge-warning font-weight-bold px-2 py-1" style="font-size: 0.9rem;"><?=$overview['winner_count']?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-star text-info mr-2"></i>Sản phẩm Tiềm năng (Promising)</span>
                                <span class="badge badge-info font-weight-bold px-2 py-1" style="font-size: 0.9rem;"><?=$overview['promising_count']?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-sliders-h text-secondary mr-2"></i>Ngưỡng mẫu tối thiểu (Gate)</span>
                                <span class="badge badge-light border font-weight-bold"><?=$rulesConfig['min_landing_sessions']?> sessions / <?=$rulesConfig['min_affiliate_clicks']?> clicks</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-percentage text-secondary mr-2"></i>Ngưỡng Winner CTR</span>
                                <span class="badge badge-light border font-weight-bold">&ge; <?=$rulesConfig['winner_ctr_pct']?>%</span>
                            </li>
                        </ul>
                        <div class="text-center">
                            <a href="<?=$linkWinner?>" class="btn btn-sm btn-block btn-outline-dark">
                                <i class="fas fa-sync-alt mr-1"></i> Chạy Đánh Giá Winner Toàn Diện
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
