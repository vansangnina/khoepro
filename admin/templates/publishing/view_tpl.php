<?php
$linkMan = "index.php?com=publishing&act=man";
$linkView = "index.php?com=publishing&act=view";
$platInfo = PublishingCenter::PLATFORMS[$item['platform']] ?? array('name' => $item['platform'], 'badge_class' => 'badge-secondary', 'icon' => 'fas fa-share');
$statusInfo = PublishingCenter::STATUSES[$item['status']] ?? array('name' => $item['status'], 'badge' => 'badge-secondary');

// Chuẩn bị nội dung tổng hợp để sao chép
$fullCopyText = trim($item['caption'] ?? '');
if (!empty($item['hashtags'])) {
    $fullCopyText .= "\n\n" . trim($item['hashtags']);
}
if (!empty($item['disclosure_text'])) {
    $fullCopyText .= "\n\n" . trim($item['disclosure_text']);
}
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-paper-plane mr-2 text-primary"></i>Post Package #<?=$item['id']?>: <?=htmlspecialchars($item['title'])?>
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2 shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Danh sách bài đăng
                </a>
                <a href="index.php?com=publishing&act=duplicate&id=<?=$item['id']?>" class="btn btn-sm btn-outline-primary mr-2 shadow-sm" onclick="return confirm('Nhân bản bài đăng này?')">
                    <i class="fas fa-copy mr-1"></i> Nhân bản
                </a>
                <?php if ($item['status'] !== 'PUBLISHED'): ?>
                    <a href="index.php?com=publishing&act=ready&id=<?=$item['id']?>" class="btn btn-sm btn-info mr-2 shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> Đánh dấu Sẵn sàng (Ready)
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <!-- Thông báo Trạng thái Lỗi / Outdated -->
        <?php if (!empty($item['is_outdated'])): ?>
            <div class="alert alert-danger shadow-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i><strong>CẢNH BÁO NỘI DUNG LỖI THỜI:</strong> Video hoặc thông số sản phẩm gốc đã bị thay đổi sau khi tạo Post Package. Vui lòng kiểm tra lại trước khi xuất bản.
            </div>
        <?php endif; ?>

        <?php if ($item['status'] === 'PUBLISHED'): ?>
            <div class="alert alert-success shadow-sm d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-check-double mr-2 fa-lg"></i>
                    <strong>BÀI ĐĂNG ĐÃ XUẤT BẢN THÀNH CÔNG!</strong> 
                    Thời gian: <strong><?=date('H:i:s d/m/Y', $item['published_at'])?></strong>
                    <?php if (!empty($item['external_post_id'])): ?>
                        | Post ID: <code><?=htmlspecialchars($item['external_post_id'])?></code>
                    <?php endif; ?>
                </div>
                <?php if (!empty($item['external_post_url'])): ?>
                    <a href="<?=$item['external_post_url']?>" target="_blank" class="btn btn-sm btn-light font-weight-bold shadow-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Mở bài đăng trên TikTok
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- CỘT TRÁI: Video Preview & Manual Package Toolkit -->
            <div class="col-lg-5 col-12 mb-3">
                <!-- Video Player Card -->
                <div class="card card-outline card-primary shadow-sm mb-3">
                    <div class="card-header p-2 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold text-dark"><i class="fas fa-video mr-1"></i> Video Xem trước (9:16)</span>
                        <div>
                            <span class="badge <?=$platInfo['badge_class']?>"><?=$platInfo['name']?></span>
                            <span class="badge <?=$statusInfo['badge']?>"><?=$statusInfo['name']?></span>
                        </div>
                    </div>
                    <div class="card-body p-3 text-center bg-dark rounded-bottom">
                        <?php if (!empty($item['video']['video_file']) && file_exists($item['video']['video_file'])): ?>
                            <video controls class="w-100 rounded shadow" style="max-height: 520px; object-fit: contain; background: #000;" poster="<?=$item['video']['thumbnail'] ?? ''?>">
                                <source src="<?=$item['video']['video_file']?>" type="video/mp4">
                                Trình duyệt không hỗ trợ phát video HTML5.
                            </video>
                            <div class="mt-2 d-flex justify-content-between align-items-center text-white-50 small">
                                <span><i class="fas fa-clock mr-1"></i> Thời lượng: <strong><?=round($item['video']['duration_actual'] ?? 0, 1)?>s</strong></span>
                                <span><i class="fas fa-hdd mr-1"></i> Dung lượng: <strong><?=number_format(($item['video']['file_size'] ?? 0) / 1024 / 1024, 2)?> MB</strong></span>
                                <span><i class="fas fa-tag mr-1"></i> Mode: <strong><?=$item['video']['mode'] ?? 'ECONOMY'?></strong></span>
                            </div>
                        <?php else: ?>
                            <div class="py-5 text-muted">
                                <i class="fas fa-video-slash fa-3x mb-2 text-secondary"></i><br>
                                Không tìm thấy tệp video trên máy chủ!
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer p-2 bg-light d-flex justify-content-between">
                        <?php if (!empty($item['video']['video_file']) && file_exists($item['video']['video_file'])): ?>
                            <a href="<?=$item['video']['video_file']?>" download="fitnado_tiktok_post_<?=$item['id']?>.mp4" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
                                <i class="fas fa-download mr-1"></i> Tải Video Về Máy (MP4)
                            </a>
                        <?php endif; ?>
                        <a href="https://www.tiktok.com/creator-center/upload?from=webapp" target="_blank" class="btn btn-sm btn-dark shadow-sm">
                            <i class="fab fa-tiktok mr-1"></i> Mở TikTok Creator Center <i class="fas fa-external-link-alt ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Manual Copy Toolkit Card (1-Click Fast Actions) -->
                <div class="card card-outline card-success shadow-sm mb-3">
                    <div class="card-header p-2 bg-success text-white">
                        <span class="font-weight-bold"><i class="fas fa-copy mr-1"></i> Hộp Công cụ Sao chép Nhanh (1-Click Copy)</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-2">Bấm sao chép nội dung để dán trực tiếp vào giao diện đăng video của TikTok / Mạng xã hội:</p>
                        
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-block text-left mb-1 shadow-sm" onclick="copyToClipboard(document.getElementById('raw_caption').value, 'Đã sao chép Caption!')">
                                <i class="fas fa-quote-left mr-1"></i> <strong>Sao chép Caption</strong>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info btn-block text-left mb-1 shadow-sm" onclick="copyToClipboard(document.getElementById('raw_hashtags').value, 'Đã sao chép Hashtags!')">
                                <i class="fas fa-hashtag mr-1"></i> <strong>Sao chép Hashtags</strong>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success btn-block text-left mb-1 shadow-sm" onclick="copyToClipboard(document.getElementById('raw_landing').value, 'Đã sao chép Link Landing!')">
                                <i class="fas fa-link mr-1"></i> <strong>Sao chép Link Landing Page FITNADO</strong>
                            </button>
                            <button type="button" class="btn btn-sm btn-success btn-block text-left shadow-sm" onclick="copyToClipboard(document.getElementById('raw_full').value, 'Đã sao chép toàn bộ gói Caption + Hashtags + Disclosure!')">
                                <i class="fas fa-clipboard-check mr-1"></i> <strong>Sao chép Trọn bộ (Caption + Hashtags + Disclosure)</strong>
                            </button>
                        </div>

                        <!-- Hidden Textareas for Copying -->
                        <textarea id="raw_caption" style="display: none;"><?=htmlspecialchars($item['caption'])?></textarea>
                        <textarea id="raw_hashtags" style="display: none;"><?=htmlspecialchars($item['hashtags'])?></textarea>
                        <textarea id="raw_landing" style="display: none;"><?=htmlspecialchars($item['landing_url'])?></textarea>
                        <textarea id="raw_full" style="display: none;"><?=htmlspecialchars($fullCopyText)?></textarea>
                    </div>
                </div>

                <!-- Pre-publish Checklist Card -->
                <div class="card card-outline card-info shadow-sm mb-3">
                    <div class="card-header p-2">
                        <span class="font-weight-bold text-dark"><i class="fas fa-tasks mr-1"></i> Bảng Kiểm định Tiền Xuất bản (Pre-publish Checklist)</span>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-video text-muted mr-2"></i> Video đã được duyệt (APPROVED):</span>
                                <?php if (($item['video']['status'] ?? '') === 'APPROVED'): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> ĐẠT</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> KHÔNG ĐẠT</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-file-video text-muted mr-2"></i> Tệp Video MP4 hợp lệ (>1KB):</span>
                                <?php if (!empty($item['video']['video_file']) && file_exists($item['video']['video_file']) && filesize($item['video']['video_file']) > 1000): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> ĐẠT (<?=number_format(filesize($item['video']['video_file'])/1024/1024, 2)?> MB)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> KHÔNG TỒN TẠI</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-box text-muted mr-2"></i> Sản phẩm tồn tại trong CSDL:</span>
                                <?php if (!empty($item['product'])): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> ĐẠT (#<?=$item['id_product']?>)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> THIẾU</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-quote-left text-muted mr-2"></i> Caption không để trống:</span>
                                <?php if (!empty(trim($item['caption']))): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> ĐẠT (<?=mb_strlen($item['caption'], 'UTF-8')?> ký tự)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> TRỐNG</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span><i class="fas fa-user-check text-muted mr-2"></i> Kênh / Tài khoản xuất bản:</span>
                                <span class="badge badge-success"><i class="fas fa-check"></i> <?=htmlspecialchars($item['account']['account_handle'] ?? '@fitnado.vn')?></span>
                            </li>
                        </ul>

                        <div class="mt-3">
                            <?php if ($checklist['valid']): ?>
                                <div class="alert alert-success p-2 mb-0 text-center font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i> TẤT CẢ TIÊU CHÍ ĐỀU ĐẠT CHUẨN!
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger p-2 mb-0 font-weight-bold">
                                    <i class="fas fa-times-circle mr-1"></i> CHƯA ĐẠT YÊU CẦU:
                                    <ul class="mb-0 pl-3">
                                        <?php foreach ($checklist['errors'] as $err): ?>
                                            <li><?=$err?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: Chi tiết Post Package, Biểu mẫu Chỉnh sửa & Lịch sử -->
            <div class="col-lg-7 col-12 mb-3">
                <!-- Action Tabs Card -->
                <div class="card card-outline card-primary shadow-sm mb-3">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills" id="postTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="details-tab" data-toggle="pill" href="#tab-details" role="tab"><i class="fas fa-info-circle mr-1"></i> Chi tiết & Chỉnh sửa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="mark-tab" data-toggle="pill" href="#tab-mark" role="tab"><i class="fas fa-check-square mr-1"></i> Xác nhận Đã Đăng (Publish)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="schedule-tab" data-toggle="pill" href="#tab-schedule" role="tab"><i class="fas fa-clock mr-1"></i> Lên lịch Xuất bản</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="history-tab" data-toggle="pill" href="#tab-history" role="tab"><i class="fas fa-history mr-1"></i> Nhật ký Lịch sử (<?=count($item['logs'] ?? array())?>)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-3">
                        <div class="tab-content" id="postTabContent">
                            <!-- TAB 1: Chi tiết & Chỉnh sửa -->
                            <div class="tab-pane fade show active" id="tab-details" role="tabpanel">
                                <form action="index.php?com=publishing&act=save_edit" method="POST">
                                    <input type="hidden" name="id" value="<?=$item['id']?>">

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Tiêu đề Gói Xuất bản:</label>
                                        <input type="text" name="title" class="form-control" value="<?=htmlspecialchars($item['title'])?>" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-12 form-group mb-3">
                                            <label class="font-weight-bold text-dark">Nền tảng:</label>
                                            <select name="platform" class="form-control">
                                                <?php foreach (PublishingCenter::PLATFORMS as $pKey => $pInfo): ?>
                                                    <option value="<?=$pKey?>" <?= ($item['platform'] === $pKey) ? 'selected' : '' ?>><?=$pInfo['name']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-12 form-group mb-3">
                                            <label class="font-weight-bold text-dark">Phương thức Xuất bản:</label>
                                            <select name="provider" class="form-control">
                                                <option value="manual" <?= ($item['provider'] === 'manual') ? 'selected' : '' ?>>Thủ công (Manual Provider - Khuyên dùng)</option>
                                                <option value="tiktok_api" <?= ($item['provider'] === 'tiktok_api') ? 'selected' : '' ?>>TikTok Content Posting API</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Nội dung Caption Video:</label>
                                        <textarea name="caption" class="form-control" rows="5" required><?=htmlspecialchars($item['caption'])?></textarea>
                                        <small class="form-text text-muted">Văn bản mô tả video, gợi mở tò mò hoặc hướng dẫn xem giỏ hàng.</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Hashtags chiến lược:</label>
                                        <input type="text" name="hashtags" class="form-control" value="<?=htmlspecialchars($item['hashtags'] ?? '')?>">
                                        <small class="form-text text-muted">Các hashtag liên quan trực tiếp đến sản phẩm/gym (vd: <code>#fitnado #daicung #tapgym #reviewgym</code>).</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Affiliate Disclosure / Tuyên bố Liên kết:</label>
                                        <input type="text" name="disclosure_text" class="form-control" value="<?=htmlspecialchars($item['disclosure_text'] ?? '')?>">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Đường dẫn Đích Sản phẩm FITNADO (Landing URL):</label>
                                        <input type="text" class="form-control bg-light" value="<?=htmlspecialchars($item['landing_url'] ?? '')?>" readonly>
                                    </div>

                                    <?php if ($item['status'] !== 'PUBLISHED'): ?>
                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary shadow-sm">
                                                <i class="fas fa-save mr-1"></i> Lưu Cập nhật Post Package
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning p-2 small mb-0">
                                            <i class="fas fa-lock mr-1"></i> Bài đăng này đã xuất bản nên không thể chỉnh sửa trực tiếp. Vui lòng bấm <strong>Nhân bản (Duplicate)</strong> nếu bạn muốn tạo bài mới.
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <!-- TAB 2: Xác nhận Đã Xuất Bản Thủ Công -->
                            <div class="tab-pane fade" id="tab-mark" role="tabpanel">
                                <div class="bg-light p-3 rounded border mb-3">
                                    <h6 class="font-weight-bold text-dark"><i class="fas fa-clipboard-check mr-1 text-success"></i> Quy trình Xuất bản Thủ công (Manual Flow):</h6>
                                    <ol class="small text-muted mb-0 pl-3">
                                        <li>Bấm <strong>"Tải Video Về Máy"</strong> từ cột bên trái.</li>
                                        <li>Bấm <strong>"Sao chép Caption"</strong> và <strong>"Sao chép Hashtags"</strong>.</li>
                                        <li>Mở <a href="https://www.tiktok.com/creator-center/upload?from=webapp" target="_blank">TikTok Creator Center</a> để đăng video.</li>
                                        <li>Sau khi video được duyệt công khai trên TikTok, dán URL bài đăng vào ô bên dưới và bấm <strong>"Xác nhận Đã Xuất Bản"</strong>.</li>
                                    </ol>
                                </div>

                                <form action="index.php?com=publishing&act=mark_published" method="POST">
                                    <input type="hidden" name="id" value="<?=$item['id']?>">

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Đường dẫn Bài đăng TikTok Thật (External Post URL): <span class="text-danger">*</span></label>
                                        <input type="url" name="external_post_url" class="form-control" placeholder="https://www.tiktok.com/@fitnado.vn/video/7345678901234567890" value="<?=htmlspecialchars($item['external_post_url'] ?? '')?>" required>
                                        <small class="form-text text-muted">Hệ thống sẽ tự động xác thực tên miền TikTok hợp lệ và trích xuất Video ID.</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">ID Bài đăng Ngoài (External Post ID - Không bắt buộc):</label>
                                        <input type="text" name="external_post_id" class="form-control" placeholder="Tự động trích xuất từ URL nếu để trống" value="<?=htmlspecialchars($item['external_post_id'] ?? '')?>">
                                    </div>

                                    <button type="submit" class="btn btn-success shadow-sm">
                                        <i class="fas fa-check mr-1"></i> Xác nhận Đã Xuất Bản (Mark as Published)
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 3: Lên lịch Xuất bản -->
                            <div class="tab-pane fade" id="tab-schedule" role="tabpanel">
                                <form action="index.php?com=publishing&act=schedule" method="POST">
                                    <input type="hidden" name="id" value="<?=$item['id']?>">

                                    <div class="row">
                                        <div class="col-md-6 col-12 form-group mb-3">
                                            <label class="font-weight-bold text-dark">Ngày xuất bản:</label>
                                            <input type="date" name="schedule_date" class="form-control" value="<?= !empty($item['scheduled_at']) ? date('Y-m-d', $item['scheduled_at']) : date('Y-m-d', strtotime('+1 day')) ?>" required>
                                        </div>
                                        <div class="col-md-6 col-12 form-group mb-3">
                                            <label class="font-weight-bold text-dark">Giờ xuất bản (Khuyên dùng khung giờ vàng 18:00–21:00):</label>
                                            <input type="time" name="schedule_time" class="form-control" value="<?= !empty($item['scheduled_at']) ? date('H:i', $item['scheduled_at']) : '19:00' ?>" required>
                                        </div>
                                    </div>

                                    <div class="alert alert-info p-2 small mb-3">
                                        <i class="fas fa-info-circle mr-1"></i> Khi đến giờ hẹn lịch:
                                        <ul class="mb-0 pl-3">
                                            <li>Với <strong>Manual Provider</strong>: Trạng thái sẽ tự động chuyển sang <strong>READY</strong> kèm thông báo nhắc Admin đăng bài.</li>
                                            <li>Với <strong>API Provider</strong>: Cron Worker ngầm sẽ tự động kích hoạt tiến trình upload.</li>
                                        </ul>
                                    </div>

                                    <button type="submit" class="btn btn-primary shadow-sm">
                                        <i class="fas fa-calendar-check mr-1"></i> Lưu Lên Lịch Xuất Bản
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 4: Nhật ký Lịch sử & Audit Trail -->
                            <div class="tab-pane fade" id="tab-history" role="tabpanel">
                                <?php if (!empty($item['logs'])): ?>
                                    <div class="timeline timeline-inverse small">
                                        <?php foreach ($item['logs'] as $log): ?>
                                            <div>
                                                <i class="fas fa-circle bg-primary"></i>
                                                <div class="timeline-item shadow-sm">
                                                    <span class="time"><i class="fas fa-clock"></i> <?=date('H:i:s d/m/Y', $log['date_created'])?></span>
                                                    <h3 class="timeline-header font-weight-bold" style="font-size: 0.95rem;">
                                                        Sự kiện: <span class="text-primary"><?=$log['event']?></span> 
                                                        (Bởi: <code><?=$log['actor']?></code>)
                                                    </h3>
                                                    <div class="timeline-body p-2">
                                                        <div>Chuyển trạng thái: 
                                                            <span class="badge badge-secondary"><?=$log['old_status'] ?: 'NONE'?></span> 
                                                            <i class="fas fa-arrow-right text-muted mx-1"></i> 
                                                            <span class="badge badge-success"><?=$log['new_status']?></span>
                                                        </div>
                                                        <?php if (!empty($log['details'])): ?>
                                                            <pre class="bg-light p-2 rounded mt-2 mb-0 text-muted" style="font-size: 0.8rem;"><?=htmlspecialchars($log['details'])?></pre>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        Chưa có bản ghi lịch sử nào.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Snapshot Data Preview (Immutable Frozen Copy) -->
                <?php if (!empty($item['snapshot_data'])): ?>
                    <?php $snap = json_decode($item['snapshot_data'], true); ?>
                    <div class="card card-outline card-secondary shadow-sm">
                        <div class="card-header p-2 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-muted small"><i class="fas fa-lock mr-1 text-warning"></i> Bản ghi Snapshot Bất biến (Frozen at: <?=date('H:i:s d/m/Y', $snap['snapshotted_at'] ?? time())?>)</span>
                            <span class="badge badge-warning">Snapshot Locked</span>
                        </div>
                        <div class="card-body p-2 bg-light">
                            <div class="small text-muted">
                                <strong>Caption đã đóng băng:</strong> <?=htmlspecialchars($snap['caption'] ?? '')?><br>
                                <strong>Hashtags:</strong> <?=htmlspecialchars($snap['hashtags'] ?? '')?><br>
                                <strong>Video Version ID:</strong> #<?=$snap['video_id'] ?? ''?> (<?=round($snap['video_duration'] ?? 0, 1)?>s)
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function copyToClipboard(text, successMsg) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        alert(successMsg);
    }, function(err) {
        // Fallback
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert(successMsg);
    });
}
</script>
