<?php
$linkOverview  = "index.php?com=operations&act=overview";
$linkProviders = "index.php?com=operations&act=providers";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-plug mr-2 text-primary"></i>Nhà Cung cấp & Kết nối API (Providers Readiness)
                </h1>
                <small class="text-muted">Kiểm tra tính sẵn sàng của các nhà cung cấp bên ngoài mà KHÔNG phát sinh chi phí gọi API</small>
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
        <!-- Zero Cost Policy Notice -->
        <div class="alert alert-info shadow-sm border-0 mb-4">
            <h5><i class="icon fas fa-info-circle"></i> CHÍNH SÁCH KIỂM TRA KHÔNG PHÁT SINH CHI PHÍ (ZERO-COST HEALTH CHECK):</h5>
            Trang tổng quan này đối soát trực tiếp từ cấu hình tĩnh và lịch sử các request thật gần nhất. Tuyệt đối KHÔNG tự động gửi request gọi API tính phí (Runway, Luma, TTS, LLM) mỗi khi tải trang.
        </div>

        <!-- Providers Grid -->
        <div class="row">
            <?php foreach ($providersList as $p): ?>
                <?php
                $badge = 'secondary';
                if ($p['status'] === 'CONFIGURED' || $p['status'] === 'AVAILABLE') $badge = 'success';
                elseif ($p['status'] === 'NOT_CONFIGURED') $badge = 'warning text-dark';
                elseif ($p['status'] === 'UNAVAILABLE' || $p['status'] === 'AUTH_ERROR') $badge = 'danger';
                ?>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                            <span class="text-dark"><i class="fas fa-server mr-2 text-primary"></i><?=$p['name']?></span>
                            <span class="badge badge-<?=$badge?> px-2 py-1"><?=$p['status']?></span>
                        </div>
                        <div class="card-body p-3">
                            <div class="text-muted small mb-2"><?=$p['purpose']?></div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 130px;">Phân loại:</td>
                                    <td><code><?=$p['type']?></code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Trạng thái:</td>
                                    <td class="font-weight-bold text-dark"><?=$p['status_label']?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Thành công gần nhất:</td>
                                    <td>
                                        <?php if (!empty($p['last_success_at'])): ?>
                                            <?=date('H:i:s d/m/Y', $p['last_success_at'])?>
                                        <?php else: ?>
                                            <span class="text-muted font-italic">Chưa ghi nhận</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
