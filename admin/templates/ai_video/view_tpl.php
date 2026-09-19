<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkApprove = "index.php?com=ai_video&act=approve&id=" . $item['id'];
$linkRender = "index.php?com=ai_video&act=render_now&id=" . $item['id'];

$mode = !empty($item['mode']) ? strtoupper($item['mode']) : 'ECONOMY';
$modeBadgeClass = ($mode === 'ECONOMY') ? 'badge-success' : (($mode === 'HYBRID') ? 'badge-info' : 'badge-danger');
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark" style="font-size: 1.35rem;">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Chi tiết Dự án Video #<?=$item['id']?> (v<?=$item['version']?>)
                    <span class="badge <?=$modeBadgeClass?> ml-2 font-weight-bold" style="font-size: 0.9rem;"><?=$mode?> MODE</span>
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <?php if ($item['status'] === 'REVIEW_REQUIRED' || $item['status'] === 'RENDERED'): ?>
                    <a href="<?=$linkApprove?>" class="btn btn-success mr-2 shadow-sm" onclick="return confirm('Xác nhận phê duyệt Video này? Video sẽ sẵn sàng cho giai đoạn phân phối.');">
                        <i class="fas fa-check-circle mr-1"></i> Phê duyệt Video (Approve)
                    </a>
                    <button type="button" class="btn btn-danger shadow-sm" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times-circle mr-1"></i> Từ chối (Reject)
                    </button>
                <?php elseif ($item['status'] === 'WAITING_ASSET'): ?>
                    <button type="button" class="btn btn-warning mr-2 shadow-sm" data-toggle="modal" data-target="#uploadAssetModal">
                        <i class="fas fa-upload mr-1"></i> Tải ảnh sản phẩm
                    </button>
                    <a href="<?=$linkRender?>" class="btn btn-primary shadow-sm" onclick="return confirm('Kích hoạt tiến trình Video Composer render ngay?');">
                        <i class="fas fa-bolt mr-1"></i> Kích hoạt Video Composer
                    </a>
                <?php elseif (in_array($item['status'], array('READY', 'FAILED', 'REJECTED', 'DRAFT'))): ?>
                    <button type="button" class="btn btn-outline-secondary mr-2 shadow-sm" data-toggle="modal" data-target="#uploadAssetModal">
                        <i class="fas fa-upload mr-1"></i> Đổi ảnh sản phẩm
                    </button>
                    <a href="<?=$linkRender?>" class="btn btn-primary shadow-sm" onclick="return confirm('Kích hoạt tiến trình Video Composer render ngay?');">
                        <i class="fas fa-bolt mr-1"></i> Kích hoạt Video Composer
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Cảnh báo Outdated nếu có -->
        <?php if (!empty($item['is_outdated'])): ?>
            <div class="alert alert-danger shadow-sm">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Cảnh báo Kịch bản Đã Thay Đổi (Outdated Script)!</h5>
                Kịch bản TikTok gốc trong module AI Content đã được cập nhật sau thời điểm video này được cấu hình. Bạn nên cân nhắc tạo lại dự án mới để đồng bộ nội dung.
            </div>
        <?php endif; ?>

        <?php if ($item['status'] === 'WAITING_ASSET'): ?>
            <div class="alert alert-warning shadow-sm border-warning">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h5 class="font-weight-bold mb-2"><i class="icon fas fa-info-circle"></i> Sản phẩm chưa có File hình ảnh thực tế</h5>
                        <p class="mb-2">Bạn có thể chọn 1 trong 2 phương án bên dưới để tiếp tục tiến trình sản xuất video:</p>
                        <ul class="mb-0 pl-3">
                            <li class="mb-1"><strong>Lựa chọn 1 (Khuyên dùng khi chưa có ảnh chụp):</strong> Nhấn nút <strong>"Kích hoạt Video Composer"</strong> để hệ thống tự động sinh ảnh/visual và áp dụng chuyển động Ken Burns.</li>
                            <li><strong>Lựa chọn 2 (Chuẩn xác theo sản phẩm thật):</strong> Nhấn <strong>"Tải ảnh sản phẩm"</strong> hoặc chọn file bên dưới để hệ thống tự động ánh xạ ảnh thật vào tất cả phân cảnh rồi render.</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <form action="index.php?com=ai_video&act=upload_asset" method="POST" enctype="multipart/form-data" class="form-inline">
                        <input type="hidden" name="id_video" value="<?=$item['id']?>">
                        <label class="mr-2 font-weight-bold"><i class="fas fa-file-image mr-1"></i> Upload nhanh ảnh sản phẩm:</label>
                        <input type="file" name="file" class="form-control-file d-inline-block w-auto mr-2" accept="image/*" required>
                        <button type="submit" class="btn btn-sm btn-dark font-weight-bold"><i class="fas fa-cloud-upload-alt mr-1"></i> Tải lên & Đồng bộ ngay</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Cột trái: Trình phát Video HTML5 Preview & Thông tin kỹ thuật -->
            <div class="col-lg-4">
                <div class="card card-outline card-primary shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-play mr-2 text-primary"></i>Trình phát Video Preview</h3>
                        <div class="card-tools">
                            <span class="badge badge-info"><?=$item['aspect_ratio']?></span>
                        </div>
                    </div>
                    <div class="card-body text-center p-3 bg-dark rounded-bottom">
                        <?php if (!empty($item['video_file']) && file_exists($item['video_file'])): ?>
                            <video width="100%" height="450" controls style="max-height: 480px; border-radius: 8px; background: #000;" poster="<?=$item['thumbnail'] ?? ''?>">
                                <source src="<?=$item['video_file']?>" type="video/mp4">
                                Trình duyệt của bạn không hỗ trợ thẻ video HTML5.
                            </video>
                            <div class="mt-3 text-left">
                                <a href="<?=$item['video_file']?>" download class="btn btn-sm btn-outline-light btn-block"><i class="fas fa-download mr-1"></i> Tải file MP4 về máy</a>
                            </div>
                        <?php else: ?>
                            <div class="py-5 text-secondary">
                                <i class="fas fa-video-slash fa-4x mb-3 d-block"></i>
                                <p class="text-light mb-1">Chưa có file video thành phẩm</p>
                                <small class="text-muted">Trạng thái: <strong><?=$item['status']?></strong></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Thẻ Chi tiết Chi phí Sản xuất (Cost Report) -->
                <div class="card card-outline card-warning shadow-sm mb-4">
                    <div class="card-header py-2">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-coins mr-2 text-warning"></i>Báo cáo Chi phí Sản xuất (Cost Report)</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tr>
                                <th>Chế độ (Mode):</th>
                                <td><span class="badge <?=$modeBadgeClass?> font-weight-bold"><?=$mode?></span></td>
                            </tr>
                            <tr>
                                <th>Local Render Cost:</th>
                                <td class="text-success font-weight-bold">0 VND (Miễn phí)</td>
                            </tr>
                            <tr>
                                <th>Số giây AI Video:</th>
                                <td><?=(int)($item['ai_video_seconds'] ?? 0)?> giây</td>
                            </tr>
                            <tr>
                                <th>Chi phí AI Video:</th>
                                <td><?=number_format((float)($item['ai_video_cost'] ?? 0))?> VND</td>
                            </tr>
                            <tr>
                                <th>Chi phí TTS Voice:</th>
                                <td><?=number_format((float)($item['tts_cost'] ?? 0))?> VND</td>
                            </tr>
                            <tr class="bg-light">
                                <th class="font-weight-bold">Tổng Chi phí API:</th>
                                <td class="font-weight-bold text-primary" style="font-size: 1.05rem;">
                                    <?=number_format((float)($item['total_external_api_cost'] ?? 0))?> VND
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Thẻ Thông số Kỹ thuật & Quality Check -->
                <div class="card card-outline card-secondary shadow-sm mb-4">
                    <div class="card-header py-2">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-sliders-h mr-2"></i>Thông số & Kiểm định Media</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tr>
                                <th>Thời lượng:</th>
                                <td><?=$item['duration_actual'] ? round($item['duration_actual'], 1).'s (Thực tế)' : $item['target_duration'].'s (Mục tiêu)'?></td>
                            </tr>
                            <tr>
                                <th>Kích thước:</th>
                                <td><?=($item['width'] && $item['height']) ? $item['width'].'x'.$item['height'].' px' : '1080x1920 (9:16)'?></td>
                            </tr>
                            <tr>
                                <th>Dung lượng:</th>
                                <td><?=$item['file_size'] ? round($item['file_size'] / (1024 * 1024), 2).' MB' : 'N/A'?></td>
                            </tr>
                            <tr>
                                <th>Giọng đọc TTS:</th>
                                <td><?=htmlspecialchars($item['voice_id'])?></td>
                            </tr>
                            <tr>
                                <th>Template Visual:</th>
                                <td><span class="badge badge-secondary"><?=$item['template_id']?></span></td>
                            </tr>
                            <tr>
                                <th>Nhà cung cấp:</th>
                                <td><span class="badge badge-light border text-uppercase"><?=$item['provider']?></span></td>
                            </tr>
                            <?php if (!empty($qcReport)): ?>
                                <tr>
                                    <th>Điểm Media QC:</th>
                                    <td>
                                        <span class="badge badge-<?=$qcReport['passed'] ? 'success' : 'danger'?> font-weight-bold">
                                            <?=$qcReport['score']?> / 100
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Lịch sử Phiên bản -->
                <?php if (!empty($historyList)): ?>
                    <div class="card card-outline card-info shadow-sm mb-4">
                        <div class="card-header py-2">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-2"></i>Phiên bản khác của sản phẩm</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($historyList as $his): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <strong>Version v<?=$his['version']?></strong>
                                            <span class="badge badge-light border ml-1"><?=$his['status']?></span>
                                            <div class="small text-muted"><?=date('d/m/Y H:i', $his['date_created'])?></div>
                                        </div>
                                        <a href="index.php?com=ai_video&act=view&id=<?=$his['id']?>" class="btn btn-xs btn-outline-info">Xem</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cột phải: Bảng Phân cảnh Shot Plan & Ánh xạ Tài nguyên -->
            <div class="col-lg-8">
                <!-- Thông tin Sản phẩm & Kịch bản gốc -->
                <div class="card card-outline card-info shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-box-open mr-2 text-info"></i>Sản phẩm & Kịch bản TikTok Nguồn</h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <?php if (!empty($product['photo']) && file_exists('upload/product/'.$product['photo'])): ?>
                                    <img src="upload/product/<?=$product['photo']?>" class="img-fluid rounded border" style="max-height: 80px;">
                                <?php else: ?>
                                    <i class="fas fa-image fa-3x text-secondary"></i>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-10">
                                <h5 class="font-weight-bold mb-1"><?=htmlspecialchars($product['namevi'] ?? 'N/A')?></h5>
                                <div class="text-muted small mb-2">
                                    Giá bán: <strong class="text-danger"><?=number_format((float)($product['sale_price'] ?: $product['regular_price']))?> đ</strong>
                                    | Kịch bản nguồn: <a href="index.php?com=ai_content&act=view&id=<?=$content['id'] ?? 0?>" target="_blank"><strong><?=htmlspecialchars($content['title'] ?? 'N/A')?></strong> <i class="fas fa-external-link-alt fa-xs"></i></a>
                                </div>
                                <div class="bg-light p-2 rounded small border">
                                    <i class="fas fa-quote-left mr-1 text-secondary"></i>
                                    <strong>Hook mở đầu (0-3s):</strong> <?=htmlspecialchars($content['title'] ?? '')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng Phân cảnh Chi tiết (Scene by Scene Shot Plan) -->
                <div class="card card-outline card-success shadow-sm mb-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-film mr-2 text-success"></i>Bảng Phân cảnh Mục tiêu (Shot Plan & Purpose Flow)</h3>
                        <div class="card-tools">
                            <span class="badge badge-success"><?=count($scenes)?> Phân cảnh</span>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 45px;" class="text-center">Cảnh</th>
                                    <th style="width: 130px;">Purpose & Phương thức</th>
                                    <th>Chỉ dẫn Khung hình & Chuyển động</th>
                                    <th>Lời lồng tiếng (Voiceover)</th>
                                    <th>Phụ đề Safe-area</th>
                                    <th style="width: 100px;" class="text-center">Tài nguyên</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($scenes)): foreach ($scenes as $sc): 
                                    $pBadge = ($sc['purpose'] === 'HOOK') ? 'badge-danger' : (($sc['purpose'] === 'CTA') ? 'badge-danger' : (($sc['purpose'] === 'PROBLEM') ? 'badge-warning text-dark' : 'badge-primary'));
                                    $mBadge = (!empty($sc['render_method']) && $sc['render_method'] === 'AI_VIDEO') ? 'badge-info' : 'badge-success';
                                ?>
                                    <tr>
                                        <td class="text-center font-weight-bold align-middle">
                                            <span class="badge badge-dark">#<?=$sc['scene_number']?></span>
                                            <div class="small text-muted mt-1"><?=$sc['duration']?>s</div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge <?=$pBadge?> font-weight-bold d-block mb-1"><?=htmlspecialchars($sc['purpose'] ?? 'BENEFIT')?></span>
                                            <span class="badge <?=$mBadge?> d-block mb-1"><?=htmlspecialchars($sc['render_method'] ?? 'LOCAL')?></span>
                                            <?php if (!empty($sc['motion_effect'])): ?>
                                                <small class="text-muted d-block"><i class="fas fa-film mr-1"></i><?=$sc['motion_effect']?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <strong><i class="fas fa-camera mr-1 text-primary"></i>Visual:</strong><br>
                                            <?=htmlspecialchars($sc['visual_instruction'])?>
                                        </td>
                                        <td class="small bg-light">
                                            <strong><i class="fas fa-microphone mr-1 text-success"></i>Voiceover:</strong><br>
                                            <?=htmlspecialchars($sc['voiceover'])?>
                                        </td>
                                        <td class="small">
                                            <span class="badge badge-warning text-dark font-weight-bold p-1">
                                                <?=htmlspecialchars($sc['on_screen_text'])?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center small">
                                            <?php if (!empty($sc['asset_resolved']) && file_exists($sc['asset_resolved'])): ?>
                                                <img src="<?=$sc['asset_resolved']?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" title="<?=$sc['asset_resolved']?>">
                                                <div class="text-success font-weight-bold mt-1" style="font-size: 11px;"><i class="fas fa-check-circle"></i> Đã có</div>
                                            <?php else: ?>
                                                <div class="text-danger font-weight-bold" style="font-size: 11px;"><i class="fas fa-exclamation-circle"></i> Thiếu</div>
                                                <small class="text-muted d-block" style="font-size: 10px;"><?=htmlspecialchars($sc['asset_requirement'])?></small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="6" class="text-center py-3 text-muted">Không có dữ liệu phân cảnh.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Nhật ký Video Composer (Composer Log) -->
                <?php if (!empty($item['composer_log'])): ?>
                    <div class="card card-outline card-dark shadow-sm mb-4">
                        <div class="card-header py-2">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-terminal mr-2 text-dark"></i>Nhật ký Video Composer (Composer Log)</h3>
                        </div>
                        <div class="card-body p-2 bg-dark rounded-bottom">
                            <pre class="text-light small mb-0" style="max-height: 180px; overflow-y: auto; font-family: Consolas, monospace;"><?=htmlspecialchars($item['composer_log'])?></pre>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Lý do từ chối nếu có -->
                <?php if (!empty($item['reject_reason'])): ?>
                    <div class="card card-outline card-danger shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-comment-slash mr-2"></i>Lý do Admin Từ chối</h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-danger font-weight-bold"><?=nl2br(htmlspecialchars($item['reject_reason']))?></p>
                            <small class="text-muted d-block mt-2">Duyệt bởi: <?=htmlspecialchars($item['reviewed_by'] ?? 'Admin')?> vào lúc <?=date('d/m/Y H:i', $item['reviewed_at'] ?? time())?></small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Từ chối Video -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php?com=ai_video&act=reject" method="POST">
                <input type="hidden" name="id" value="<?=$item['id']?>">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-times-circle mr-2"></i>Từ chối Video Dự án #<?=$item['id']?></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Vui lòng nhập lý do từ chối:</label>
                        <textarea name="reject_reason" class="form-control" rows="4" required placeholder="Ví dụ: Giọng đọc sai phát âm thương hiệu, khung hình chưa khớp sản phẩm, chữ phụ đề bị che..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger font-weight-bold">Xác nhận Từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Upload Ảnh Sản phẩm / Asset -->
<div class="modal fade" id="uploadAssetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="index.php?com=ai_video&act=upload_asset" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_video" value="<?=$item['id']?>">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-upload mr-2"></i>Tải lên Ảnh Sản phẩm cho Video #<?=$item['id']?></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Chọn file hình ảnh sản phẩm:</label>
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="customFileProduct" accept="image/*" required>
                            <label class="custom-file-label" for="customFileProduct">Chọn ảnh (.jpg, .png, .webp)...</label>
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> Sau khi tải lên, hệ thống sẽ tự động đồng bộ ảnh này làm tài nguyên cho tất cả phân cảnh trong kịch bản.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-cloud-upload-alt mr-1"></i> Tải lên & Ánh xạ ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>
