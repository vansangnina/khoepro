<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkSave = "index.php?com=ai_video&act=save_create";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Tạo Dự án Video Mới (AI Video Project)
                </h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="<?=$linkSave?>" method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-magic mr-2 text-primary"></i>Chọn Kịch bản TikTok Đã Phê Duyệt</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Kịch bản TikTok Nguồn (Approved Script): <span class="text-danger">*</span></label>
                                <select name="id_content" class="form-control select2" required style="width: 100%;">
                                    <option value="">-- Chọn kịch bản TikTok đã duyệt --</option>
                                    <?php if (!empty($approvedScripts)): foreach ($approvedScripts as $sc): ?>
                                        <option value="<?=$sc['id']?>">
                                            [#<?=$sc['id']?>] <?=htmlspecialchars($sc['product_name'])?> - <?=htmlspecialchars($sc['title'])?> (<?=$sc['target_duration']?>s)
                                        </option>
                                    <?php endforeach; else: ?>
                                        <option value="" disabled>Chưa có kịch bản TikTok nào ở trạng thái APPROVED. Vui lòng duyệt kịch bản tại module AI Content.</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Chỉ hiển thị các kịch bản TikTok đã được Admin phê duyệt (Human Gate).</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Tiêu đề Dự án Video (Tùy chọn):</label>
                                <input type="text" name="title" class="form-control" placeholder="Để trống để tự động lấy theo tiêu đề kịch bản">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Định dạng Video:</label>
                                        <select name="video_type" class="form-control">
                                            <option value="TIKTOK_9_16" selected>TikTok / Reels / Shorts (Dọc 9:16)</option>
                                            <option value="PRODUCT_SHOWCASE">Showcase Vuông (1:1)</option>
                                            <option value="YOUTUBE_SHORTS">YouTube Ngang (16:9)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tỷ lệ khung hình:</label>
                                        <select name="aspect_ratio" class="form-control">
                                            <option value="9:16" selected>9:16 (1080 x 1920 px) - Khuyên dùng</option>
                                            <option value="1:1">1:1 (1080 x 1080 px)</option>
                                            <option value="16:9">16:9 (1920 x 1080 px)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-outline card-secondary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-cogs mr-2"></i>Cấu hình Giọng đọc & Template</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Giọng đọc Tiếng Việt (TTS):</label>
                                <select name="voice_id" class="form-control">
                                    <?php foreach ($voicesList as $vKey => $vVal): ?>
                                        <option value="<?=$vKey?>"><?=htmlspecialchars($vVal['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Mẫu Visual Presentation:</label>
                                <select name="template_id" class="form-control">
                                    <?php foreach ($templatesList as $tKey => $tVal): ?>
                                        <option value="<?=$tKey?>"><?=htmlspecialchars($tVal['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Nhà cung cấp Render (Provider):</label>
                                <select name="provider" class="form-control">
                                    <option value="mock" selected>Mock Video Engine (Mặc định)</option>
                                    <option value="beeknoee">Beeknoee AI Video (Veo-3.1 Model)</option>
                                    <option value="creatify">Creatify AI Video (Thương mại)</option>
                                    <option value="arcads">Arcads AI (Thương mại)</option>
                                    <option value="manual">Manual DIY Upload (Tải lên thủ công)</option>
                                </select>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm">
                                <i class="fas fa-layer-group mr-1"></i> Khởi tạo Dự án Video
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
