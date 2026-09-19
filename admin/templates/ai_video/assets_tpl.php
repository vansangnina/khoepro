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
        <div class="card card-outline card-info shadow-sm text-sm">
            <div class="card-header py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-images mr-2"></i>Danh sách Tài nguyên Ánh xạ Phân cảnh</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="thead-light">
                        <tr class="text-center" style="font-size: 13px;">
                            <th style="width: 50px;">ID</th>
                            <th style="width: 70px;">Xem trước</th>
                            <th style="width: 120px;">Loại Tài nguyên</th>
                            <th style="width: 110px;">Nguồn</th>
                            <th class="text-left" style="min-width: 180px;">Dự án Video</th>
                            <th class="text-left" style="min-width: 180px;">Sản phẩm</th>
                            <th style="width: 90px;">Phân cảnh</th>
                            <th class="text-left" style="min-width: 150px;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($assets)): foreach ($assets as $ast): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-muted">#<?=$ast['id']?></td>
                                <td class="text-center align-middle" style="width: 70px;">
                                    <?php if (!empty($ast['source_ref']) && file_exists($ast['source_ref'])): ?>
                                        <img src="<?=$ast['source_ref']?>" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light p-2 border text-center rounded d-inline-block"><i class="fas fa-file-image text-secondary"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle"><span class="badge badge-info p-1 text-wrap d-block"><?=$ast['asset_type']?></span></td>
                                <td class="text-center align-middle"><span class="badge badge-secondary p-1 text-wrap d-block"><?=$ast['source_type']?></span></td>
                                <td class="align-middle">
                                    <a href="index.php?com=ai_video&act=view&id=<?=$ast['id_video']?>" class="font-weight-bold text-primary" style="word-break: break-word;">
                                        <?=htmlspecialchars($ast['video_title'] ?: 'Video #'.$ast['id_video'])?>
                                    </a>
                                </td>
                                <td class="align-middle" style="word-break: break-word;">
                                    <?=htmlspecialchars($ast['product_name'] ?: 'N/A')?>
                                </td>
                                <td class="text-center align-middle"><span class="badge badge-dark">Scene #<?=$ast['scene_number']?></span></td>
                                <td class="align-middle text-muted text-xs" style="word-break: break-word;">
                                    <?=htmlspecialchars($ast['notes'] ?? '')?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-images fa-2x mb-2 text-secondary d-block"></i>Chưa có tài nguyên nào được ánh xạ.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($paging)): ?>
                <div class="card-footer clearfix py-2"><?=$paging?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
