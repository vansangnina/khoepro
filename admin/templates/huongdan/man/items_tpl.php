<?php
/**
 * Template: Danh sách & Tìm kiếm Hướng Dẫn Sử Dụng Admin KhoePro
 * Template: admin/templates/huongdan/man/items_tpl.php
 * Constraint: Max font-weight <= 600, Font Inter
 */
?>
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark" style="font-weight: 600; font-family: 'Inter', Arial, sans-serif;">
                    <i class="fas fa-book-reader text-primary mr-2"></i> HƯỚNG DẪN SỬ DỤNG ADMIN
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Hướng dẫn sử dụng</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content text-sm" style="font-family: 'Inter', Arial, sans-serif;">
    <div class="container-fluid">
        <!-- Search Hero Section -->
        <div class="card card-primary card-outline shadow-sm mb-4" style="border-top: 3px solid #007bff;">
            <div class="card-body p-4 text-center bg-light rounded">
                <h3 class="mb-2 text-primary" style="font-weight: 600;">Bạn cần hướng dẫn thực hiện thao tác gì?</h3>
                <p class="text-muted mb-4">Tìm kiếm nhanh theo tên chức năng, thao tác (thêm sản phẩm, tạo video, đối soát đơn hàng,...) hoặc từ khóa</p>
                
                <form action="index.php" method="GET" class="d-flex justify-content-center">
                    <input type="hidden" name="com" value="huongdan">
                    <input type="hidden" name="act" value="man">
                    <div class="input-group" style="max-width: 650px;">
                        <input type="text" name="keyword" class="form-control form-control-lg shadow-sm" placeholder="Nhập từ khóa cần tìm (VD: thêm sản phẩm, xuất bản tiktok, tạo hook ai...)" value="<?= htmlspecialchars($keyword ?? '') ?>" style="border-radius: 8px 0 0 8px; font-size: 15px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary px-4 shadow-sm" type="submit" style="font-weight: 600; border-radius: 0 8px 8px 0;">
                                <i class="fas fa-search mr-1"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($keyword)) { ?>
                    <div class="mt-3">
                        <span class="text-muted">Kết quả tìm kiếm cho từ khóa: <strong>"<?= htmlspecialchars($keyword) ?>"</strong> (<?= count($articles) ?> bài viết)</span>
                        <a href="index.php?com=huongdan&act=man" class="btn btn-sm btn-outline-secondary ml-2">
                            <i class="fas fa-times mr-1"></i> Xóa bộ lọc tìm kiếm
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Categories Navigation Pills -->
        <div class="mb-4">
            <h5 class="mb-3 text-secondary" style="font-weight: 600;"><i class="fas fa-layer-group mr-2"></i> DANH MỤC HƯỚNG DẪN THEO CHUYÊN ĐỀ</h5>
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <a href="index.php?com=huongdan&act=man" class="btn btn-block p-3 text-left shadow-sm <?= empty($category) && empty($keyword) ? 'btn-primary' : 'btn-outline-primary bg-white' ?>" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-th-large mr-2"></i> Tất cả hướng dẫn
                    </a>
                </div>
                <?php foreach ($categories as $catId => $cat) { ?>
                    <div class="col-6 col-md-3 mb-3">
                        <a href="index.php?com=huongdan&act=man&category=<?= $catId ?>" class="btn btn-block p-3 text-left shadow-sm <?= ($category === $catId) ? 'btn-primary' : 'btn-outline-secondary bg-white text-dark' ?>" style="border-radius: 8px; font-weight: 500;">
                            <i class="<?= $cat['icon'] ?> text-primary mr-2"></i> <?= $cat['name'] ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="row">
            <?php if (empty($articles)) { ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-secondary" style="font-weight: 600;">Không tìm thấy bài hướng dẫn phù hợp</h4>
                        <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc duyệt theo danh mục ở trên.</p>
                        <a href="index.php?com=huongdan&act=man" class="btn btn-primary mt-2" style="font-weight: 600;">
                            <i class="fas fa-arrow-left mr-1"></i> Xem tất cả hướng dẫn
                        </a>
                    </div>
                </div>
            <?php } else { ?>
                <?php foreach ($articles as $art) { 
                    $catMeta = $categories[$art['category_id']] ?? array('name' => 'Chung', 'badge' => 'badge-secondary');
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 transition-hover" style="border-radius: 10px; overflow: hidden; border-top: 3px solid #007bff;">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge <?= $catMeta['badge'] ?> px-2 py-1" style="font-weight: 500; font-size: 11px;">
                                        <?= $catMeta['name'] ?>
                                    </span>
                                    <span class="text-muted" style="font-size: 11px;"><i class="far fa-clock mr-1"></i> <?= count($art['steps']) ?> bước</span>
                                </div>
                                <h5 class="card-title mb-2 text-dark" style="font-weight: 600; font-size: 16px; line-height: 1.4;">
                                    <a href="index.php?com=huongdan&act=detail&id=<?= $art['id'] ?>" class="text-dark text-decoration-none hover-primary">
                                        <?= $art['title'] ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted flex-grow-1 mb-3" style="font-size: 13px; line-height: 1.5;">
                                    <?= $art['summary'] ?>
                                </p>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= mb_substr($art['menu_path'], 0, 30) ?>...</small>
                                    <a href="index.php?com=huongdan&act=detail&id=<?= $art['id'] ?>" class="btn btn-sm btn-primary px-3" style="font-weight: 600; border-radius: 6px;">
                                        Xem chi tiết <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>

<style>
.hover-primary:hover {
    color: #007bff !important;
}
.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
}
</style>
