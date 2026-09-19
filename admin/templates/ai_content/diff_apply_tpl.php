<?php
$linkView = "index.php?com=ai_content&act=view&id=" . $item['id'];
$linkApply = "index.php?com=ai_content&act=apply";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="index.php?com=ai_content&act=man">Kho nội dung AI</a></li>
                <li class="breadcrumb-item"><a href="<?= $linkView ?>"><?= htmlspecialchars($item['title']) ?></a></li>
                <li class="breadcrumb-item active">So Sánh & Áp Dụng (Diff & Apply)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="card card-purple card-outline shadow-sm text-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-columns mr-1"></i> Đối Soát Nội Dung Trước Khi Áp Dụng Vào Sản Phẩm: <?= htmlspecialchars($product['namevi']) ?>
            </h3>
            <div class="card-tools">
                <span class="badge badge-success mr-2">Trạng thái: Đã Phê Duyệt</span>
                <span class="badge badge-info">Loại: <?= $item['content_type'] ?></span>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info p-3 text-xs mb-4">
                <h6><i class="fas fa-info-circle mr-1"></i> <strong>Cơ chế Sao lưu An toàn (Reversible Backup):</strong></h6>
                Toàn bộ nội dung hiện tại của sản phẩm sẽ được hệ thống <strong>tự động sao lưu vào bảng Backup</strong> trước khi ghi đè. Bạn có thể hoàn tác lại bất kỳ lúc nào nếu cần.
            </div>

            <!-- Diff Table -->
            <div class="table-responsive">
                <table class="table table-bordered text-sm">
                    <thead class="bg-light text-center">
                        <tr>
                            <th style="width: 20%;">Trường dữ liệu</th>
                            <th style="width: 40%;" class="text-danger bg-pink">
                                <i class="fas fa-history mr-1"></i> Nội dung Hiện tại trên Sản phẩm
                            </th>
                            <th style="width: 40%;" class="text-success bg-light-green">
                                <i class="fas fa-magic mr-1"></i> Nội dung AI Mới Đã Duyệt (Sẽ Áp Dụng)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($newFields as $fieldName => $newVal) {
                            $oldVal = $currentFields[$fieldName] ?? '';
                            $isDiff = (trim($oldVal) !== trim($newVal));
                        ?>
                            <tr>
                                <td class="font-weight-bold text-dark bg-light align-middle">
                                    <?= htmlspecialchars($fieldName) ?>
                                    <?php if ($isDiff) { ?>
                                        <br><span class="badge badge-warning text-xs">Có thay đổi</span>
                                    <?php } else { ?>
                                        <br><span class="badge badge-light border text-xs text-muted">Trùng khớp</span>
                                    <?php } ?>
                                </td>
                                <td class="text-wrap bg-light font-italic text-muted" style="max-height: 250px; overflow-y: auto;">
                                    <?= !empty($oldVal) ? nl2br(htmlspecialchars($oldVal)) : '<em class="text-secondary">(Đang để trống)</em>' ?>
                                </td>
                                <td class="text-wrap bg-white font-weight-bold text-dark" style="max-height: 250px; overflow-y: auto;">
                                    <?= !empty($newVal) ? nl2br(htmlspecialchars($newVal)) : '<em class="text-danger">(Không có dữ liệu mới)</em>' ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-between">
            <a href="<?= $linkView ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại chi tiết
            </a>
            <form method="post" action="<?= $linkApply ?>" onsubmit="return confirm('Bạn có chắc chắn muốn áp dụng nội dung AI này vào sản phẩm? Hệ thống sẽ tạo bản backup trước khi cập nhật.')">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn-purple font-weight-bold">
                    <i class="fas fa-check-double mr-1"></i> Xác nhận Áp Dụng Vào Sản Phẩm Ngay
                </button>
            </form>
        </div>
    </div>
</section>
