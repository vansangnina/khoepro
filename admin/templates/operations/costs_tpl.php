<?php
$linkOverview = "index.php?com=operations&act=overview";
$linkCosts    = "index.php?com=operations&act=costs";
$bd = $costData['breakdown'] ?? array();
$bg = $costData['budget'] ?? array();
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-dollar-sign mr-2 text-success"></i>Trung tâm Chi phí API & Kiểm soát Ngân sách (Cost Center)
                </h1>
                <small class="text-muted">Tổng hợp chi phí thực tế, đối soát ngân sách Ngày/Tháng và cơ chế Override có kiểm toán</small>
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
        <!-- Range Filter Buttons -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="font-weight-bold mr-2 text-muted">Khoảng thời gian:</span>
                    <a href="<?=$linkCosts?>&range=today" class="btn btn-sm <?=($timeRange === 'today') ? 'btn-primary' : 'btn-outline-secondary'?> mr-1">Hôm nay</a>
                    <a href="<?=$linkCosts?>&range=7days" class="btn btn-sm <?=($timeRange === '7days') ? 'btn-primary' : 'btn-outline-secondary'?> mr-1">7 ngày qua</a>
                    <a href="<?=$linkCosts?>&range=30days" class="btn btn-sm <?=($timeRange === '30days') ? 'btn-primary' : 'btn-outline-secondary'?> mr-1">30 ngày qua</a>
                    <a href="<?=$linkCosts?>&range=all" class="btn btn-sm <?=($timeRange === 'all') ? 'btn-primary' : 'btn-outline-secondary'?>">Toàn bộ</a>
                </div>
                <div>
                    <span class="badge badge-light border px-2 py-1">Đơn vị tiền tệ: <strong>VND</strong></span>
                </div>
            </div>
        </div>

        <!-- Budget Level Progress Meters -->
        <div class="row mb-3">
            <!-- Daily Budget -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark"><i class="fas fa-calendar-day mr-1 text-primary"></i> Ngân sách Ngày (Daily Budget)</span>
                            <span class="badge badge-<?=($bg['daily_level'] === 'LIMIT_REACHED') ? 'danger' : (($bg['daily_level'] === 'WARNING') ? 'warning text-dark' : 'success')?> px-2 py-1">
                                <?=$bg['daily_level']?>
                            </span>
                        </div>
                        <div class="h5 font-weight-bold text-dark mb-2">
                            <?=number_format($bg['spent_today'])?> <small class="text-muted">/ <?=number_format($bg['daily_limit'])?> VND</small>
                        </div>
                        <?php
                        $dailyPct = min(100, round(($bg['daily_limit'] > 0 ? ($bg['spent_today'] / $bg['daily_limit']) * 100 : 0), 1));
                        $barClass = 'bg-success';
                        if ($dailyPct >= 100) $barClass = 'bg-danger';
                        elseif ($dailyPct >= 80) $barClass = 'bg-warning';
                        ?>
                        <div class="progress progress-sm">
                            <div class="progress-bar <?=$barClass?>" role="progressbar" style="width: <?=$dailyPct?>%"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">Đã sử dụng <?=$dailyPct?>% hạn mức ngày.</small>
                    </div>
                </div>
            </div>

            <!-- Monthly Budget -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark"><i class="fas fa-calendar-alt mr-1 text-info"></i> Ngân sách Tháng (Monthly Budget)</span>
                            <span class="badge badge-<?=($bg['monthly_level'] === 'LIMIT_REACHED') ? 'danger' : (($bg['monthly_level'] === 'WARNING') ? 'warning text-dark' : 'success')?> px-2 py-1">
                                <?=$bg['monthly_level']?>
                            </span>
                        </div>
                        <div class="h5 font-weight-bold text-dark mb-2">
                            <?=number_format($bg['spent_month'])?> <small class="text-muted">/ <?=number_format($bg['monthly_limit'])?> VND</small>
                        </div>
                        <?php
                        $monthPct = min(100, round(($bg['monthly_limit'] > 0 ? ($bg['spent_month'] / $bg['monthly_limit']) * 100 : 0), 1));
                        $barMonthClass = 'bg-info';
                        if ($monthPct >= 100) $barMonthClass = 'bg-danger';
                        elseif ($monthPct >= 80) $barMonthClass = 'bg-warning';
                        ?>
                        <div class="progress progress-sm">
                            <div class="progress-bar <?=$barMonthClass?>" role="progressbar" style="width: <?=$monthPct?>%"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">Đã sử dụng <?=$monthPct?>% hạn mức tháng.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown Table & Override Form -->
        <div class="row">
            <!-- Cost Breakdown -->
            <div class="col-lg-7 col-12">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-pie-chart mr-2 text-primary"></i>Phân rã Chi phí Thực tế theo Loại Tác vụ
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th>Loại Chi phí</th>
                                    <th>Mục đích</th>
                                    <th class="text-right">Chi phí Thực tế (Actual)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold"><i class="fas fa-video text-warning mr-2"></i>AI Video Generation</td>
                                    <td class="text-muted">Beeknoee / Runway / Luma AI Scenes</td>
                                    <td class="text-right font-weight-bold text-danger"><?=number_format($bd['ai_video_cost'])?> VND</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"><i class="fas fa-microphone-alt text-info mr-2"></i>TTS Voice Synthesis</td>
                                    <td class="text-muted">Beeknoee Vietnamese Text-to-Speech</td>
                                    <td class="text-right font-weight-bold text-danger"><?=number_format($bd['tts_cost'])?> VND</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"><i class="fas fa-laptop-code text-success mr-2"></i>Local FFmpeg Composer</td>
                                    <td class="text-muted">Ghép nối video & xuất thành phẩm (0 VND)</td>
                                    <td class="text-right font-weight-bold text-success"><?=number_format($bd['local_render_cost'])?> VND</td>
                                </tr>
                                <tr class="bg-light font-weight-bold">
                                    <td>TỔNG CHI PHÍ THỰC TẾ</td>
                                    <td class="text-muted">Khoảng thời gian: <?=$timeRange?></td>
                                    <td class="text-right text-danger font-weight-bold" style="font-size: 1.1rem;"><?=number_format($costData['actual_cost_vnd'])?> VND</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Admin Budget Override Form -->
            <div class="col-lg-5 col-12">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-key mr-2 text-warning"></i>Phê duyệt Vượt Ngân sách (Admin Override)
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-3">Khi hệ thống chạm trần ngân sách bảo vệ, Admin có thể phê duyệt giải trình vượt hạn mức có lưu vết kiểm toán (Audit Log).</p>
                        <form action="index.php?com=operations&act=override_budget" method="POST">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold">Số tiền vượt bổ sung (VND):</label>
                                <input type="number" name="amount" class="form-control form-control-sm" placeholder="Ví dụ: 100000" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Lý do giải trình (Bắt buộc):</label>
                                <textarea name="reason" rows="3" class="form-control form-control-sm" placeholder="Nhập lý do nghiệp vụ phê duyệt vượt ngân sách..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block font-weight-bold shadow-sm" onclick="return confirm('Xác nhận phê duyệt vượt ngân sách API?');">
                                <i class="fas fa-check-circle mr-1"></i> Xác nhận Phê duyệt Override
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
