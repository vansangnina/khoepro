<?php
$linkOverview = "index.php?com=operations&act=overview";
$linkSettings = "index.php?com=operations&act=settings";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-sliders-h mr-2 text-secondary"></i>Cấu hình Vận hành & Ngưỡng Giám sát (Operations Settings)
                </h1>
                <small class="text-muted">Quản lý Master Automation Switches, hạn mức ngân sách ngày/tháng và các ngưỡng cảnh báo</small>
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
        <form action="index.php?com=operations&act=save_settings" method="POST">
            <div class="row">
                <!-- 1. Automation Switches -->
                <div class="col-lg-6 col-12">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white font-weight-bold">
                            <i class="fas fa-toggle-on mr-2 text-primary"></i>Công tắc Tự động hóa (Automation Switches)
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="font-weight-bold mb-0 text-dark">Master Automation Switch (Toàn bộ hệ thống)</label>
                                    <small class="text-muted d-block">Khi TẮT: Không có background worker nào nhận job mới</small>
                                </div>
                                <select name="data[automation_enabled]" class="form-control form-control-sm" style="width: 110px;">
                                    <option value="1" <?=($settingsData['automation_enabled'] == 1) ? 'selected' : ''?>>BẬT (ON)</option>
                                    <option value="0" <?=($settingsData['automation_enabled'] == 0) ? 'selected' : ''?>>TẮT (OFF)</option>
                                </select>
                            </div>

                            <div class="form-group mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="font-weight-normal mb-0">Research Automation</label>
                                    <small class="text-muted d-block">Tự động quét sản phẩm theo Seed</small>
                                </div>
                                <select name="data[research_automation_enabled]" class="form-control form-control-sm" style="width: 110px;">
                                    <option value="1" <?=($settingsData['research_automation_enabled'] == 1) ? 'selected' : ''?>>BẬT</option>
                                    <option value="0" <?=($settingsData['research_automation_enabled'] == 0) ? 'selected' : ''?>>TẮT</option>
                                </select>
                            </div>

                            <div class="form-group mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="font-weight-normal mb-0">Content Automation</label>
                                    <small class="text-muted d-block">Tự động sinh kịch bản TikTok/SEO</small>
                                </div>
                                <select name="data[content_automation_enabled]" class="form-control form-control-sm" style="width: 110px;">
                                    <option value="1" <?=($settingsData['content_automation_enabled'] == 1) ? 'selected' : ''?>>BẬT</option>
                                    <option value="0" <?=($settingsData['content_automation_enabled'] == 0) ? 'selected' : ''?>>TẮT</option>
                                </select>
                            </div>

                            <div class="form-group mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="font-weight-normal mb-0">Video Render Automation</label>
                                    <small class="text-muted d-block">Tự động ghép nối video FFmpeg</small>
                                </div>
                                <select name="data[video_automation_enabled]" class="form-control form-control-sm" style="width: 110px;">
                                    <option value="1" <?=($settingsData['video_automation_enabled'] == 1) ? 'selected' : ''?>>BẬT</option>
                                    <option value="0" <?=($settingsData['video_automation_enabled'] == 0) ? 'selected' : ''?>>TẮT</option>
                                </select>
                            </div>

                            <div class="form-group mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="font-weight-normal mb-0">Publishing Automation</label>
                                    <small class="text-muted d-block">Tự động xuất bản bài đăng theo lịch</small>
                                </div>
                                <select name="data[publishing_automation_enabled]" class="form-control form-control-sm" style="width: 110px;">
                                    <option value="1" <?=($settingsData['publishing_automation_enabled'] == 1) ? 'selected' : ''?>>BẬT</option>
                                    <option value="0" <?=($settingsData['publishing_automation_enabled'] == 0) ? 'selected' : ''?>>TẮT</option>
                                </select>
                            </div>

                            <div class="form-group mb-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="font-weight-normal mb-0">Optimization Evaluation Automation</label>
                                    <small class="text-muted d-block">Tự động đánh giá hiệu năng A/B</small>
                                </div>
                                <select name="data[optimization_automation_enabled]" class="form-control form-control-sm" style="width: 110px;">
                                    <option value="1" <?=($settingsData['optimization_automation_enabled'] == 1) ? 'selected' : ''?>>BẬT</option>
                                    <option value="0" <?=($settingsData['optimization_automation_enabled'] == 0) ? 'selected' : ''?>>TẮT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Budget & Health Thresholds -->
                <div class="col-lg-6 col-12">
                    <!-- Budget Guards -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white font-weight-bold">
                            <i class="fas fa-coins mr-2 text-success"></i>Hạn mức Ngân sách API (Budget Guards)
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Ngân sách API Ngày tối đa (VND):</label>
                                <input type="number" name="data[daily_external_api_budget]" class="form-control form-control-sm" value="<?=$settingsData['daily_external_api_budget']?>" required>
                                <small class="text-muted">Khi chạm ngưỡng này, toàn bộ API tính phí bên ngoài sẽ bị tự động khóa.</small>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Ngân sách API Tháng tối đa (VND):</label>
                                <input type="number" name="data[monthly_external_api_budget]" class="form-control form-control-sm" value="<?=$settingsData['monthly_external_api_budget']?>" required>
                                <small class="text-muted">Hạn mức ngân sách kiểm soát chi phí tổng trong tháng.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Health & Stale Thresholds -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-white font-weight-bold">
                            <i class="fas fa-heartbeat mr-2 text-danger"></i>Ngưỡng Cảnh báo Sức khỏe (Health Thresholds)
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-2">
                                <label class="font-weight-normal mb-1">Ngưỡng Heartbeat Worker (giây):</label>
                                <input type="number" name="data[worker_heartbeat_threshold_seconds]" class="form-control form-control-sm" value="<?=$settingsData['worker_heartbeat_threshold_seconds']?>" required>
                            </div>

                            <div class="form-group mb-2">
                                <label class="font-weight-normal mb-1">Ngưỡng phát hiện Tác vụ Kẹt (Stuck Job - giây):</label>
                                <input type="number" name="data[stuck_job_threshold_seconds]" class="form-control form-control-sm" value="<?=$settingsData['stuck_job_threshold_seconds']?>" required>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-normal mb-1">Ngưỡng Stale Tracking Event (giờ):</label>
                                <input type="number" name="data[tracking_stale_threshold_hours]" class="form-control form-control-sm" value="<?=$settingsData['tracking_stale_threshold_hours']?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3 text-right">
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> Lưu Cấu hình Vận hành
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
