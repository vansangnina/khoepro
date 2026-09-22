<?php
/**
 * Portal Hướng Dẫn Sử Dụng Admin KhoePro (Standalone & Direct Access)
 * Path: /Volumes/CD/web_2026/khoepro/huongdan/index.php
 * Constraint: Max font-weight <= 600, Font Inter
 */

require_once __DIR__ . '/data/guide_data.php';

$act = htmlspecialchars($_GET['act'] ?? 'man');
$id = htmlspecialchars($_GET['id'] ?? '');
$category = htmlspecialchars($_GET['category'] ?? '');
$keyword = htmlspecialchars($_GET['keyword'] ?? '');

$categories = GuideRepository::getCategories();

if ($act === 'detail' && !empty($id)) {
    $article = GuideRepository::getArticle($id);
    if ($article) {
        $categoryInfo = $categories[$article['category_id']] ?? null;
        $relatedArticles = array();
        if (!empty($article['related_links'])) {
            foreach ($article['related_links'] as $rId) {
                $rArt = GuideRepository::getArticle($rId);
                if ($rArt) $relatedArticles[] = $rArt;
            }
        }
    }
} else {
    if (!empty($keyword)) {
        $articles = GuideRepository::searchArticles($keyword);
    } elseif (!empty($category)) {
        $articles = GuideRepository::getArticlesByCategory($category);
    } else {
        $articles = GuideRepository::getArticles();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= !empty($article) ? htmlspecialchars($article['title']) . ' - ' : '' ?>Hướng Dẫn Sử Dụng Admin KhoePro</title>
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 4 & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #212529;
        }
        h1, h2, h3, h4, h5, h6, .font-weight-bold {
            font-weight: 600 !important;
        }
        .navbar-brand {
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        .card {
            border-radius: 10px;
        }
        .step-item {
            border-left: 4px solid #007bff !important;
        }
        .badge {
            font-weight: 500;
        }
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fas fa-book-reader text-primary mr-2 fa-lg"></i>
            <span>KHOEPRO ADMIN GUIDE</span>
        </a>
        <div class="ml-auto d-flex align-items-center">
            <a href="../admin/index.php" class="btn btn-outline-light btn-sm px-3" style="font-weight: 500; border-radius: 6px;">
                <i class="fas fa-arrow-right mr-1"></i> Vào Trang Quản Trị Admin
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <?php if ($act === 'detail' && !empty($article)) { ?>
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ hướng dẫn</a></li>
                <?php if ($categoryInfo) { ?>
                    <li class="breadcrumb-item"><a href="index.php?category=<?= $categoryInfo['id'] ?>"><?= $categoryInfo['name'] ?></a></li>
                <?php } ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['title']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-9">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white p-4 border-bottom">
                        <span class="badge badge-primary px-3 py-1 mb-2"><?= $categoryInfo['name'] ?? 'Chung' ?></span>
                        <h2 class="text-dark mb-2" style="font-size: 24px;"><?= htmlspecialchars($article['title']) ?></h2>
                        <p class="text-muted lead mb-0" style="font-size: 15px;"><?= htmlspecialchars($article['summary']) ?></p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Meta Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="text-primary mb-2"><i class="fas fa-bullseye mr-2"></i> Chức năng này dùng để làm gì?</h6>
                                    <p class="mb-0" style="font-size: 13.5px;"><?= htmlspecialchars($article['purpose']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="text-success mb-2"><i class="fas fa-clock mr-2"></i> Khi nào sử dụng?</h6>
                                    <p class="mb-0" style="font-size: 13.5px;"><?= htmlspecialchars($article['when_to_use']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Prerequisites -->
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e8f4fd; color: #0c5460;">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-check-circle mr-1"></i> Điều kiện trước khi thực hiện:</strong>
                                    <div class="mt-1" style="font-size: 13.5px;"><?= htmlspecialchars($article['prerequisites']) ?></div>
                                </div>
                                <div class="col-md-6 mt-2 mt-md-0 border-left border-info pl-md-3">
                                    <strong><i class="fas fa-route mr-1"></i> Cách truy cập:</strong>
                                    <div class="mt-1" style="font-size: 13.5px;"><?= htmlspecialchars($article['menu_path']) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Steps -->
                        <h4 class="mb-3 text-dark border-bottom pb-2"><i class="fas fa-tasks text-primary mr-2"></i> CÁC BƯỚC THỰC HIỆN CHI TIẾT</h4>
                        <div class="timeline-steps mb-4">
                            <?php foreach ($article['steps'] as $st) { ?>
                                <div class="step-item p-4 mb-4 bg-white rounded border shadow-sm">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="step-badge mr-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">
                                            <?= $st['step_num'] ?>
                                        </div>
                                        <h5 class="m-0 text-dark" style="font-size: 17px;">Bước <?= $st['step_num'] ?>: <?= htmlspecialchars($st['title']) ?></h5>
                                    </div>
                                    <p class="text-secondary mb-3" style="font-size: 14px; line-height: 1.6;"><?= htmlspecialchars($st['content']) ?></p>
                                    <?php if (!empty($st['image'])) { ?>
                                        <div class="step-image-box text-center p-2 bg-light rounded border mb-2">
                                            <img src="images/<?= htmlspecialchars($st['image']) ?>" alt="Ảnh minh họa bước <?= $st['step_num'] ?>" class="img-fluid rounded shadow-sm" style="max-height: 460px;">
                                            <div class="small text-muted mt-2"><i class="fas fa-camera mr-1"></i> Ảnh chụp màn hình thực tế Bước <?= $st['step_num'] ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Result -->
                        <div class="p-3 mb-4 rounded border" style="background-color: #f6fff8; border-color: #c3e6cb !important;">
                            <h5 class="text-success mb-2"><i class="fas fa-check-double mr-2"></i> Kết quả sau khi thực hiện</h5>
                            <p class="mb-0 text-dark" style="font-size: 14px;"><?= htmlspecialchars($article['result']) ?></p>
                        </div>

                        <!-- Warnings & Troubleshooting -->
                        <?php if (!empty($article['warnings'])) { ?>
                            <div class="alert alert-warning border-0 shadow-sm mb-4">
                                <h6 class="mb-2"><i class="fas fa-exclamation-triangle mr-2"></i> LƯU Ý QUAN TRỌNG:</h6>
                                <div style="font-size: 13.5px;"><?= htmlspecialchars($article['warnings']) ?></div>
                            </div>
                        <?php } ?>

                        <?php if (!empty($article['troubleshooting'])) { ?>
                            <div class="p-3 rounded border mb-4" style="background-color: #f8f9fa; border-left: 4px solid #6c757d !important;">
                                <h6 class="text-secondary mb-2"><i class="fas fa-wrench mr-2"></i> Xử lý lỗi & Ngộ nhận thường gặp</h6>
                                <div class="text-dark" style="font-size: 13.5px;"><?= htmlspecialchars($article['troubleshooting']) ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white p-3">
                        <h6 class="m-0"><i class="fas fa-compass mr-2"></i> DANH MỤC</h6>
                    </div>
                    <div class="card-body p-3">
                        <a href="index.php" class="btn btn-outline-primary btn-sm btn-block text-left mb-2">
                            <i class="fas fa-th-large mr-2"></i> Tất cả bài viết
                        </a>
                        <?php foreach ($categories as $cId => $c) { ?>
                            <a href="index.php?category=<?= $cId ?>" class="btn btn-sm btn-block text-left mb-1 <?= ($categoryInfo && $categoryInfo['id'] === $cId) ? 'btn-primary' : 'btn-light text-dark' ?>">
                                <i class="<?= $c['icon'] ?> mr-1"></i> <?= $c['name'] ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <?php if (!empty($relatedArticles)) { ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white p-3 border-bottom">
                            <h6 class="m-0 text-dark"><i class="fas fa-link text-info mr-2"></i> HƯỚNG DẪN LIÊN QUAN</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($relatedArticles as $rArt) { ?>
                                    <li class="mb-3 pb-2 border-bottom">
                                        <a href="index.php?act=detail&id=<?= $rArt['id'] ?>" class="text-dark text-decoration-none d-block" style="font-size: 13px;">
                                            <i class="far fa-file-alt text-primary mr-1"></i> <?= $rArt['title'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    <?php } else { ?>
        <!-- Hero Search Box -->
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
            <div class="card-body p-5 text-center text-white">
                <h2 class="mb-2">HỆ THỐNG HƯỚNG DẪN SỬ DỤNG ADMIN KHOEPRO</h2>
                <p class="mb-4 text-white-50">Tra cứu nhanh toàn bộ quy trình vận hành, quản lý sản phẩm, AI Content, Video và TikTok Affiliate</p>
                <form action="index.php" method="GET" class="d-flex justify-content-center">
                    <div class="input-group" style="max-width: 650px;">
                        <input type="text" name="keyword" class="form-control form-control-lg" placeholder="Nhập từ khóa cần tìm (VD: thêm sản phẩm, xuất bản tiktok, tạo hook ai...)" value="<?= htmlspecialchars($keyword ?? '') ?>" style="border-radius: 8px 0 0 8px; font-size: 15px;">
                        <div class="input-group-append">
                            <button class="btn btn-warning px-4 font-weight-bold" type="submit" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-search mr-1"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Category Pills -->
        <div class="mb-4">
            <h5 class="mb-3 text-secondary"><i class="fas fa-layer-group mr-2"></i> DANH MỤC CHUYÊN ĐỀ</h5>
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <a href="index.php" class="btn btn-block p-3 text-left shadow-sm <?= empty($category) && empty($keyword) ? 'btn-primary' : 'btn-outline-primary bg-white' ?>" style="border-radius: 8px;">
                        <i class="fas fa-th-large mr-2"></i> Tất cả hướng dẫn
                    </a>
                </div>
                <?php foreach ($categories as $catId => $cat) { ?>
                    <div class="col-6 col-md-3 mb-3">
                        <a href="index.php?category=<?= $catId ?>" class="btn btn-block p-3 text-left shadow-sm <?= ($category === $catId) ? 'btn-primary' : 'btn-outline-secondary bg-white text-dark' ?>" style="border-radius: 8px;">
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
                        <h4 class="text-secondary">Không tìm thấy bài hướng dẫn phù hợp</h4>
                        <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc duyệt theo danh mục ở trên.</p>
                        <a href="index.php" class="btn btn-primary mt-2">Xem tất cả hướng dẫn</a>
                    </div>
                </div>
            <?php } else { ?>
                <?php foreach ($articles as $art) { 
                    $catMeta = $categories[$art['category_id']] ?? array('name' => 'Chung', 'badge' => 'badge-secondary');
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 hover-card" style="border-top: 3px solid #007bff;">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge <?= $catMeta['badge'] ?> px-2 py-1"><?= $catMeta['name'] ?></span>
                                    <small class="text-muted"><i class="far fa-clock mr-1"></i> <?= count($art['steps']) ?> bước</small>
                                </div>
                                <h5 class="card-title mb-2">
                                    <a href="index.php?act=detail&id=<?= $art['id'] ?>" class="text-dark text-decoration-none">
                                        <?= $art['title'] ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted flex-grow-1 mb-3" style="font-size: 13px; line-height: 1.5;">
                                    <?= $art['summary'] ?>
                                </p>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= mb_substr($art['menu_path'], 0, 30) ?>...</small>
                                    <a href="index.php?act=detail&id=<?= $art['id'] ?>" class="btn btn-sm btn-primary px-3">
                                        Xem chi tiết <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<footer class="bg-white border-top py-4 text-center text-muted small mt-5">
    <div class="container">
        &copy; <?= date('Y') ?> KhoePro Marketing Operations Suite - Tài liệu Hướng Dẫn Sử Dụng Nội Bộ
    </div>
</footer>

</body>
</html>
