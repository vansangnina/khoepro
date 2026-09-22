<?php
/**
 * Template: Chi tiết bài Hướng Dẫn Sử Dụng Admin KhoePro
 * Template: admin/templates/huongdan/man/detail_tpl.php
 * Constraint: Max font-weight <= 600, Font Inter
 */
?>
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="index.php?com=huongdan&act=man">Hướng dẫn sử dụng</a></li>
                    <?php if ($categoryInfo) { ?>
                        <li class="breadcrumb-item"><a href="index.php?com=huongdan&act=man&category=<?= $categoryInfo['id'] ?>"><?= $categoryInfo['name'] ?></a></li>
                    <?php } ?>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($article['title']) ?></li>
                </ol>
            </div>
            <div class="col-sm-4 text-right">
                <a href="index.php?com=huongdan&act=man" class="btn btn-sm btn-outline-secondary" style="font-weight: 500;">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content text-sm pb-5" style="font-family: 'Inter', Arial, sans-serif;">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content Guide Area -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px; overflow: hidden;">
                    <!-- Article Header Banner -->
                    <div class="card-header bg-white p-4 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-primary px-3 py-1 mr-2" style="font-weight: 500; font-size: 12px;">
                                <?= $categoryInfo['name'] ?? 'Hướng dẫn nghiệp vụ' ?>
                            </span>
                            <span class="text-muted"><i class="fas fa-list-ol mr-1"></i> <?= count($article['steps']) ?> bước thực hiện</span>
                        </div>
                        <h2 class="text-dark mb-2" style="font-weight: 600; font-size: 24px; line-height: 1.3;">
                            <?= htmlspecialchars($article['title']) ?>
                        </h2>
                        <p class="text-muted lead mb-0" style="font-size: 15px;">
                            <?= htmlspecialchars($article['summary']) ?>
                        </p>
                    </div>

                    <div class="card-body p-4">
                        <!-- Overview Meta Callout Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="text-primary mb-2" style="font-weight: 600;"><i class="fas fa-bullseye mr-2"></i> Chức năng này dùng để làm gì?</h6>
                                    <p class="text-dark mb-0" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($article['purpose']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="text-success mb-2" style="font-weight: 600;"><i class="fas fa-clock mr-2"></i> Khi nào sử dụng?</h6>
                                    <p class="text-dark mb-0" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($article['when_to_use']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Prerequisites & Access Path -->
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e8f4fd; color: #0c5460; border-radius: 8px;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <strong style="font-weight: 600;"><i class="fas fa-check-circle mr-1"></i> Điều kiện trước khi thực hiện:</strong>
                                    <div class="mt-1" style="font-size: 13.5px;"><?= htmlspecialchars($article['prerequisites']) ?></div>
                                </div>
                                <div class="col-md-6 mt-2 mt-md-0 border-left border-info pl-md-3">
                                    <strong style="font-weight: 600;"><i class="fas fa-route mr-1"></i> Cách truy cập:</strong>
                                    <div class="mt-1 font-weight-500" style="font-size: 13.5px;"><?= htmlspecialchars($article['menu_path']) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step-by-Step Instructions -->
                        <h4 class="mb-3 text-dark border-bottom pb-2" style="font-weight: 600;">
                            <i class="fas fa-tasks text-primary mr-2"></i> CÁC BƯỚC THỰC HIỆN CHI TIẾT
                        </h4>

                        <div class="timeline-steps mb-4">
                            <?php foreach ($article['steps'] as $st) { ?>
                                <div class="step-item p-4 mb-4 bg-white rounded border shadow-sm" style="border-left: 4px solid #007bff !important;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="step-badge mr-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600; font-size: 14px;">
                                            <?= $st['step_num'] ?>
                                        </div>
                                        <h5 class="m-0 text-dark" style="font-weight: 600; font-size: 17px;">
                                            Bước <?= $st['step_num'] ?>: <?= htmlspecialchars($st['title']) ?>
                                        </h5>
                                    </div>
                                    <p class="text-secondary mb-3" style="font-size: 14px; line-height: 1.6;">
                                        <?= htmlspecialchars($st['content']) ?>
                                    </p>
                                    <?php if (!empty($st['image'])) { ?>
                                        <div class="step-image-box text-center p-2 bg-light rounded border mb-2">
                                            <img src="../huongdan/images/<?= htmlspecialchars($st['image']) ?>" alt="Ảnh minh họa bước <?= $st['step_num'] ?>" class="img-fluid rounded shadow-sm" style="max-height: 480px; width: auto; object-fit: contain;">
                                            <div class="small text-muted mt-2"><i class="fas fa-camera mr-1"></i> Ảnh chụp màn hình thực tế vị trí thao tác Bước <?= $st['step_num'] ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Result Section -->
                        <div class="p-3 mb-4 rounded border" style="background-color: #f6fff8; border-color: #c3e6cb !important;">
                            <h5 class="text-success mb-2" style="font-weight: 600;"><i class="fas fa-check-double mr-2"></i> Kết quả sau khi thực hiện</h5>
                            <p class="mb-0 text-dark" style="font-size: 14px; line-height: 1.5;"><?= htmlspecialchars($article['result']) ?></p>
                        </div>

                        <!-- Edit & Delete Instructions -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="text-primary mb-2" style="font-weight: 600;"><i class="fas fa-edit mr-2"></i> Cách chỉnh sửa dữ liệu</h6>
                                    <p class="text-muted mb-0" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($article['edit_instructions']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="text-danger mb-2" style="font-weight: 600;"><i class="fas fa-trash-alt mr-2"></i> Cách xóa & Lưu ý an toàn</h6>
                                    <p class="text-muted mb-0" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($article['delete_instructions']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Warnings & Notice -->
                        <?php if (!empty($article['warnings'])) { ?>
                            <div class="alert alert-warning border-0 shadow-sm mb-4" style="background-color: #fff3cd; color: #856404; border-radius: 8px;">
                                <h6 class="mb-2" style="font-weight: 600;"><i class="fas fa-exclamation-triangle mr-2"></i> LƯU Ý QUAN TRỌNG:</h6>
                                <div style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($article['warnings']) ?></div>
                            </div>
                        <?php } ?>

                        <!-- Troubleshooting -->
                        <?php if (!empty($article['troubleshooting'])) { ?>
                            <div class="p-3 rounded border mb-4" style="background-color: #f8f9fa; border-left: 4px solid #6c757d !important;">
                                <h6 class="text-secondary mb-2" style="font-weight: 600;"><i class="fas fa-wrench mr-2"></i> Xử lý lỗi & Ngộ nhận thường gặp</h6>
                                <div class="text-dark" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($article['troubleshooting']) ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation & Related Guides -->
            <div class="col-lg-3">
                <!-- Quick Navigation Box -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                    <div class="card-header bg-primary text-white p-3">
                        <h6 class="m-0" style="font-weight: 600;"><i class="fas fa-compass mr-2"></i> ĐIỀU HƯỚNG NHANH</h6>
                    </div>
                    <div class="card-body p-3">
                        <a href="index.php?com=huongdan&act=man" class="btn btn-outline-primary btn-sm btn-block text-left mb-2" style="font-weight: 500;">
                            <i class="fas fa-th-large mr-2"></i> Tất cả bài hướng dẫn
                        </a>
                        <?php if ($categoryInfo) { ?>
                            <a href="index.php?com=huongdan&act=man&category=<?= $categoryInfo['id'] ?>" class="btn btn-outline-secondary btn-sm btn-block text-left mb-2 text-dark" style="font-weight: 500;">
                                <i class="<?= $categoryInfo['icon'] ?> text-primary mr-2"></i> <?= $categoryInfo['name'] ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Related Articles -->
                <?php if (!empty($relatedArticles)) { ?>
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                        <div class="card-header bg-white p-3 border-bottom">
                            <h6 class="m-0 text-dark" style="font-weight: 600;"><i class="fas fa-link text-info mr-2"></i> HƯỚNG DẪN LIÊN QUAN</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($relatedArticles as $rArt) { ?>
                                    <li class="mb-3 pb-2 border-bottom">
                                        <a href="index.php?com=huongdan&act=detail&id=<?= $rArt['id'] ?>" class="text-dark text-decoration-none hover-primary d-block" style="font-weight: 500; font-size: 13px; line-height: 1.4;">
                                            <i class="far fa-file-alt text-primary mr-1"></i> <?= $rArt['title'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>

                <!-- Need Help Box -->
                <div class="p-3 bg-white rounded shadow-sm border text-center">
                    <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                    <h6 class="text-dark mb-1" style="font-weight: 600;">Cần hỗ trợ thêm?</h6>
                    <p class="text-muted small mb-2">Liên hệ đội ngũ quản trị kỹ thuật KhoePro nếu gặp sự cố phát sinh.</p>
                    <a href="index.php?com=contact&act=man" class="btn btn-sm btn-primary px-3" style="font-weight: 600;">
                        Xem hòm thư liên hệ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.step-image-box img {
    transition: transform 0.2s ease;
}
.step-image-box img:hover {
    transform: scale(1.01);
}
</style>
