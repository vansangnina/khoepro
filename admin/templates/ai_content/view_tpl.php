<?php
$linkMan = "index.php?com=ai_content&act=man";
$linkApprove = "index.php?com=ai_content&act=approve&id=" . $item['id'];
$linkDiff = "index.php?com=ai_content&act=diff&id=" . $item['id'];
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>">Kho nội dung AI</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($item['title']) ?> (v<?= $item['version'] ?>)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <div class="card card-primary card-outline shadow-sm text-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-alt mr-1"></i> Chi tiết Nội dung: <?= htmlspecialchars($item['title']) ?>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary mr-2">Version <?= $item['version'] ?></span>
                        <?php if ($item['is_active']) { ?>
                            <span class="badge badge-success mr-2"><i class="fas fa-star text-warning"></i> ACTIVE VERSION</span>
                        <?php } ?>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Specific Structured Renderers -->
                    <?php if ($item['content_type'] === 'tiktok_hooks' && !empty($structuredData['hooks'])) { ?>
                        <h5 class="text-danger font-weight-bold mb-3"><i class="fab fa-tiktok mr-1"></i> 7 Biến Thể TikTok Strategic Hooks</h5>
                        <div class="row">
                            <?php foreach ($structuredData['hooks'] as $idx => $h) { ?>
                                <div class="col-12 mb-3">
                                    <div class="card border border-danger shadow-none">
                                        <div class="card-header bg-light py-2">
                                            <span class="badge badge-danger mr-2">#<?= $idx + 1 ?> <?= strtoupper($h['hook_type'] ?? 'HOOK') ?></span>
                                            <strong class="text-dark"><?= htmlspecialchars($h['headline'] ?? '') ?></strong>
                                        </div>
                                        <div class="card-body py-2">
                                            <p class="mb-1 text-sm font-italic">"<?= htmlspecialchars($h['hook_script'] ?? '') ?>"</p>
                                            <div class="text-xs text-muted">
                                                <i class="fas fa-video mr-1 text-info"></i> <strong>Visual:</strong> <?= htmlspecialchars($h['visual_action'] ?? '') ?>
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-heart mr-1 text-danger"></i> <strong>Emotion:</strong> <?= htmlspecialchars($h['target_emotion'] ?? '') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                    <?php } elseif ($item['content_type'] === 'tiktok_script') { ?>
                        <div class="alert alert-dark p-3 mb-3">
                            <h5 class="font-weight-bold mb-1"><i class="fas fa-video mr-1"></i> <?= htmlspecialchars($structuredData['title'] ?? $item['title']) ?></h5>
                            <div class="text-sm">
                                <strong>Thời lượng:</strong> <?= $structuredData['target_duration'] ?? $item['target_duration'] ?>s | 
                                <strong>Hook:</strong> "<?= htmlspecialchars($structuredData['hook_text'] ?? '') ?>"
                            </div>
                        </div>

                        <div class="card card-outline card-secondary mb-3">
                            <div class="card-header py-2 font-weight-bold">Toàn văn Lời thoại (Voiceover Script)</div>
                            <div class="card-body bg-light p-3">
                                <p class="lead text-sm font-italic mb-0"><?= nl2br(htmlspecialchars($structuredData['full_script'] ?? $item['content_text'])) ?></p>
                            </div>
                        </div>

                        <?php if (!empty($structuredData['shot_plan'])) { ?>
                            <h5 class="font-weight-bold mb-3"><i class="fas fa-film mr-1 text-primary"></i> Kế hoạch Phân cảnh Video (Shot Plan - Bridge to Phase 06)</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-sm">
                                    <thead class="bg-primary text-white text-center">
                                        <tr style="font-size: 13px;">
                                            <th style="width: 60px;">Cảnh</th>
                                            <th style="width: 70px;">Thời lượng</th>
                                            <th style="width: 250px;">Chỉ dẫn Hình ảnh (Visual)</th>
                                            <th>Lời thoại (Voiceover) & Text Overlay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($structuredData['shot_plan'] as $sp) { ?>
                                            <tr>
                                                <td class="text-center font-weight-bold">#<?= $sp['scene_number'] ?></td>
                                                <td class="text-center"><span class="badge badge-info"><?= $sp['duration'] ?>s</span></td>
                                                <td class="text-wrap">
                                                    <div class="font-weight-bold text-dark"><?= htmlspecialchars($sp['visual_instruction']) ?></div>
                                                    <div class="text-xs text-muted mt-1"><i class="fas fa-camera mr-1"></i> <?= htmlspecialchars($sp['asset_requirement'] ?? '') ?></div>
                                                </td>
                                                <td class="text-wrap">
                                                    <div class="mb-1 text-primary font-italic">"<?= htmlspecialchars($sp['voiceover']) ?>"</div>
                                                    <?php if (!empty($sp['on_screen_text'])) { ?>
                                                        <span class="badge badge-warning text-xs"><i class="fas fa-font mr-1"></i> Text: <?= htmlspecialchars($sp['on_screen_text']) ?></span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>

                    <?php } else { ?>
                        <!-- Formatted Content Markdown/HTML -->
                        <div class="p-3 bg-light rounded border text-sm" style="line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($item['content_text'])) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Meta Column -->
        <div class="col-lg-4">
            <!-- Quality Gate Status Card -->
            <div class="card card-outline <?= ($qualityCheck['passed'] ?? true) ? 'card-success' : 'card-danger' ?> shadow-sm text-sm mb-3">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-shield-alt mr-1"></i> Kiểm Định Chất Lượng (Quality Gate)
                </div>
                <div class="card-body p-3">
                    <?php if ($qualityCheck['passed'] ?? true) { ?>
                        <div class="text-success font-weight-bold mb-2">
                            <i class="fas fa-check-circle fa-lg mr-1"></i> ĐẠT TIÊU CHUẨN KIỂM ĐỊNH
                        </div>
                        <ul class="text-xs text-muted pl-3 mb-0">
                            <li>Không bịa đặt số liệu lượt bán/rating sàn.</li>
                            <li>Không tự xưng trải nghiệm cá nhân ("Tôi đã test...").</li>
                            <li>Không tạo review/testimonial giả mạo.</li>
                            <li>Không cam kết y khoa/chữa bệnh trái quy định.</li>
                        </ul>
                    <?php } else { ?>
                        <div class="text-danger font-weight-bold mb-2">
                            <i class="fas fa-exclamation-triangle fa-lg mr-1"></i> CẢNH BÁO VI PHẠM NGUYÊN TẮC:
                        </div>
                        <ul class="text-xs text-danger pl-3 mb-0">
                            <?php foreach ($qualityCheck['flags'] as $flag) { ?>
                                <li><?= htmlspecialchars($flag) ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>

            <!-- Approval Actions Card -->
            <div class="card card-outline card-info shadow-sm text-sm mb-3">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-tasks mr-1"></i> Quyết định Kiểm duyệt (Human Gate)
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted text-xs">Trạng thái hiện tại:</label>
                        <div>
                            <?php if ($item['status'] === 'REVIEW_REQUIRED') { ?>
                                <span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i> CHỜ BIÊN TẬP DUYỆT</span>
                            <?php } elseif ($item['status'] === 'APPROVED') { ?>
                                <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> ĐÃ DUYỆT (APPROVED)</span>
                            <?php } elseif ($item['status'] === 'APPLIED') { ?>
                                <span class="badge badge-purple p-2"><i class="fas fa-check-double mr-1"></i> ĐÃ ÁP DỤNG VÀO SẢN PHẨM</span>
                            <?php } elseif ($item['status'] === 'REJECTED') { ?>
                                <span class="badge badge-danger p-2"><i class="fas fa-times-circle mr-1"></i> BỊ TỪ CHỐI</span>
                                <?php if (!empty($item['reject_reason'])) { ?>
                                    <div class="text-xs text-danger mt-1">Lý do: <?= htmlspecialchars($item['reject_reason']) ?></div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="btn-group-vertical w-100">
                        <?php if ($item['status'] !== 'APPROVED' && $item['status'] !== 'APPLIED') { ?>
                            <a href="<?= $linkApprove ?>" class="btn btn-success mb-2" onclick="return confirm('Xác nhận PHÊ DUYỆT phiên bản nội dung này và kích hoạt làm Active Version?')">
                                <i class="fas fa-check mr-1"></i> Phê Duyệt Nội Dung (Approve)
                            </a>
                        <?php } ?>

                        <?php if ($item['status'] === 'APPROVED') { ?>
                            <a href="<?= $linkDiff ?>" class="btn btn-purple mb-2">
                                <i class="fas fa-share-square mr-1"></i> So Sánh & Áp Dụng vào Sản Phẩm
                            </a>
                        <?php } ?>

                        <button type="button" class="btn btn-outline-danger" data-toggle="collapse" data-target="#rejectCollapse">
                            <i class="fas fa-times mr-1"></i> Từ chối nội dung...
                        </button>
                    </div>

                    <div class="collapse mt-3" id="rejectCollapse">
                        <form method="post" action="index.php?com=ai_content&act=reject">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <div class="form-group mb-2">
                                <label class="text-xs">Lý do từ chối:</label>
                                <textarea name="reject_reason" class="form-control text-xs" rows="2" placeholder="Nhập lý do..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block btn-sm">Xác nhận Từ Chối</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Version History Card -->
            <div class="card card-outline card-secondary shadow-sm text-sm mb-3">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-history mr-1"></i> Lịch Sử Phiên Bản (Versioning)
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush text-xs">
                        <?php foreach ($versionHistory as $vh) { ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center <?= ($vh['id'] == $item['id']) ? 'bg-light font-weight-bold' : '' ?>">
                                <div>
                                    <a href="index.php?com=ai_content&act=view&id=<?= $vh['id'] ?>" class="text-primary">
                                        Version <?= $vh['version'] ?>
                                    </a>
                                    <span class="text-muted ml-1">(<?= date('d/m H:i', $vh['date_created']) ?>)</span>
                                </div>
                                <div>
                                    <?php if ($vh['is_active']) { ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php } ?>
                                    <span class="badge badge-secondary"><?= $vh['status'] ?></span>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <!-- Product Context Card -->
            <div class="card card-outline card-secondary shadow-sm text-sm">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-box mr-1"></i> Thông Tin Sản Phẩm Gốc
                </div>
                <div class="card-body p-3">
                    <div class="font-weight-bold text-primary mb-1">
                        <a href="index.php?com=product&act=edit&type=san-pham&id=<?= $product['id'] ?>" target="_blank">
                            <?= htmlspecialchars($product['namevi']) ?>
                        </a>
                    </div>
                    <div class="text-xs text-muted mb-2">Mã SKU: <?= $product['code'] ?? 'N/A' ?></div>
                    <div class="text-xs mb-1"><strong>Giá bán:</strong> <?= number_format($product['sale_price'] ?: $product['regular_price']) ?> VND</div>
                    <div class="text-xs mb-1"><strong>Model AI:</strong> <?= htmlspecialchars($item['model']) ?> (<?= $item['provider'] ?>)</div>
                    <div class="text-xs"><strong>Prompt Version:</strong> <?= htmlspecialchars($item['prompt_version']) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
