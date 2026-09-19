<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkAssets = "index.php?com=ai_video&act=assets";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Kho Tài nguyên Trực quan Video (Video Assets Manager)
                </h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-images mr-2"></i>Danh sách Tài nguyên Ánh xạ Phân cảnh</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-nowrap">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Xem trước</th>
                            <th>Loại Tài nguyên</th>
                            <th>Nguồn (Source)</th>
                            <th>Dự án Video</th>
                            <th>Sản phẩm</th>
                            <th>Phân cảnh (Scene)</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($assets)): foreach ($assets as $ast): ?>
                            <tr>
                                <td>#<?=$ast['id']?></td>
                                <td style="width: 70px;">
                                    <?php if (!empty($ast['source_ref']) && file_exists($ast['source_ref'])): ?>
                                        <img src="<?=$ast['source_ref']?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light p-2 border text-center rounded"><i class="fas fa-file-image text-secondary"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-info"><?=$ast['asset_type']?></span></td>
                                <td><span class="badge badge-secondary"><?=$ast['source_type']?></span></td>
                                <td>
                                    <a href="index.php?com=ai_video&act=view&id=<?=$ast['id_video']?>">
                                        <strong><?=htmlspecialchars($ast['video_title'] ?: 'Video #'.$ast['id_video'])?></strong>
                                    </a>
                                </td>
                                <td><?=htmlspecialchars($ast['product_name'] ?: 'N/A')?></td>
                                <td><span class="badge badge-dark">Scene #<?=$ast['scene_number']?></span></td>
                                <td><small class="text-muted"><?=htmlspecialchars($ast['notes'] ?? '')?></small></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có tài nguyên nào được ánh xạ.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($paging)): ?>
                <div class="card-footer clearfix"><?=$paging?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
