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
                    <i class="fas fa-sliders-h mr-2 text-secondary"></i>Cấu hình Quy tắc Tối ưu hóa (Optimization Rules)
                </h1>
                <small class="text-muted">Quản lý hạn mức ngân sách, ngưỡng kích thước mẫu kiểm thử và thời gian giãn cách</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkRecommendations?>" class="btn btn-sm btn-outline-warning text-dark font-weight-bold shadow-sm mr-1">
                    <i class="fas fa-lightbulb mr-1"></i> Xem Khuyến nghị
                </a>
                <a href="<?=$linkExperiments?>" class="btn btn-sm btn-outline-info shadow-sm">
                    <i class="fas fa-flask mr-1"></i> Xem Thử nghiệm
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <form action="index.php?com=optimization&act=save_rules" method="POST">
            <div class="row">
                <!-- 1. Cost & Budget Limits -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white font-weight-bold py-3">
                            <i class="fas fa-coins text-warning mr-1"></i> Hạn mức Ngân sách & Kiểm soát Chi phí
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark">Hạn mức chi phí tối đa cho 1 thử nghiệm (VND):</label>
                                <div class="input-group">
                                    <input type="number" name="max_cost_per_experiment" class="form-control" value="<?=$currentSettings['max_cost_per_experiment'] ?? 60000?>" required>
                                    <div class="input-group-append"><span class="input-group-text font-weight-bold">VND</span></div>
                                </div>
                                <small class="text-muted">Nếu chi phí dự toán vượt quá hạn mức này, hệ thống sẽ chặn phê duyệt (Hard Cost Gate) trừ khi có quyền Admin Override.</small>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark">Số thử nghiệm đồng thời tối đa cho 1 sản phẩm:</label>
                                <input type="number" name="max_active_experiments_per_product" class="form-control" value="<?=$currentSettings['max_active_experiments_per_product'] ?? 2?>" required min="1" max="10">
                                <small class="text-muted">Ngăn chặn việc tạo quá nhiều thử nghiệm song song gây nhiễu phân bổ nguồn traffic.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Sample Size & Cooldown Rules -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white font-weight-bold py-3">
                            <i class="fas fa-stopwatch text-primary mr-1"></i> Kích thước Mẫu & Giãn cách (Cooldown)
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark">Thời gian giãn cách tạo Khuyến nghị trùng lặp (Giờ):</label>
                                <div class="input-group">
                                    <input type="number" name="recommendation_cooldown_hours" class="form-control" value="<?=$currentSettings['recommendation_cooldown_hours'] ?? 24?>" required min="1">
                                    <div class="input-group-append"><span class="input-group-text font-weight-bold">Giờ</span></div>
                                </div>
                                <small class="text-muted">Tránh spam tạo lại cùng loại khuyến nghị cho sản phẩm nếu số liệu chưa có biến động mới.</small>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="font-weight-bold text-dark">Sessions tối thiểu kết luận:</label>
                                    <input type="number" name="experiment_min_sessions" class="form-control" value="<?=$currentSettings['experiment_min_sessions'] ?? 30?>" required min="10">
                                    <small class="text-muted">Ngưỡng sessions tối thiểu của biến thể mới.</small>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="font-weight-bold text-dark">Clicks tối thiểu kết luận:</label>
                                    <input type="number" name="experiment_min_clicks" class="form-control" value="<?=$currentSettings['experiment_min_clicks'] ?? 10?>" required min="3">
                                    <small class="text-muted">Ngưỡng clicks tối thiểu của biến thể mới.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 text-right">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Lưu Cấu hình Quy tắc
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
